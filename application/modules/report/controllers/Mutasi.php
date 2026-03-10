<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mutasi extends Public_Controller {

    private $pathView = 'report/mutasi/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    'assets/report/mutasi/js/mutasi.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/mutasi/css/mutasi.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['gudang'] = $this->getGudang();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Mutasi';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGudang()
    {
        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gudang->count() > 0 ) {
            $data = $d_gudang->toArray();
        }

        return $data;
    }

    public function getData($gudang_asal, $gudang_tujuan, $start_date, $end_date)
    {
        $sql_gudang_asal = "";
        if ( !in_array('all', $gudang_asal) ) {
            $sql_gudang_asal = "and m.asal in ('".implode("', '", $gudang_asal)."')";
        }

        $sql_gudang_tujuan = "";
        if ( !in_array('all', $gudang_tujuan) ) {
            $sql_gudang_tujuan = "and m.tujuan in ('".implode("', '", $gudang_tujuan)."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select  
                m.kode_mutasi, 
                m.tgl_mutasi, 
                g_asal.nama as nama_gudang_asal, 
                g_tujuan.nama as nama_gudang_tujuan, 
                i.nama as nama_item,
                mi.jumlah,
                (isnull(sh.harga, 0) * mi.pengali) as harga,
                mi.satuan,
                gi.coa
            from mutasi_item mi
            right join
                mutasi m
                on
                    mi.mutasi_kode = m.kode_mutasi
            left join
                item i
                on
                    mi.item_kode = i.kode
            left join
                group_item gi
                on
                    i.group_kode = gi.kode
            left join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            left join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            left join
                (
                    select  
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        sum(st.jumlah) as jumlah,
                        cast(st.tbl_name as varchar(max)) as tbl_name
                    from stok_trans st
                    right join
                        stok s
                        on
                            st.id_header = s.id
                    group by
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        cast(st.tbl_name as varchar(max))
                ) st
                on
                    st.kode_trans = m.kode_mutasi and
                    st.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = st.id_header and
                    sh.item_kode = mi.item_kode
            where
                m.tgl_mutasi between '".$start_date."' and '".$end_date."'
                ".$sql_gudang_asal."
                ".$sql_gudang_tujuan."
            order by
                m.tgl_mutasi asc,
                m.kode_mutasi asc,
                i.nama asc
        ";
        $d_mutasi = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_mutasi->count() > 0 ) {
            $d_mutasi = $d_mutasi->toArray();

            foreach ($d_mutasi as $key => $value) {
                $data[ $value['tgl_mutasi'] ]['tgl_mutasi'] = $value['tgl_mutasi'];
                $data[ $value['tgl_mutasi'] ]['detail'][ $value['kode_mutasi'] ]['kode'] = $value['kode_mutasi'];
                $data[ $value['tgl_mutasi'] ]['detail'][ $value['kode_mutasi'] ]['detail'][] = $value;
            }
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $start_date = $params['start_date'].' 00:00:00';
            $end_date = $params['end_date'].' 23:59:59';
            $gudang_asal = $params['gudang_asal'];
            $gudang_tujuan = $params['gudang_tujuan'];

            $data = $this->getData($gudang_asal, $gudang_tujuan, $start_date, $end_date);

            $content_report['data'] = $data;
            $html_report = $this->load->view($this->pathView . 'list_report', $content_report, TRUE);

            $list_html = array(
                'list_report' => $html_report
            );

            $this->result['status'] = 1;
            $this->result['content'] = $list_html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function excryptParamsExportExcel()
    {
        $params = $this->input->post('params');

        try {
            $paramsEncrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = array('data' => $paramsEncrypt);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportExcel($_params) {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $start_date = $_data_params['start_date'].' 00:00:00';
        $end_date = $_data_params['end_date'].' 23:59:59';
        $gudang_asal = $_data_params['gudang_asal'];
        $gudang_tujuan = $_data_params['gudang_tujuan'];

        $detail = $this->getData($gudang_asal, $gudang_tujuan, $start_date, $end_date);

        // cetak_r( $detail, 1 );

        $data = array(
            'gudang_asal' => $gudang_asal,
            'gudang_tujuan' => $gudang_tujuan,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'detail' => $detail
        );

        $filename = 'export-mutasi-barang-';
        $filename = $filename.str_replace('-', '', substr($start_date, 0, 10)).'_'.str_replace('-', '', substr($end_date, 0, 10));

        $arr_column = null;

        $idx = 0;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'LAPORAN MUTASI BARANG', 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none')
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => '', 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Gudang Asal', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.implode(", ", $data['gudang_asal']), 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Gudang Tujuan', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.implode(", ", $data['gudang_tujuan']), 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'PERIODE', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.str_replace('-', '/', substr($start_date, 0, 10)).' - '.str_replace('-', '/', substr($end_date, 0, 10)), 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Tanggal', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'B' => array('value' => 'Kode Mutasi', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'C' => array('value' => 'Asal', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'D' => array('value' => 'Tujuan', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'E' => array('value' => 'Nama Item', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'F' => array('value' => 'COA SAP', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'G' => array('value' => 'Satuan', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'H' => array('value' => 'Jumlah', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'I' => array('value' => 'Harga (Rp.)', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'J' => array('value' => 'Nilai', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
        );
        $idx++;

        $start_row_header = $idx;

        $arr_header = array('A','B','C','D','E','F','G','H','I','J');
        if ( !empty($data['detail']) && count($data['detail']) > 0 ) {
            $grand_total = 0;
            foreach ($data['detail'] as $k_tanggal => $v_tanggal) {
                $total_per_tanggal = 0;
                foreach ($v_tanggal['detail'] as $k_kode => $v_kode) {
                    $total_per_kode = 0;
                    foreach ($v_kode['detail'] as $k_det => $v_det) {
                        $total = $v_det['jumlah'] * $v_det['harga'];
                        $grand_total += $total;
                        $total_per_tanggal += $total;
                        $total_per_kode += $total;

                        $arr_column[ $idx ] = array(
                            'A' => array('value' => $v_det['tgl_mutasi'], 'data_type' => 'date', 'align' => 'left', 'border' => 'border'),
                            'B' => array('value' => $v_det['kode_mutasi'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'C' => array('value' => $v_det['nama_gudang_asal'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'D' => array('value' => $v_det['nama_gudang_tujuan'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'E' => array('value' => $v_det['nama_item'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'F' => array('value' => $v_det['coa'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'G' => array('value' => $v_det['satuan'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'H' => array('value' => $v_det['jumlah'], 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                            'I' => array('value' => $v_det['harga'], 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                            'J' => array('value' => $total, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                        );
                        $idx++;
                    }

                    $arr_column[ $idx ] = array(
                        'I' => array('value' => 'TOTAL', 'data_type' => 'string', 'align' => 'left', 'border' => 'border', 'text_style' => 'bold', 'colspan' => array('A','I')),
                        'J' => array('value' => $total_per_kode, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
                    );
                    $idx++;
                }

                $arr_column[ $idx ] = array(
                    'I' => array('value' => 'TOTAL PER TANGGAL - '.tglIndonesia($v_det['tgl_mutasi'], '-', ' '), 'data_type' => 'string', 'align' => 'left', 'border' => 'border', 'text_style' => 'bold', 'colspan' => array('A','I')),
                    'J' => array('value' => $total_per_tanggal, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
                );
                $idx++;
            }

            $arr_column[ $idx ] = array(
                'I' => array('value' => 'TOTAL', 'data_type' => 'string', 'align' => 'left', 'border' => 'border', 'text_style' => 'bold', 'colspan' => array('A','I')),
                'J' => array('value' => $grand_total, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
            );
            $idx++;
        } else {
            $arr_column[ $idx ] = array(
                'J' => array('value' => 'Data tidak ditemukan.', 'data_type' => 'string', 'align' => 'left', 'border' => 'border', 'colspan' => array('A','J'), 'text_style' => 'bold')
            );
        }

        Modules::run( 'base/ExportExcel/exportExcelUsingSpreadSheet', $filename, $arr_header, $arr_column, $start_row_header, 0 );

        $this->load->helper('download');
        force_download('export_excel/'.$filename.'.xlsx', NULL);
    }

    public function exportExcelOld($_params)
    {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $start_date = $_data_params['start_date'].' 00:00:00';
        $end_date = $_data_params['end_date'].' 23:59:59';
        $gudang_asal = $_data_params['gudang_asal'];
        $gudang_tujuan = $_data_params['gudang_tujuan'];

        $detail = $this->getData($gudang_asal, $gudang_tujuan, $start_date, $end_date);

        // cetak_r( $detail, 1 );

        $data = array(
            'gudang_asal' => $gudang_asal,
            'gudang_tujuan' => $gudang_tujuan,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'detail' => $detail
        );

        $content['data'] = $data;
        $res_view_html = $this->load->view('report/mutasi/export_excel', $content, true);

        $filename = 'export-mutasi-barang-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}

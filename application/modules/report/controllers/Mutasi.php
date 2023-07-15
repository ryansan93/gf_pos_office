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
                item i
                on
                    mi.item_kode = i.kode
            right join
                group_item gi
                on
                    i.group_kode = gi.kode
            right join
                mutasi m
                on
                    mi.mutasi_kode = m.kode_mutasi
            right join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            right join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            left join
                (
                    select 
                        s.id, 
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        st.jumlah,
                        st.tbl_name
                    from stok_trans st
                    right join
                        stok s
                        on
                            st.id_header = s.id
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

    public function exportExcel($_params)
    {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $start_date = $_data_params['start_date'].' 00:00:00';
        $end_date = $_data_params['end_date'].' 23:59:59';
        $gudang_asal = $_data_params['gudang_asal'];
        $gudang_tujuan = $_data_params['gudang_tujuan'];

        $detail = $this->getData($gudang_asal, $gudang_tujuan, $start_date, $end_date);

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

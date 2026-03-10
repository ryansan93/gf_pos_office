<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan extends Public_Controller {

    private $pathView = 'report/penerimaan/';
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
                    'assets/report/penerimaan/js/penerimaan.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/penerimaan/css/penerimaan.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['gudang'] = $this->getGudang();
            $content['supplier'] = $this->getSupplier();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Penerimaan';
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

    public function getSupplier()
    {
        $m_supl = new \Model\Storage\Supplier_model();
        $d_supl = $m_supl->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_supl->count() > 0 ) {
            $data = $d_supl->toArray();
        }

        return $data;
    }

    public function getData($gudang, $supplier, $start_date, $end_date)
    {
        $sql_gudang = "";
        if ( !in_array('all', $gudang) ) {
            $sql_gudang = "and t.gudang_kode in ('".implode("', '", $gudang)."')";
        }

        $sql_supplier = "";
        if ( !in_array('all', $supplier) ) {
            $sql_supplier = "and t.supplier_kode in ('".implode("', '", $supplier)."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                t.po_no, 
                t.kode_terima, 
                t.tgl_terima, 
                t.supplier,
                supl.npwp as npwp_supplier,
                g.nama as nama_gudang, 
                i.nama as nama_item,
                ti.jumlah_terima,
                ti.harga,
                ti.satuan,
                gi.coa
            from terima_item ti
            left join
                item i
                on
                    ti.item_kode = i.kode
            left join
                group_item gi
                on
                    i.group_kode = gi.kode
            left join
                terima t
                on
                    ti.terima_kode = t.kode_terima
            left join
                gudang g
                on
                    t.gudang_kode = g.kode_gudang
            left join
                supplier supl
                on
                    t.supplier_kode = supl.kode
            where
                t.tgl_terima between '".$start_date."' and '".$end_date."'
                ".$sql_gudang."
                ".$sql_supplier."
            order by
                t.tgl_terima asc,
                t.kode_terima asc,
                i.nama asc
        ";
        $d_terima = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_terima->count() > 0 ) {
            $d_terima = $d_terima->toArray();

            foreach ($d_terima as $key => $value) {
                $data[ $value['tgl_terima'] ]['tgl_terima'] = $value['tgl_terima'];
                $data[ $value['tgl_terima'] ]['detail'][ $value['kode_terima'] ]['kode'] = $value['kode_terima'];
                $data[ $value['tgl_terima'] ]['detail'][ $value['kode_terima'] ]['detail'][] = $value;
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
            $gudang = $params['gudang'];
            $supplier = $params['supplier'];

            $data = $this->getData( $gudang, $supplier, $start_date, $end_date );

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
        $gudang = $_data_params['gudang'];
        $supplier = $_data_params['supplier'];

        $detail = $this->getData( $gudang, $supplier, $start_date, $end_date );

        $data = array(
            'gudang' => $gudang,
            'supplier' => $supplier,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'detail' => $detail
        );

        $filename = 'export-penerimaan-barang-';
        $filename = $filename.str_replace('-', '', substr($start_date, 0, 10)).'_'.str_replace('-', '', substr($end_date, 0, 10));

        $arr_column = null;

        $idx = 0;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'LAPORAN PENERIMAAN BARANG', 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none')
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => '', 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Supplier', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.implode(", ", $data['supplier']), 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Gudang', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.implode(", ", $data['gudang']), 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'PERIODE', 'data_type' => 'string', 'align' => 'left', 'text_style' => 'bold', 'border' => 'none'),
            'F' => array('value' => ': '.str_replace('-', '/', substr($start_date, 0, 10)).' - '.str_replace('-', '/', substr($end_date, 0, 10)), 'data_type' => 'string', 'colspan' => array('A','F'), 'align' => 'left', 'text_style' => 'bold', 'border' => 'none', 'colspan' => array('B','F')),
        );
        $idx++;
        $arr_column[ $idx ] = array(
            'A' => array('value' => 'Tanggal', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'B' => array('value' => 'Kode Terima', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'C' => array('value' => 'Kode PO', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'D' => array('value' => 'Supplier', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'E' => array('value' => 'NPWP', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'F' => array('value' => 'Gudang', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'G' => array('value' => 'Nama Item', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'H' => array('value' => 'COA SAP', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'I' => array('value' => 'Satuan', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'J' => array('value' => 'Jumlah', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'K' => array('value' => 'Harga (Rp.)', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
            'L' => array('value' => 'Nilai', 'data_type' => 'string', 'align' => 'center', 'text_style' => 'bold', 'border' => 'border'),
        );
        $idx++;

        $start_row_header = $idx;

        $arr_header = array('A','B','C','D','E','F','G','H','I','J','K','L');
        if ( !empty($data['detail']) && count($data['detail']) > 0 ) {
            $grand_total = 0;
            foreach ($data['detail'] as $k_tanggal => $v_tanggal) {
                $total_per_tanggal = 0;
                foreach ($v_tanggal['detail'] as $k_kode => $v_kode) {
                    $total_per_kode = 0;
                    foreach ($v_kode['detail'] as $k_det => $v_det) {
                        $total = $v_det['jumlah_terima'] * $v_det['harga'];
                        $grand_total += $total;
                        $total_per_tanggal += $total;
                        $total_per_kode += $total;

                        $arr_column[ $idx ] = array(
                            'A' => array('value' => $v_det['tgl_terima'], 'data_type' => 'date', 'align' => 'left', 'border' => 'border'),
                            'B' => array('value' => $v_det['kode_terima'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'C' => array('value' => $v_det['po_no'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'D' => array('value' => $v_det['supplier'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'E' => array('value' => $v_det['npwp_supplier'], 'data_type' => 'string', 'align' => 'right', 'border' => 'border'),
                            'F' => array('value' => $v_det['nama_gudang'], 'data_type' => 'string', 'align' => 'right', 'border' => 'border'),
                            'G' => array('value' => $v_det['nama_item'], 'data_type' => 'string', 'align' => 'left', 'border' => 'border'),
                            'H' => array('value' => $v_det['coa'], 'data_type' => 'string', 'align' => 'right', 'border' => 'border'),
                            'I' => array('value' => $v_det['satuan'], 'data_type' => 'string', 'align' => 'right', 'border' => 'border'),
                            'J' => array('value' => $v_det['jumlah_terima'], 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                            'K' => array('value' => $v_det['harga'], 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                            'L' => array('value' => $total, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border'),
                        );
                        $idx++;
                    }

                    $arr_column[ $idx ] = array(
                        'K' => array('value' => 'TOTAL', 'data_type' => 'string', 'align' => 'right', 'border' => 'border', 'colspan' => array('A','K'), 'text_style' => 'bold'),
                        'L' => array('value' => $total_per_kode, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
                    );
                    $idx++;
                }

                $arr_column[ $idx ] = array(
                    'K' => array('value' => 'TOTAL PER TANGGAL - '.tglIndonesia($v_det['tgl_terima'], '-', ' '), 'data_type' => 'string', 'align' => 'right', 'border' => 'border', 'colspan' => array('A','K'), 'text_style' => 'bold'),
                    'L' => array('value' => $total_per_tanggal, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
                );
                $idx++;
            }

            $arr_column[ $idx ] = array(
                'K' => array('value' => 'GRAND TOTAL', 'data_type' => 'string', 'align' => 'right', 'border' => 'border', 'colspan' => array('A','K'), 'text_style' => 'bold'),
                'L' => array('value' => $grand_total, 'data_type' => 'decimal2', 'align' => 'right', 'border' => 'border', 'text_style' => 'bold'),
            );
            $idx++;
        } else {
            $arr_column[ $idx ] = array(
                'L' => array('value' => 'Data tidak ditemukan.', 'data_type' => 'string', 'align' => 'left', 'border' => 'border', 'colspan' => array('A','L'), 'text_style' => 'bold')
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
        $gudang = $_data_params['gudang'];
        $supplier = $_data_params['supplier'];

        $detail = $this->getData( $gudang, $supplier, $start_date, $end_date );

        $data = array(
            'gudang' => $gudang,
            'supplier' => $supplier,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'detail' => $detail
        );

        $content['data'] = $data;
        $res_view_html = $this->load->view('report/penerimaan/export_excel', $content, true);

        $filename = 'export-penerimaan-barang-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}

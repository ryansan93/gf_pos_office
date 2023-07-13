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
                g.nama as nama_gudang, 
                i.nama as nama_item,
                ti.jumlah_terima,
                ti.harga,
                ti.satuan,
                gi.coa
            from terima_item ti
            right join
                item i
                on
                    ti.item_kode = i.kode
            right join
                group_item gi
                on
                    i.group_kode = gi.kode
            right join
                terima t
                on
                    ti.terima_kode = t.kode_terima
            right join
                gudang g
                on
                    t.gudang_kode = g.kode_gudang
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

    public function exportExcel($_params)
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

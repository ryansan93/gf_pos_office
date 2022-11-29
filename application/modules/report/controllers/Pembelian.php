<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends Public_Controller {

    private $pathView = 'report/pembelian/';
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
                    'assets/report/pembelian/js/pembelian.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/pembelian/css/pembelian.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['branch'] = $this->getBranch();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Pembelian';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $start_date = $params['start_date'].' 00:00:00';
            $end_date = $params['end_date'].' 23:59:59';
            $branch = $params['branch'];

            $m_beli = new \Model\Storage\Beli_model();
            $d_beli = $m_beli->whereBetween('tgl_beli', [$start_date, $end_date])->where('branch_kode', $branch)->with(['supplier', 'branch', 'detail'])->orderBy('tgl_beli', 'asc')->get();

            $data = null;
            if ( $d_beli->count() > 0 ) {
                $data = $d_beli->toArray();
            }

            $mappingDataReport = $this->mappingDataReport( $data );

            // cetak_r( $mappingDataReport, 1 );

            $content_report['data'] = $mappingDataReport;
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

    public function mappingDataReport($_data)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', ' ', substr($v_data['tgl_beli'], 0, 10));
                $key_beli = $v_data['kode_beli'];
                $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_beli'], 0, 10);

                foreach ($v_data['detail'] as $k_det => $v_det) {
                    $key_detail = $v_det['beli_kode'].' | '.$v_det['item_kode'];
                    $key_group = $v_det['item']['group']['kode'];

                    $data[ $key_tanggal ]['group_item'][ $key_group ]['kode'] = $v_det['item']['group']['kode'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['nama'] = $v_det['item']['group']['nama'];

                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['kode_beli'] = $v_data['kode_beli'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['supplier'] = $v_data['supplier']['nama'];

                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['detail'][ $key_detail ]['nama_item'] = $v_det['item']['nama'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['detail'][ $key_detail ]['satuan'] = $v_det['item']['satuan'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['detail'][ $key_detail ]['harga'] = $v_det['harga'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['detail'][ $key_detail ]['jumlah'] = $v_det['jumlah'];
                    $data[ $key_tanggal ]['group_item'][ $key_group ]['beli'][ $key_beli ]['detail'][ $key_detail ]['total'] = $v_det['total'];
                }
            }
        }

        return $data;
    }
}

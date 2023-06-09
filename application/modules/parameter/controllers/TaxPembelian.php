<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TaxPembelian extends Public_Controller {

    private $pathView = 'parameter/tax_pembelian/';
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
    public function index($segment=0)
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                "assets/jquery/list.min.js",
                "assets/parameter/tax_pembelian/js/tax-pembelian.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/tax_pembelian/css/tax-pembelian.css",
            ));

            $data = $this->includes;

            $m_tp = new \Model\Storage\TaxPembelian_model();
            $d_tp = $m_tp->where('mstatus', 1)->orderBy('tgl_berlaku', 'desc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_tp;
            $content['title_panel'] = 'Master Tax Pembelian';

            // Load Indexx
            $data['title_menu'] = 'Master Tax Pembelian';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function modalAddForm()
    {
        $content = null;
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_tp = new \Model\Storage\TaxPembelian_model();
            $d_tp = $m_tp->where('mstatus', 1)->orderBy('id', 'desc')->first();
            if ( $d_tp ) {
                $m_tp = new \Model\Storage\TaxPembelian_model();
                $m_tp->where('id', $d_tp->id)->update(
                    array(
                        'mstatus' => 0
                    )
                );
            }

            $m_tp = new \Model\Storage\TaxPembelian_model();
            $m_tp->tgl_berlaku = $params['tgl_berlaku'];
            $m_tp->nilai = $params['nilai'];
            $m_tp->mstatus = 1;
            $m_tp->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_tp, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id_tp = $this->input->post('id_tp');

        try {
            $m_tp = new \Model\Storage\TaxPembelian_model();
            $m_tp->where('id', $id_tp)->update(
                array(
                    'mstatus' => 0
                )
            );
            $d_tp = $m_tp->where('id', $id_tp)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_tp, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
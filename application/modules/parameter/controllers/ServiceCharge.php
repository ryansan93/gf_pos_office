<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceCharge extends Public_Controller {

    private $pathView = 'parameter/service_charge/';
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
                "assets/parameter/service_charge/js/service-charge.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/service_charge/css/service-charge.css",
            ));

            $data = $this->includes;

            $m_sc = new \Model\Storage\ServiceCharge_model();
            $d_sc = $m_sc->where('mstatus', 1)->with(['branch'])->orderBy('tgl_berlaku', 'desc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_sc;
            $content['title_panel'] = 'Master Service Charge';

            // Load Indexx
            $data['title_menu'] = 'Master Service Charge';
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

    public function modalAddForm()
    {
        $content['branch'] = $this->getBranch();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_sc = new \Model\Storage\ServiceCharge_model();
            $m_sc->branch_kode = $params['branch_kode'];
            $m_sc->tgl_berlaku = $params['tgl_berlaku'];
            $m_sc->nilai = $params['nilai'];
            $m_sc->mstatus = 1;
            $m_sc->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_sc, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id_sc = $this->input->post('id_sc');

        try {
            $m_sc = new \Model\Storage\ServiceCharge_model();
            $m_sc->where('id', $id_sc)->update(
                array(
                    'mstatus' => 0
                )
            );
            $d_sc = $m_sc->where('id', $id_sc)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_sc, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
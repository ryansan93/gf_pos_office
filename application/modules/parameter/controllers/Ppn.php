<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ppn extends Public_Controller {

    private $pathView = 'parameter/ppn/';
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
                "assets/parameter/ppn/js/ppn.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/ppn/css/ppn.css",
            ));

            $data = $this->includes;

            $m_ppn = new \Model\Storage\Ppn_model();
            $d_ppn = $m_ppn->where('mstatus', 1)->with(['branch'])->orderBy('tgl_berlaku', 'desc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_ppn;
            $content['title_panel'] = 'Master PPN';

            // Load Indexx
            $data['title_menu'] = 'Master PPN';
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
            $m_ppn = new \Model\Storage\Ppn_model();
            $m_ppn->branch_kode = $params['branch_kode'];
            $m_ppn->tgl_berlaku = $params['tgl_berlaku'];
            $m_ppn->nilai = $params['nilai'];
            $m_ppn->mstatus = 1;
            $m_ppn->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_ppn, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id_ppn = $this->input->post('id_ppn');

        try {
            $m_ppn = new \Model\Storage\Ppn_model();
            $m_ppn->where('id', $id_ppn)->update(
                array(
                    'mstatus' => 0
                )
            );
            $d_ppn = $m_ppn->where('id', $id_ppn)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_ppn, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
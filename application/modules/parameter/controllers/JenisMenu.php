<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JenisMenu extends Public_Controller {

    private $pathView = 'parameter/jenis_menu/';
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
                "assets/parameter/jenis_menu/js/jenis-menu.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/jenis_menu/css/jenis-menu.css",
            ));

            $data = $this->includes;

            $m_jm = new \Model\Storage\JenisMenu_model();
            $d_jm = $m_jm->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_jm;
            $content['title_panel'] = 'Master Jenis Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Jenis Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function modalAddForm()
    {
        $html = $this->load->view($this->pathView . 'addForm', null, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_jm = new \Model\Storage\JenisMenu_model();

            $m_jm->id = $m_jm->getNextIdentity();
            $m_jm->nama = $params['nama'];
            $m_jm->status = 1;
            $m_jm->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_jm = new \Model\Storage\JenisMenu_model();
        $d_jm = $m_jm->where('id', $kode)->first()->toArray();

        $content['data'] = $d_jm;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_jm = new \Model\Storage\JenisMenu_model();
            $m_jm->where('id', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'status' => 1
                )
            );

            $d_jm = $m_jm->where('id', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $kode = $this->input->post('kode');

        try {
            $m_jm = new \Model\Storage\JenisMenu_model();
            $m_jm->where('id', $kode)->update(
                array(
                    'status' => 0
                )
            );

            $d_jm = $m_jm->where('id', $kode)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_jm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
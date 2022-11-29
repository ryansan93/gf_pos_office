<?php defined('BASEPATH') OR exit('No direct script access allowed');

class IndukMenu extends Public_Controller {

    private $pathView = 'parameter/induk_menu/';
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
                "assets/parameter/induk_menu/js/induk-menu.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/induk_menu/css/induk-menu.css",
            ));

            $data = $this->includes;

            $m_im = new \Model\Storage\IndukMenu_model();
            $d_im = $m_im->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_im;
            $content['title_panel'] = 'Master Induk Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Induk Menu';
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
            $m_im = new \Model\Storage\IndukMenu_model();
            $m_im->nama = $params['nama'];
            $m_im->status = 1;
            $m_im->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_im, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $id = $this->input->get('id');

        $m_im = new \Model\Storage\IndukMenu_model();
        $d_im = $m_im->where('id', $id)->first()->toArray();

        $content['data'] = $d_im;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_im = new \Model\Storage\IndukMenu_model();
            $m_im->where('id', $params['id'])->update(
                array(
                    'nama' => $params['nama'],
                    'status' => 1
                )
            );

            $d_im = $m_im->where('id', $params['id'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_im, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id = $this->input->post('id');

        try {
            $m_im = new \Model\Storage\IndukMenu_model();
            $m_im->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_im = $m_im->where('id', $id)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_im, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
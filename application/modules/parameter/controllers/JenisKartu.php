<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JenisKartu extends Public_Controller {

    private $pathView = 'parameter/jenis_kartu/';
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
                "assets/parameter/jenis_kartu/js/jenis-kartu.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/jenis_kartu/css/jenis-kartu.css",
            ));

            $data = $this->includes;

            $m_jk = new \Model\Storage\JenisKartu_model();
            $d_jk = $m_jk->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_jk;
            $content['title_panel'] = 'Master Jenis Kartu';

            // Load Indexx
            $data['title_menu'] = 'Master Jenis Kartu';
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
            $m_jk = new \Model\Storage\JenisKartu_model();

            $kode = $m_jk->getNextId();

            $m_jk->kode_jenis_kartu = $kode;
            $m_jk->nama = $params['nama'];
            $m_jk->status = $params['status'];
            $m_jk->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jk, $deskripsi_log );

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

        $m_jk = new \Model\Storage\JenisKartu_model();
        $d_jk = $m_jk->where('kode_jenis_kartu', $kode)->first()->toArray();

        $content['data'] = $d_jk;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_jk = new \Model\Storage\JenisKartu_model();
            $m_jk->where('kode_jenis_kartu', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'status' => $params['status']
                )
            );

            $d_jk = $m_jk->where('kode_jenis_kartu', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jk, $deskripsi_log );

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
            $m_jk = new \Model\Storage\JenisKartu_model();
            $d_jk = $m_jk->where('kode_jenis_kartu', $kode)->first();

            $m_jk->where('kode_jenis_kartu', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_jk, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JenisPesanan extends Public_Controller {

    private $pathView = 'parameter/jenis_pesanan/';
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
                "assets/parameter/jenis_pesanan/js/jenis-pesanan.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/jenis_pesanan/css/jenis-pesanan.css",
            ));

            $data = $this->includes;

            $m_jp = new \Model\Storage\JenisPesanan_model();
            $d_jp = $m_jp->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_jp;
            $content['title_panel'] = 'Master Jenis Pesanan';

            // Load Indexx
            $data['title_menu'] = 'Master Jenis Pesanan';
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
            $m_jp = new \Model\Storage\JenisPesanan_model();

            $kode = $m_jp->getNextId();

            $m_jp->kode = $kode;
            $m_jp->nama = $params['nama'];
            $m_jp->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jp, $deskripsi_log );

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

        $m_jp = new \Model\Storage\JenisPesanan_model();
        $d_jp = $m_jp->where('kode', $kode)->first()->toArray();

        $content['data'] = $d_jp;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_jp = new \Model\Storage\JenisPesanan_model();
            $m_jp->where('kode', $params['kode'])->update(
                array(
                    'nama' => $params['nama']
                )
            );

            $d_jp = $m_jp->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jp, $deskripsi_log );

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
            $m_jp = new \Model\Storage\JenisPesanan_model();
            $d_jp = $m_jp->where('kode', $kode)->first();

            $m_jp->where('kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_jp, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
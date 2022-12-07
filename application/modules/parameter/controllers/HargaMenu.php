<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HargaMenu extends Public_Controller {

    private $pathView = 'parameter/harga_menu/';
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
                "assets/select2/js/select2.min.js",
                "assets/parameter/harga_menu/js/harga-menu.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/harga_menu/css/harga-menu.css",
            ));

            $data = $this->includes;

            $m_hm = new \Model\Storage\HargaMenu_model();
            $d_hm = $m_hm->orderBy('tgl_mulai', 'desc')->with(['menu', 'jenis_pesanan'])->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_hm;
            $content['title_panel'] = 'Master Harga Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Harga Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getMenu()
    {
        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->orderBy('nama', 'asc')->with(['kategori'])->get();

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        return $data;
    }

    public function getJenisPesanan()
    {
        $m_jp = new \Model\Storage\JenisPesanan_model();
        $d_jp = $m_jp->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_jp->count() > 0 ) {
            $data = $d_jp->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['menu'] = $this->getMenu();
        $content['jenis_pesanan'] = $this->getJenisPesanan();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            foreach ($params['list_jenis_pesanan'] as $key => $value) {
                $m_hm = new \Model\Storage\HargaMenu_model();
                $m_hm->jenis_pesanan_kode = $value['jenis_pesanan'];
                $m_hm->menu_kode = $params['menu'];
                $m_hm->harga = $value['harga'];
                $m_hm->tgl_mulai = $params['tgl_berlaku'];
                $m_hm->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_hm, $deskripsi_log );
            }

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

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $m_hm = new \Model\Storage\HargaMenu_model();
            $d_hm = $m_hm->where('menu_kode', $params['menu'])
                         ->where('jenis_pesanan_kode', $params['jenis_pesanan'])
                         ->where('tgl_mulai', $params['tgl_berlaku'])
                         ->where('harga', $params['harga'])
                         ->first();

            $m_hm->where('menu_kode', $params['menu'])
                 ->where('jenis_pesanan_kode', $params['jenis_pesanan'])
                 ->where('tgl_mulai', $params['tgl_berlaku'])
                 ->where('harga', $params['harga'])
                 ->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_hm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
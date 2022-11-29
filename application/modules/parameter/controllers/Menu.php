<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Public_Controller {

    private $pathView = 'parameter/menu/';
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
                "assets/parameter/menu/js/menu.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/menu/css/menu.css",
            ));

            $data = $this->includes;

            $m_jp = new \Model\Storage\Menu_model();
            $d_jp = $m_jp->orderBy('nama', 'asc')->with(['kategori', 'induk_menu'])->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_jp;
            $content['title_panel'] = 'Master Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getDataIndukMenu()
    {
        $m_im = new \Model\Storage\IndukMenu_model();
        $d_im = $m_im->where('status', 1)->get();

        $data = null;
        if ( $d_im->count() ) {
            $data = $d_im->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $m_km = new \Model\Storage\KategoriMenu_model();
        $d_km = $m_km->where('status', 1)->orderBy('nama', 'asc')->get();

        $kategori = null;
        if ( $d_km->count() > 0 ) {
            $kategori = $d_km->toArray();
        }

        $content['kategori'] = $kategori;
        $content['induk_menu'] = $this->getDataIndukMenu();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_menu = new \Model\Storage\Menu_model();

            $kode = $m_menu->getNextId();

            $m_menu->kode_menu = $kode;
            $m_menu->nama = $params['nama'];
            $m_menu->deskripsi = isset($params['deskripsi']) ? $params['deskripsi'] : null;
            $m_menu->kategori_menu_id = isset($params['kategori']) ? $params['kategori'] : null;
            $m_menu->status = 1;
            $m_menu->induk_menu_id = $params['induk_menu_id'];
            $m_menu->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_menu, $deskripsi_log );

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

        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->where('kode_menu', $kode)->with(['kategori'])->first()->toArray();

        $m_km = new \Model\Storage\KategoriMenu_model();
        $d_km = $m_km->orderBy('nama', 'asc')->get();

        $kategori = null;
        if ( $d_km->count() > 0 ) {
            $kategori = $d_km->toArray();
        }

        $content['kategori'] = $kategori;
        $content['induk_menu'] = $this->getDataIndukMenu();
        $content['data'] = $d_menu;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_menu = new \Model\Storage\Menu_model();
            $m_menu->where('kode_menu', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'deskripsi' => isset($params['deskripsi']) ? $params['deskripsi'] : null,
                    'kategori_menu_id' => isset($params['kategori']) ? $params['kategori'] : null,
                    'status' => 1,
                    'induk_menu_id' => $params['induk_menu_id']
                )
            );

            $d_menu = $m_menu->where('kode_menu', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_menu, $deskripsi_log );

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
            $m_menu = new \Model\Storage\Menu_model();
            $m_menu->where('kode_menu', $kode)->update(
                array(
                    'status' => 0
                )
            );

            $d_menu = $m_menu->where('kode_menu', $kode)->first();

            // $m_menu->where('kode_menu', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_menu, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
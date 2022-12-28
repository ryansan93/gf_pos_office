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
                "assets/select2/js/select2.min.js",
                "assets/parameter/menu/js/menu.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/menu/css/menu.css",
            ));

            $data = $this->includes;

            $m_jp = new \Model\Storage\Menu_model();
            $d_jp = $m_jp->orderBy('nama', 'asc')->with(['kategori', 'jenis', 'induk_menu', 'branch'])->get()->toArray();

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

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->get();

        $data = null;
        if ( $d_branch->count() ) {
            $data = $d_branch->toArray();
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

        $m_jm = new \Model\Storage\JenisMenu_model();
        $d_jm = $m_jm->where('status', 1)->orderBy('nama', 'asc')->get();

        $jenis = null;
        if ( $d_jm->count() > 0 ) {
            $jenis = $d_jm->toArray();
        }

        $content['kategori'] = $kategori;
        $content['jenis'] = $jenis;
        $content['branch'] = $this->getBranch();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            foreach ($params['branch'] as $k_branch => $v_branch) {
                $m_menu = new \Model\Storage\Menu_model();

                $kode = $m_menu->getNextId();

                $m_menu->kode_menu = $kode;
                $m_menu->nama = $params['nama'];
                $m_menu->deskripsi = isset($params['deskripsi']) ? $params['deskripsi'] : null;
                $m_menu->jenis_menu_id = isset($params['jenis']) ? $params['jenis'] : null;
                $m_menu->kategori_menu_id = isset($params['kategori']) ? $params['kategori'] : null;
                $m_menu->branch_kode = $v_branch;
                $m_menu->additional = $params['additional'];
                $m_menu->status = 1;
                $m_menu->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_menu, $deskripsi_log );
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

        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->where('kode_menu', $kode)->with(['kategori'])->first()->toArray();

        $m_km = new \Model\Storage\KategoriMenu_model();
        $d_km = $m_km->orderBy('nama', 'asc')->get();

        $kategori = null;
        if ( $d_km->count() > 0 ) {
            $kategori = $d_km->toArray();
        }

        $m_jm = new \Model\Storage\JenisMenu_model();
        $d_jm = $m_jm->where('status', 1)->orderBy('nama', 'asc')->get();

        $jenis = null;
        if ( $d_jm->count() > 0 ) {
            $jenis = $d_jm->toArray();
        }

        $content['kategori'] = $kategori;
        $content['jenis'] = $jenis;
        $content['branch'] = $this->getBranch();
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
                    'jenis_menu_id' => isset($params['jenis']) ? $params['jenis'] : null,
                    'kategori_menu_id' => isset($params['kategori']) ? $params['kategori'] : null,
                    'additional' => $params['additional'],
                    'status' => 1
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
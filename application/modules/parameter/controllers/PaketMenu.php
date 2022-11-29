<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PaketMenu extends Public_Controller {

    private $pathView = 'parameter/paket_menu/';
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
                "assets/parameter/paket_menu/js/paket-menu.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/paket_menu/css/paket-menu.css",
            ));

            $data = $this->includes;

            $m_pm = new \Model\Storage\PaketMenu_model();
            $d_pm = $m_pm->with(['menu', 'isi_paket_menu'])->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_pm;
            $content['title_panel'] = 'Master Paket Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Paket Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getMenu()
    {
        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['menu'] = $this->getMenu();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_pm = new \Model\Storage\PaketMenu_model();

            $kode = $m_pm->getNextId();

            $m_pm->kode_paket_menu = $kode;
            $m_pm->menu_kode = $params['menu_kode'];
            $m_pm->nama = $params['nama'];
            $m_pm->max_pilih = $params['max_pilih'];
            $m_pm->_default = 0;
            $m_pm->save();

            foreach ($params['detail'] as $key => $value) {
                $m_ipm = new \Model\Storage\IsiPaketMenu_model();
                $m_ipm->paket_menu_kode = $kode;
                $m_ipm->menu_kode = $value['menu_kode'];
                $m_ipm->jumlah_min = $value['jumlah_min'];
                $m_ipm->jumlah_max = $value['jumlah_max'];
                $m_ipm->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_pm, $deskripsi_log );

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

        $m_pm = new \Model\Storage\PaketMenu_model();
        $d_pm = $m_pm->where('kode_paket_menu', $kode)->with(['menu', 'isi_paket_menu'])->first()->toArray();

        $content['menu'] = $this->getMenu();
        $content['data'] = $d_pm;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_pm = new \Model\Storage\PaketMenu_model();

            $m_pm->where('kode_paket_menu', $params['kode'])->update(
                array(
                    'menu_kode' => $params['menu_kode'],
                    'nama' => $params['nama'],
                    'max_pilih' => $params['max_pilih'],
                    '_default' => 0
                )
            );

            $m_ipm = new \Model\Storage\IsiPaketMenu_model();
            $d_ipm = $m_ipm->where('paket_menu_kode', $params['kode'])->delete();

            foreach ($params['detail'] as $key => $value) {
                $m_ipm = new \Model\Storage\IsiPaketMenu_model();
                $m_ipm->paket_menu_kode = $params['kode'];
                $m_ipm->menu_kode = $value['menu_kode'];
                $m_ipm->jumlah_min = $value['jumlah_min'];
                $m_ipm->jumlah_max = $value['jumlah_max'];
                $m_ipm->save();
            }

            $d_pm = $m_pm->where('kode_paket_menu', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_pm, $deskripsi_log );

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
            $m_pm = new \Model\Storage\PaketMenu_model();
            $d_pm = $m_pm->where('kode_paket_menu', $kode)->first();

            $m_pm->where('kode_paket_menu', $kode)->delete();

            $m_ipm = new \Model\Storage\IsiPaketMenu_model();
            $m_ipm->where('paket_menu_kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_pm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
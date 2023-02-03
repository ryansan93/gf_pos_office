<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diskon extends Public_Controller {

    private $pathView = 'parameter/Diskon/';
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
                "assets/parameter/diskon/js/diskon.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/diskon/css/diskon.css",
            ));

            $data = $this->includes;

            $m_diskon = new \Model\Storage\Diskon_model();
            $d_diskon = $m_diskon->orderBy('start_date', 'desc')->with(['branch'])->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_diskon;
            $content['title_panel'] = 'Master Diskon';

            // Load Indexx
            $data['title_menu'] = 'Master Diskon';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getTipeDiskon()
    {
        $data = $this->config->item('diskon_tipe');

        return $data;
    }

    public function getJenisMenu()
    {
        $m_jenis = new \Model\Storage\JenisMenu_model();
        $d_jenis = $m_jenis->where('status', 1)->get();

        $data = null;
        if ( $d_jenis->count() > 0 ) {
            $data = $d_jenis->toArray();
        }

        return $data;
    }

    public function getMenu($jenis_menu = null, $branch = null)
    {
        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->with(['kategori', 'branch'])->get();
        if ( !empty($jenis_menu) && !empty($branch) ) {
            $d_menu = $m_menu->where('status', 1)->where('jenis_menu_id', $jenis_menu)->where('branch_kode', $branch)->with(['kategori', 'branch'])->get();
        }

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        return $data;
    }

    public function getJenisKartu()
    {
        $m_jk = new \Model\Storage\JenisKartu_model();
        $d_jk = $m_jk->where('status', 1)->get();

        $data = null;
        if ( $d_jk->count() > 0 ) {
            $data = $d_jk->toArray();
        }

        return $data;
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

    public function getMenuHtml()
    {
        $params = $this->input->post('params');

        try {
            $jenis_menu = $params['jenis_menu'];
            $branch = $params['branch'];

            $data = $this->getMenu($jenis_menu, $branch);

            $this->result['content'] = $data;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalAddForm()
    {
        $content['branch'] = $this->getBranch();
        $content['tipe_diskon'] = $this->getTipeDiskon();
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['jenis_menu'] = $this->getJenisMenu();
        $content['menu'] = $this->getMenu();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            foreach ($params['branch'] as $k_branch => $v_branch) {
                $m_diskon = new \Model\Storage\Diskon_model();

                $kode = $m_diskon->getNextId();

                $m_diskon->kode = $kode;
                $m_diskon->branch_kode = $v_branch;
                $m_diskon->nama = $params['nama'];
                $m_diskon->deskripsi = $params['deskripsi'];
                $m_diskon->diskon_tipe = $params['tipe_diskon'];
                $m_diskon->member = $params['member'];
                $m_diskon->non_member = $params['non_member'];
                $m_diskon->ppn = $params['ppn'];
                $m_diskon->service_charge = $params['status_service_charge'];
                $m_diskon->start_date = $params['tgl_mulai'];
                $m_diskon->end_date = $params['tgl_akhir'];
                $m_diskon->start_time = $params['jam_mulai'];
                $m_diskon->end_time = $params['jam_akhir'];
                $m_diskon->diskon = $params['diskon'];
                $m_diskon->diskon_jenis = $params['diskon_jenis'];
                $m_diskon->min_beli = $params['min_beli'];
                $m_diskon->mstatus = 1;
                $m_diskon->save();

                if ( isset($params['jenis_kartu']) && !empty($params['jenis_kartu']) ) {
                    foreach ($params['jenis_kartu'] as $k_jk => $v_jk) {
                        $m_diskonjk = new \Model\Storage\DiskonJenisKartu_model();
                        $m_diskonjk->diskon_kode = $kode;
                        $m_diskonjk->jenis_kartu_kode = $v_jk;
                        $m_diskonjk->save();
                    }
                }

                if ( isset($params['diskon_menu']) && !empty($params['diskon_menu']) ) {
                    foreach ($params['diskon_menu'] as $k_dm => $v_dm) {
                        if ( $v_dm['branch_kode'] == $v_branch ) {
                            $m_dm = new \Model\Storage\DiskonMenu_model();
                            $m_dm->diskon_kode = $kode;
                            $m_dm->jenis_menu_id = $v_dm['jenis_menu_id'];
                            $m_dm->menu_kode = $v_dm['menu_kode'];
                            $m_dm->diskon = $v_dm['diskon'];
                            $m_dm->diskon_jenis = $v_dm['diskon_jenis'];
                            $m_dm->save();
                        }
                    }
                }

                if ( isset($params['diskon_beli_dapat']) && !empty($params['diskon_beli_dapat']) ) {
                    foreach ($params['diskon_beli_dapat'] as $k_dbd => $v_dbd) {
                        $m_dbd = new \Model\Storage\DiskonBeliDapat_model();
                        $m_dbd->diskon_kode = $kode;
                        $m_dbd->jenis_menu_id_beli = $v_dbd['jenis_menu_id_beli'];
                        $m_dbd->menu_kode_beli = $v_dbd['menu_kode_beli'];
                        $m_dbd->jumlah_beli = $v_dbd['jumlah_beli'];
                        $m_dbd->jenis_menu_id_dapat = $v_dbd['jenis_menu_id_dapat'];
                        $m_dbd->menu_kode_dapat = $v_dbd['menu_kode_dapat'];
                        $m_dbd->jumlah_dapat = $v_dbd['jumlah_dapat'];
                        $m_dbd->diskon_dapat = $v_dbd['diskon_dapat'];
                        $m_dbd->diskon_jenis_dapat = $v_dbd['diskon_jenis_dapat'];
                        $m_dbd->save();
                    }
                }

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_diskon, $deskripsi_log );
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

        $m_diskon = new \Model\Storage\Diskon_model();
        $d_diskon = $m_diskon->where('kode', $kode)->with(['detail', 'diskon_jenis_kartu', 'diskon_menu'])->first()->toArray();

        $content['branch'] = $this->getBranch();
        $content['tipe_diskon'] = $this->getTipeDiskon();
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['jenis_menu'] = $this->getJenisMenu();
        $content['menu'] = $this->getMenu();
        $content['data'] = $d_diskon;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_diskon = new \Model\Storage\Diskon_model();
            $m_diskon->where('kode', $params['kode'])->update(
                array(
                    'branch_kode' => $params['branch'][0],
                    'nama' => $params['nama'],
                    'deskripsi' => $params['deskripsi'],
                    'start_date' => $params['tgl_mulai'],
                    'end_date' => $params['tgl_akhir'],
                    'start_time' => $params['jam_mulai'],
                    'end_time' => $params['jam_akhir'],
                    'status_ppn' => $params['status_ppn'],
                    'ppn' => $params['ppn'],
                    'status_service_charge' => $params['status_service_charge'],
                    'service_charge' => $params['service_charge']
                )
            );

            $m_diskond = new \Model\Storage\DiskonDet_model();
            $m_diskond->where('diskon_kode', $params['kode'])->update(
                array(
                    'persen' => $params['persen'],
                    'nilai' => $params['nilai'],
                    'non_member' => $params['non_member'],
                    'member' => $params['member'],
                    'min_beli' => $params['min_beli']
                )
            );

            $m_diskonjk = new \Model\Storage\DiskonJenisKartu_model();
            $m_diskonjk->where('diskon_kode', $params['kode'])->delete();

            if ( isset($params['jenis_kartu']) && !empty($params['jenis_kartu']) ) {
                foreach ($params['jenis_kartu'] as $k_jk => $v_jk) {
                    $m_diskonjk = new \Model\Storage\DiskonJenisKartu_model();
                    $m_diskonjk->diskon_kode = $params['kode'];
                    $m_diskonjk->jenis_kartu_kode = $v_jk;
                    $m_diskonjk->save();
                }
            }

            $m_diskonmenu = new \Model\Storage\DiskonMenu_model();
            $m_diskonmenu->where('diskon_kode', $params['kode'])->delete();

            if ( isset($params['menu']) && !empty($params['menu']) ) {
                foreach ($params['menu'] as $k_menu => $v_menu) {
                    $m_diskonmenu = new \Model\Storage\DiskonMenu_model();
                    $m_diskonmenu->diskon_kode = $params['kode'];
                    $m_diskonmenu->menu_kode = $v_menu['menu'];
                    $m_diskonmenu->jumlah_min = $v_menu['jumlah_min'];
                    $m_diskonmenu->save();
                }
            }

            $d_diskon = $m_diskon->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_diskon, $deskripsi_log );

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
            $m_diskon = new \Model\Storage\Diskon_model();
            $m_diskon->where('kode', $kode)->update( 
                array(
                    'mstatus' => 0
                ) 
            );
            $d_kode = $m_diskon->where('kode', $kode)->first();

            // $m_diskond = new \Model\Storage\DiskonDet_model();

            // $m_diskond->where('diskon_kode', $kode)->delete();
            // $m_diskon->where('kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_kode, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
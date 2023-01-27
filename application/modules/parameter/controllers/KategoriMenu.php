<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KategoriMenu extends Public_Controller {

    private $pathView = 'parameter/kategori_menu/';
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
                "assets/parameter/kategori_menu/js/kategori-menu.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/kategori_menu/css/kategori-menu.css",
            ));

            $data = $this->includes;

            $m_km = new \Model\Storage\KategoriMenu_model();
            $d_km = $m_km->orderBy('nama', 'asc')->get();

            $data_km = null;
            if ( $d_km->count() > 0 ) {
                $d_km = $d_km->toArray();

                $data_km = $this->mappingDataKategoriMenu($d_km);
            }

            $content['akses'] = $this->hakAkses;
            $content['data'] = $data_km;
            $content['title_panel'] = 'Master Kategori Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Kategori Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function mappingDataKategoriMenu($d_km)
    {
        $data = null;
        foreach ($d_km as $k_km => $v_km) {
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    mu.id_user, 
                    mu.username_user, 
                    du.nama_detuser as nama_user, 
                    mg.nama_group as nama_group 
                from kategori_menu_user kmu
                right join
                    ms_user mu
                    on
                        mu.id_user = kmu.user_id
                right join
                    (
                        select du1.* from detail_user du1
                        right join
                            (select max(id_detuser) as id_detuser, id_user from detail_user group by id_user) du2
                            on
                                du1.id_detuser = du2.id_detuser
                    ) du 
                    on
                        mu.id_user = du.id_user
                right join
                    ms_group mg 
                    on
                        mg.id_group = du.id_group
                where
                    kmu.kategori_menu_id = '".$v_km['id']."'
                order by
                    mg.nama_group asc,
                    du.nama_detuser asc
            ";
            $d_user = $m_conf->hydrateRaw( $sql );

            $data_user = null;
            if ( $d_user->count() > 0 ) {
                $data_user = $d_user->toArray();
            }

            $data[] = array(
                'id' => $v_km['id'],
                'nama' => $v_km['nama'],
                'print_cl' => $v_km['print_cl'],
                'status' => $v_km['status'],
                'user' => $data_user
            );
        }

        return $data;
    }

    public function getUser()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select mu.id_user, mu.username_user, du.nama_detuser as nama_user, mg.nama_group as nama_group from ms_user mu 
            right join
                (
                    select du1.* from detail_user du1
                    right join
                        (select max(id_detuser) as id_detuser, id_user from detail_user group by id_user) du2
                        on
                            du1.id_detuser = du2.id_detuser
                ) du 
                on
                    mu.id_user = du.id_user
            right join
                ms_group mg 
                on
                    mg.id_group = du.id_group 
            where
                mu.status_user = 1
        ";
        $d_user = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_user->count() > 0 ) {
            $data = $d_user->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['user'] = $this->getUser();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_km = new \Model\Storage\KategoriMenu_model();

            $id = $m_km->getNextIdentity();

            $m_km->id = $id;
            $m_km->nama = $params['nama'];
            $m_km->status = 1;
            $m_km->print_cl = $params['print_cl'];
            $m_km->save();

            foreach ($params['user'] as $key => $value) {
                $m_kmu = new \Model\Storage\KategoriMenuUser_model();
                $m_kmu->user_id = $value;
                $m_kmu->kategori_menu_id = $id;
                $m_kmu->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_km, $deskripsi_log );

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

        $m_km = new \Model\Storage\KategoriMenu_model();
        $d_km = $m_km->where('id', $kode)->first()->toArray();

        $m_kmu = new \Model\Storage\KategoriMenuUser_model();
        $d_kmu = $m_kmu->where('kategori_menu_id', $kode)->get();

        $kategori_menu_user = null;
        if ( $d_kmu->count() > 0 ) {
            $d_kmu = $d_kmu->toArray();

            foreach ($d_kmu as $key => $value) {
                $kategori_menu_user[] = $value['user_id'];
            }
        }

        $content['data'] = $d_km;
        $content['user'] = $this->getUser();
        $content['kategori_menu_user'] = $kategori_menu_user;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_km = new \Model\Storage\KategoriMenu_model();
            $m_km->where('id', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'status' => 1,
                    'print_cl' => $params['print_cl']
                )
            );

            $m_kmu = new \Model\Storage\KategoriMenuUser_model();
            $m_kmu->where('kategori_menu_id', $params['kode'])->delete();

            foreach ($params['user'] as $key => $value) {
                $m_kmu = new \Model\Storage\KategoriMenuUser_model();
                $m_kmu->user_id = $value;
                $m_kmu->kategori_menu_id = $params['kode'];
                $m_kmu->save();
            }

            $d_km = $m_km->where('id', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_km, $deskripsi_log );

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
            $m_km = new \Model\Storage\KategoriMenu_model();
            $m_km->where('id', $kode)->update(
                array(
                    'status' => 0
                )
            );

            $d_km = $m_km->where('id', $kode)->first();

            // $m_km->where('id', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_km, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
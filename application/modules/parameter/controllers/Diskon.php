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

    public function getRequirementDiskon()
    {
        $data = $this->config->item('diskon_requirement');

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
        $content['requirement_diskon'] = $this->getRequirementDiskon();
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['jenis_menu'] = $this->getJenisMenu();
        $content['menu'] = $this->getMenu();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function modalViewForm()
    {
        $kode = $this->input->get('params');

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                brc.nama as nama_branch,
                d.nama as nama_diskon,
                d.deskripsi,
                d.diskon_tipe as tipe_diskon,
                d.diskon_requirement as jenis_diskon,
                d.member,
                d.non_member,
                d.status_ppn,
                d.status_service_charge,
                d.ppn,
                d.service_charge,
                d.harga_hpp,
                d.start_date as tgl_mulai,
                d.end_date as tgl_berakhir,
                d.start_time as jam_mulai,
                d.end_time as jam_berakhir,
                d.diskon,
                d.diskon_jenis,
                d.min_beli
            from diskon d
            right join
                branch brc
                on
                    d.branch_kode = brc.kode_branch
            where
                d.kode = '".$kode."'
        ";
        $d_diskon = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_diskon->count() > 0 ) {
            $d_diskon = $d_diskon->toArray();

            foreach ($d_diskon as $k => $val) {
                $data = array(
                    'nama_branch' => $val['nama_branch'],
                    'nama_diskon' => $val['nama_diskon'],
                    'deskripsi' => $val['deskripsi'],
                    'tipe_diskon' => $val['tipe_diskon'],
                    'nama_tipe_diskon' => $this->getTipeDiskon()[ $val['tipe_diskon'] ],
                    'jenis_diskon' => $val['jenis_diskon'],
                    'nama_jenis_diskon' => !empty($val['jenis_diskon']) ? $this->getRequirementDiskon()[ $val['jenis_diskon'] ] : '-',
                    'member' => $val['member'],
                    'non_member' => $val['non_member'],
                    'status_ppn' => $val['status_ppn'],
                    'status_service_charge' => $val['status_service_charge'],
                    'ppn' => $val['ppn'],
                    'service_charge' => $val['service_charge'],
                    'harga_hpp' => $val['harga_hpp'],
                    'tgl_mulai' => $val['tgl_mulai'],
                    'tgl_berakhir' => $val['tgl_berakhir'],
                    'jam_mulai' => $val['jam_mulai'],
                    'jam_berakhir' => $val['jam_berakhir'],
                    'jenis_kartu' => null,
                    'detail' => null
                );

                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select
                        djk.*,
                        jk.nama as nama_kartu
                    from diskon_jenis_kartu djk
                    right join
                        jenis_kartu jk
                        on
                            djk.jenis_kartu_kode = jk.kode_jenis_kartu
                    where
                        djk.diskon_kode = '".$kode."'
                ";
                $d_djk = $m_conf->hydrateRaw( $sql );
                if ( $d_djk->count() ) {
                    $data['jenis_kartu'] = $d_djk->toArray();
                }

                $detail = null;
                if ( $val['tipe_diskon'] == 1 ) {
                    $detail = array(
                        'diskon' => $val['diskon'],
                        'jenis' => $val['diskon_jenis'],
                        'min_beli' => $val['min_beli']
                    );
                }

                if ( $val['tipe_diskon'] == 2 ) {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select
                            dm.*,
                            CASE
                                WHEN dm.jenis_menu_id = 'all' then
                                    'ALL'
                                WHEN dm.jenis_menu_id <> 'all' then
                                    jm.nama
                            END as nama_jenis_menu,
                            CASE
                                WHEN dm.menu_kode = 'all' then
                                    'ALL'
                                WHEN dm.menu_kode <> 'all' then
                                    m.nama
                            END as nama_menu
                        from diskon_menu dm
                        left join
                            jenis_menu jm
                            on
                                dm.jenis_menu_id = cast(jm.id as varchar(5))
                        left join
                            menu m
                            on
                                dm.menu_kode = m.kode_menu
                        where
                            dm.diskon_kode = '".$kode."'
                    ";
                    $d_dbd = $m_conf->hydrateRaw( $sql );
                    if ( $d_dbd->count() ) {
                        $detail = $d_dbd->toArray();
                    }
                }

                if ( $val['tipe_diskon'] == 3 ) {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select
                            CASE
                                WHEN dbd.jenis_menu_id_beli = 'all' then
                                    'ALL'
                                WHEN dbd.jenis_menu_id_beli <> 'all' then
                                    jm_beli.nama
                            END as nama_jenis_menu_beli,
                            CASE
                                WHEN dbd.menu_kode_beli = 'all' then
                                    'ALL'
                                WHEN dbd.menu_kode_beli <> 'all' then
                                    m_beli.nama
                            END as nama_menu_beli,
                            dbd.jumlah_beli,
                            CASE
                                WHEN dbd.jenis_menu_id_dapat = 'all' then
                                    'ALL'
                                WHEN dbd.jenis_menu_id_dapat <> 'all' then
                                    jm_dapat.nama
                            END as nama_jenis_menu_dapat,
                            CASE
                                WHEN dbd.menu_kode_dapat = 'all' then
                                    'ALL'
                                WHEN dbd.menu_kode_dapat <> 'all' then
                                    m_dapat.nama
                            END as nama_menu_dapat,
                            dbd.jumlah_dapat,
                            dbd.diskon_dapat,
                            dbd.diskon_jenis_dapat
                        from diskon_beli_dapat dbd
                        left join
                            jenis_menu jm_beli
                            on
                                dbd.jenis_menu_id_beli = cast(jm_beli.id as varchar(5))
                        left join
                            jenis_menu jm_dapat
                            on
                                dbd.jenis_menu_id_dapat = cast(jm_dapat.id as varchar(5))
                        left join
                            menu m_beli
                            on
                                dbd.menu_kode_beli = m_beli.kode_menu
                        left join
                            menu m_dapat
                            on
                                dbd.menu_kode_dapat = m_dapat.kode_menu
                        where
                            dbd.diskon_kode = '".$kode."'
                    ";
                    $d_dbd = $m_conf->hydrateRaw( $sql );
                    if ( $d_dbd->count() ) {
                        $detail = $d_dbd->toArray();
                    }
                }

                $data['detail'] = $detail;
            }
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

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
                $m_diskon->diskon_requirement = $params['requirement_diskon'];
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
                $m_diskon->harga_hpp = $params['harga_hpp'];
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
                        // if ( $v_dm['branch_kode'] == $v_branch ) {
                            $m_dm = new \Model\Storage\DiskonMenu_model();
                            $m_dm->diskon_kode = $kode;
                            $m_dm->jenis_menu_id = $v_dm['jenis_menu_id'];
                            $m_dm->menu_kode = $v_dm['menu_kode'];
                            $m_dm->jml_min = $v_dm['jml_min'];
                            $m_dm->min_beli = $v_dm['min_beli'];
                            $m_dm->diskon = $v_dm['diskon'];
                            $m_dm->diskon_jenis = $v_dm['diskon_jenis'];
                            $m_dm->save();
                        // }
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
                Modules::run( 'base/event/save', $m_diskon, $deskripsi_log, $kode );
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
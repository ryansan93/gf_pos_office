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

    public function getJenisPesanan( $kode = null )
    {
        $data = null;

        if ( empty($kode) ) {
            $m_jp = new \Model\Storage\JenisPesanan_model();
            $d_jp = $m_jp->orderBy('nama', 'asc')->get();

            if ( $d_jp->count() > 0 ) {
                $data = $d_jp->toArray();
            }
        } else {
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    hm.harga,
                    hm.jenis_pesanan_kode as kode,
                    jp.nama 
                from harga_menu hm
                right join
                    (
                        select
                            max(id) as id,
                            menu_kode,
                            jenis_pesanan_kode 
                        from harga_menu
                        group by
                            menu_kode,
                            jenis_pesanan_kode 
                    ) hm1
                    on
                        hm.id = hm1.id
                right join
                    jenis_pesanan jp 
                    on
                        hm.jenis_pesanan_kode = jp.kode 
                where
                    hm.menu_kode = '".$kode."'
                order by
                    jp.nama asc
            ";
            $d_hm = $m_conf->hydrateRaw( $sql );

            if ( $d_hm->count() > 0 ) {
                $data = $d_hm->toArray();
            } 
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
        $content['jenis_pesanan'] = $this->getJenisPesanan();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        // $params = $this->input->post('params');

        $params = json_decode($this->input->post('data'),TRUE);
        $files = isset($_FILES['file']) ? $_FILES['file'] : [];
        $mappingFiles = mappingFiles($files);

        try {
            // cetak_r( $mappingFiles, 1 );

            $file_name = null;
            $path_name = null;
            if (!empty($mappingFiles)) {
                $moved = uploadFile($mappingFiles);
                $isMoved = $moved['status'];

                if ( $isMoved ) {
                    $file_name = $moved['name'];
                    $path_name = $moved['path'];
                }
            }

            foreach ($params['branch'] as $k_branch => $v_branch) {
                $m_menu = new \Model\Storage\Menu_model();
                $now = $m_menu->getDate();

                $kode = $m_menu->getNextId();

                $m_menu->kode_menu = $kode;
                $m_menu->nama = $params['nama'];
                $m_menu->deskripsi = isset($params['deskripsi']) ? $params['deskripsi'] : null;
                $m_menu->jenis_menu_id = isset($params['jenis']) ? $params['jenis'] : null;
                $m_menu->kategori_menu_id = isset($params['kategori']) ? $params['kategori'] : null;
                $m_menu->branch_kode = $v_branch;
                $m_menu->additional = $params['additional'];
                $m_menu->ppn = $params['ppn'];
                $m_menu->service_charge = $params['service_charge'];
                $m_menu->status = 1;
                $m_menu->file_name = $file_name;
                $m_menu->path_name = $path_name;
                $m_menu->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_menu, $deskripsi_log );

                foreach ($params['list_jenis_pesanan'] as $key => $value) {
                    $m_hm = new \Model\Storage\HargaMenu_model();
                    $m_hm->jenis_pesanan_kode = $value['jenis_pesanan'];
                    $m_hm->menu_kode = $kode;
                    $m_hm->harga = $value['harga'];
                    $m_hm->tgl_mulai = $now['tanggal'];
                    $m_hm->save();

                    $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $m_hm, $deskripsi_log );
                }
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
        $content['jenis_pesanan'] = $this->getJenisPesanan( $kode );

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        // $params = $this->input->post('params');

        $params = json_decode($this->input->post('data'),TRUE);
        $files = isset($_FILES['file']) ? $_FILES['file'] : [];
        $mappingFiles = !empty($files) ? mappingFiles($files) : null;

        try {
            $file_name = $params['filename_old'];
            $path_name = $params['pathname_old'];
            if (!empty($mappingFiles)) {
                $moved = uploadFile($mappingFiles);
                $isMoved = $moved['status'];

                if ( $isMoved ) {
                    $file_name = $moved['name'];
                    $path_name = $moved['path'];
                }
            }

            $m_menu = new \Model\Storage\Menu_model();
            $now = $m_menu->getDate();

            $m_menu->where('kode_menu', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'deskripsi' => isset($params['deskripsi']) ? $params['deskripsi'] : null,
                    'jenis_menu_id' => isset($params['jenis']) ? $params['jenis'] : null,
                    'kategori_menu_id' => isset($params['kategori']) ? $params['kategori'] : null,
                    'additional' => $params['additional'],
                    'ppn' => $params['ppn'],
                    'service_charge' => $params['service_charge'],
                    'status' => 1,
                    'file_name' => $file_name,
                    'path_name' => $path_name
                )
            );

            $d_menu = $m_menu->where('kode_menu', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_menu, $deskripsi_log );

            foreach ($params['list_jenis_pesanan'] as $key => $value) {
                $m_hm = new \Model\Storage\HargaMenu_model();
                $m_hm->jenis_pesanan_kode = $value['jenis_pesanan'];
                $m_hm->menu_kode = $params['kode'];
                $m_hm->harga = $value['harga'];
                $m_hm->tgl_mulai = $now['tanggal'];
                $m_hm->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_hm, $deskripsi_log );
            }

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
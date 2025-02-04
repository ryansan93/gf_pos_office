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
        $mappingFiles = (!empty($files) && count($files) > 0) ? mappingFiles($files) : null;

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

                // $id = $m_menu->getNextIdentity();
                $kode = $m_menu->getNextId();

                // $m_menu->id = $id;
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
                // $m_menu->file_name = $file_name;
                // $m_menu->path_name = $path_name;
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
        $mappingFiles = (!empty($files) && count($files) > 0) ? mappingFiles($files) : null;

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
                    // 'file_name' => $file_name,
                    // 'path_name' => $path_name
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

    public function importForm()
    {
        $d_content['akses'] = $this->hakAkses;
		$html = $this->load->view($this->pathView.'/importForm', $d_content, true);

		echo $html;
    }

    public function import() {
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;

        try {
            if ( !empty($file) ) {
                $upload_path = FCPATH . "//uploads/import_file/";
                $moved = uploadFile($file, $upload_path);
                if ( $moved ) {
                    $path_name = $moved['path'];

                    $data = $this->getDataExcelUsingSpreadSheet( $path_name );

                    if ( !empty($data) && count($data) > 0 ) {
                        foreach ($data as $key => $value) {
                            $m_menu = new \Model\Storage\Menu_model();

                            $m_menu->kode_menu = $value['kode_menu'];
                            $m_menu->nama = $value['nama'];
                            $m_menu->deskripsi = $value['deskripsi'];
                            $m_menu->jenis_menu_id = $value['jenis_menu_id'];
                            $m_menu->kategori_menu_id = $value['kategori_menu_id'];
                            $m_menu->branch_kode = $value['branch_kode'];
                            $m_menu->additional = $value['additional'];
                            $m_menu->ppn = $value['ppn'];
                            $m_menu->service_charge = $value['service_charge'];
                            $m_menu->status = 1;
                            $m_menu->save();

                            $deskripsi_log = 'di-import oleh ' . $this->userdata['detail_user']['nama_detuser'];
                            Modules::run( 'base/event/save', $m_menu, $deskripsi_log );
                        }

                        $this->result['status'] = 1;
                        $this->result['message'] = 'Data berhasil di import.';
                    } else {
                        $this->result['message'] = 'Data yang anda upload kosong.';
                    }
                } else {
                    $this->result['message'] = 'File gagal terupload, segera hubungi tim IT.';
                }
            }
        } catch (Exception $e) {
            $this->result['message'] = 'GAGAL : '.$e->getMessage();
        }

        display_json( $this->result );
    }

    public function downloadTemplate() {
        $fileName = 'template_import_menu';
        $arr_header = array('Kode', 'Nama', 'Deskripsi', 'Kode Branch', 'Kategori', 'Jenis', 'Additional (Ya=1/Tidak=0)', 'PB1 (Ya=1/Tidak=0)', 'Service Charge (Ya=1/Tidak=0)');
        $arr_column[0] = array(
            'Kode' => array('value' => 'MNU2501012', 'data_type' => 'string'),
            'Nama' => array('value' => 'BIHUN HOT PLATE BEEF', 'data_type' => 'string'),
            'Deskripsi' => array('value' => '', 'data_type' => 'string'),
            'Kode Branch' => array('value' => 'GTR', 'data_type' => 'string'),
            'Kategori' => array('value' => 'BAVERAGE', 'data_type' => 'string'),
            'Jenis' => array('value' => 'ADDITIONAL BEVERAGE', 'data_type' => 'string'),
            'Additional (Ya=1/Tidak=0)' => array('value' => '0', 'data_type' => 'string'),
            'PB1 (Ya=1/Tidak=0)' => array('value' => '1', 'data_type' => 'string'),
            'Service Charge (Ya=1/Tidak=0)' => array('value' => '1', 'data_type' => 'string'),
        );

        Modules::run( 'base/ExportExcel/exportExcelUsingSpreadSheet', $fileName, $arr_header, $arr_column );

        $this->load->helper('download');
        force_download('export_excel/'.$fileName.'.xlsx', NULL);
    }

    public function getDataExcelUsingSpreadSheet( $path_name ) {
        $path = 'uploads/import_file/'.$path_name;

        $data = null;

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($path, \PhpOffice\PhpSpreadsheet\Reader\IReader::LOAD_WITH_CHARTS); // Load file yang tadi diupload ke folder tmp
        // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path, \PhpOffice\PhpSpreadsheet\Reader\IReader::LOAD_WITH_CHARTS);
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $numrow = 1;
        $key = null;

        foreach($sheet as $row){
            // Ambil data pada excel sesuai Kolom
            $kode = $row['A'];
            $nama = $row['B'];
            $deskripsi = $row['C'];
            $kode_branch = $row['D'];
            $kategori = $row['E'];
            $jenis = $row['F'];
            $additional = $row['G'];
            $pb1 = $row['H'];
            $service_charge = $row['I'];
            // Cek jika semua data tidak diisi
            if($kode == "" && $nama == "" && $kode_branch == "" && $kategori == "" && $jenis == "" && $additional == "" && $pb1 == "" && $service_charge == "")
            continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)
            // Cek $numrow apakah lebih dari 1
            // Artinya karena baris pertama adalah nama-nama kolom
            // Jadi dilewat saja, tidak usah diimport
            if($numrow > 1){
                $key = trim($kode);

                $m_km = new \Model\Storage\KategoriMenu_model();
                $data_km = $m_km->getData(array($kategori));
                $kategori_menu_id = $data_km[0]['id'];
                
                $m_jm = new \Model\Storage\JenisMenu_model();
                $data_jm = $m_jm->getData(array($jenis));
                $jenis_menu_id = $data_jm[0]['id'];

                $data[ $key ] = array(
                    'kode_menu' => $kode,
                    'nama' => $nama,
                    'deskripsi' => $deskripsi,
                    'branch_kode' => $kode_branch,
                    'kategori_menu_id' => $kategori_menu_id,
                    'jenis_menu_id' => $jenis_menu_id,
                    'additional' => $additional,
                    'ppn' => $pb1,
                    'service_charge' => $service_charge,
                );
            }
            $numrow++; // Tambah 1 setiap kali looping
        }

        return $data;
    }
}
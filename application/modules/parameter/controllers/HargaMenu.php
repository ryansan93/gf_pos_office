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
            $content['branch'] = $this->getBranch();
            $content['menu'] = $this->getMenu();
            $content['jenis_pesanan'] = $this->getJenisPesanan();
            $content['title_panel'] = 'Master Harga Menu';

            // Load Indexx
            $data['title_menu'] = 'Master Harga Menu';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getMenu()
    {
        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->orderBy('nama', 'asc')->with(['jenis'])->get();

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

    public function getMenuByBranch()
    {
        $kode_branch = $this->input->get('kode_branch');

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select jm.nama as nama_jenis, m.kode_menu, m.nama as nama_menu, m.tanggal from menu m
            right join
                jenis_menu jm
                on
                    m.jenis_menu_id = jm.id
            where
                m.branch_kode = '".$kode_branch."'
        ";

        $d_menu = $m_conf->hydrateRaw( $sql );

        $html = '<option value="">-- Pilih Menu --</option>';
        if ( $d_menu->count() > 0 ) {
            $d_menu = $d_menu->toArray();

            foreach ($d_menu as $k_menu => $v_menu) {
                $html .= '<option value="'.$v_menu['kode_menu'].'" data-branch="'.$v_menu['branch_kode'].'" >'.$v_menu['nama_jenis'].' | '.$v_menu['nama_menu'].'</option>';
            }
        }

        echo $html;
    }

    public function modalAddForm()
    {
        $content['branch'] = $this->getBranch();
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

        $content['branch'] = $this->getBranch();
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
                            if ( stristr($value['tanggal'], '/') !== false ) {
                                $_tanggal = explode('/',trim(preg_replace('/\s/u', ' ', $value['tanggal'])));
                                
                                $tahun = null;
                                $bulan = null;
                                $hari = null;
                                if ( count($_tanggal) < 3 ) {
                                    $tahun = date("Y");
                                    $bulan = ( strlen(preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-1])) > 1 ) ? preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-1]) : '0'.preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-1]);
                                    $hari = ( strlen(preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2])) > 0 ) ? preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2]) : '0'.preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2]);
                                } else {
                                    $tahun = preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-1]);
                                    $bulan = ( strlen(preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2])) > 1 ) ? preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2]) : '0'.preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-2]);
                                    $hari = ( strlen(preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-3])) > 0 ) ? preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-3]) : '0'.preg_replace('/\s/u', ' ', $_tanggal[count($_tanggal)-3]);
                                }

                                if ( $bulan > 12 ) {
                                    $tanggal = $tahun.'-'.$hari.'-'.$bulan;
                                } else {
                                    $tanggal = $tahun.'-'.$bulan.'-'.$hari;
                                }
                            } else {
                                $tanggal = $value['tanggal'];
                            }

                            if ( isset($value['jenis_pesanan_kode']) && !empty($value['jenis_pesanan_kode']) ) {
                                $m_hm = new \Model\Storage\HargaMenu_model();
                                $m_hm->jenis_pesanan_kode = $value['jenis_pesanan_kode'];
                                $m_hm->menu_kode = $value['menu_kode'];
                                $m_hm->harga = $value['harga'];
                                $m_hm->tgl_mulai = $tanggal;
                                $m_hm->save();

                                $deskripsi_log = 'di-import oleh ' . $this->userdata['detail_user']['nama_detuser'];
                                Modules::run( 'base/event/save', $m_hm, $deskripsi_log );
                            } else {
                                $jp = $this->getJenisPesanan();

                                foreach ($jp as $k_jp => $v_jp) {
                                    if ( stristr( $v_jp['nama'], 'dine in' ) !== false || stristr( $v_jp['nama'], 'take away' ) !== false ) {
                                        $m_hm = new \Model\Storage\HargaMenu_model();
                                        $m_hm->jenis_pesanan_kode = $v_jp['kode'];
                                        $m_hm->menu_kode = $value['menu_kode'];
                                        $m_hm->harga = $value['harga'];
                                        $m_hm->tgl_mulai = $tanggal;
                                        $m_hm->save();
                                        
                                        $deskripsi_log = 'di-import oleh ' . $this->userdata['detail_user']['nama_detuser'];
                                        Modules::run( 'base/event/save', $m_hm, $deskripsi_log );
                                    }
                                }
                            }

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
        $fileName = 'template_import_harga_menu';
        $arr_header = array('Jenis Pesanan', 'Kode Menu', 'Harga', 'Tgl Berlaku');
        $arr_column[0] = array(
            'Jenis Pesanan' => array('value' => 'DINE IN', 'data_type' => 'string'),
            'Kode Menu' => array('value' => 'MNU2501012', 'data_type' => 'string'),
            'Harga' => array('value' => 10000, 'data_type' => 'integer'),
            'Tgl Berlaku' => array('value' => '2025-02-04', 'data_type' => 'date', 'data_format' => 'yyyy-mm-dd'),
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
            $jenis_pesanan = $row['A'];
            $kode_menu = $row['B'];
            $harga = preg_replace('/\s/u', '', str_replace(',', '', $row['C']));
            $tgl_berlaku = $row['D'];
            // Cek jika semua data tidak diisi
            if($kode_menu == "" && $harga == "" && $tgl_berlaku == "")
            continue; // Lewat data pada baris ini (masuk ke looping selanjutnya / baris selanjutnya)
            // Cek $numrow apakah lebih dari 1
            // Artinya karena baris pertama adalah nama-nama kolom
            // Jadi dilewat saja, tidak usah diimport
            if($numrow > 1){
                $key = trim($kode_menu).'-'.$jenis_pesanan;

                $m_jp = new \Model\Storage\JenisPesanan_model();
                $data_jp = $m_jp->getData(array($jenis_pesanan));
                $jenis_pesanan_kode = $data_jp[0]['kode'];

                $data[ $key ] = array(
                    'jenis_pesanan_kode' => $jenis_pesanan_kode,
                    'menu_kode' => $kode_menu,
                    'harga' => !empty($harga) ? $harga : 0,
                    'tanggal' => $tgl_berlaku,
                );
            }
            $numrow++; // Tambah 1 setiap kali looping
        }

        return $data;
    }
}
<?php defined('BASEPATH') or exit('No direct script access allowed');

class MenuGagal extends Public_Controller
{
    private $pathView = 'transaksi/menu_gagal/';
    private $url;
    private $hakAkses;
    private $persen_ppn = 0;
    /**
     * Constructor
     */
    public function __construct()
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
    public function index()
    {
        // if ( $this->hakAkses['a_view'] == 1 ) {
            $this->load->library('Mobile_Detect');
            $detect = new Mobile_Detect();

            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    "assets/transaksi/menu_gagal/js/menu-gagal.js"
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/transaksi/menu_gagal/css/menu-gagal.css"
                )
            );
            $data = $this->includes;

            $content['akses'] = $this->hakAkses;

            $content['riwayatForm'] = $this->riwayatForm();
            $content['addForm'] = $this->addForm();

            $data['title_menu'] = 'Menu Gagal';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function getLists()
    {
        $date = $this->input->get('date');
        $branch_kode = $this->input->get('branch_kode');

        $_branch_kode = "'".implode("', '", $branch_kode)."'";

        $m_wm = new \Model\Storage\WasteMenu_model();
        $sql = "
            select 
                wm.id,
                wm.tanggal,
                b.nama as branch,
                sum(wmi.jumlah) as jumlah
            from waste_menu_item wmi
            right join
                waste_menu wm
                on
                    wmi.id_header = wm.id
            right join
                branch b
                on
                    wm.branch_kode = b.kode_branch
            where
                wm.tanggal = '".$date."' and
                wm.branch_kode in (".$_branch_kode.")
            group by
                wm.id,
                wm.tanggal,
                b.nama

        ";
        $d_wm = $m_wm->hydrateRaw( $sql );

        $data = null;
        if ( $d_wm->count() > 0 ) {
            $data = $d_wm->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'listRiwayat', $content, TRUE);

        echo $html;
    }

    public function loadForm()
    {
        $id = $this->input->get('id');
        $edit = $this->input->get('edit');

        $html = null;

        if ( !empty($id) && !empty($edit) ) {
            $html = $this->editForm( $id );
        } else if ( !empty($id) && empty($edit) ) {
            $html = $this->viewForm( $id );
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function riwayatForm()
    {
        $content['akses'] = $this->hakAkses;
        $content['branch'] = $this->getBranch();
        $html = $this->load->view($this->pathView . 'riwayatForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['branch'] = $this->getBranch();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function viewForm( $id )
    {
        $m_wm = new \Model\Storage\WasteMenu_model();
        $d_wm = $m_wm->where('id', $id)->with(['branch', 'waste_menu_item'])->first()->toArray();

        $content['data'] = $d_wm;
        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function editForm($id )
    {
        $m_wm = new \Model\Storage\WasteMenu_model();
        $d_wm = $m_wm->where('id', $id)->with(['branch', 'waste_menu_item'])->first()->toArray();

        $content['data'] = $d_wm;
        $content['branch'] = $this->getBranch();
        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        return $html;
    }

    public function getMenu()
    {
        $params = $this->input->post('params');

        try {
            $m_menu = new \Model\Storage\Menu_model();
            $d_menu = $m_menu->where('branch_kode', $params)->orderBy('nama', 'asc')->get();

            $data = null;
            if ( $d_menu->count() > 0 ) {
                $data = $d_menu->toArray();
            }

            $this->result['status'] = 1;
            $this->result['content'] = $data;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_wm = new \Model\Storage\WasteMenu_model();
            $m_wm->tanggal = $params['tanggal'];
            $m_wm->branch_kode = $params['branch_kode'];
            $m_wm->save();

            $id_header = $m_wm->id;

            foreach ($params['list_menu'] as $key => $value) {
                $m_wmi = new \Model\Storage\WasteMenuItem_model();
                $m_wmi->id_header = $id_header;
                $m_wmi->menu_kode = $value['menu_kode'];
                $m_wmi->jumlah = $value['jumlah'];
                $m_wmi->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_wm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            $this->result['content'] = array('id' => $id_header);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_wm = new \Model\Storage\WasteMenu_model();
            $m_wm->where('id', $params['id'])->update(
                array(
                    'tanggal' => $params['tanggal'],
                    'branch_kode' => $params['branch_kode']
                )
            );

            $id_header = $params['id'];

            $m_wmi = new \Model\Storage\WasteMenuItem_model();
            $m_wmi->where('id_header', $id_header)->delete();

            foreach ($params['list_menu'] as $key => $value) {
                $m_wmi = new \Model\Storage\WasteMenuItem_model();
                $m_wmi->id_header = $id_header;
                $m_wmi->menu_kode = $value['menu_kode'];
                $m_wmi->jumlah = $value['jumlah'];
                $m_wmi->save();
            }

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_wm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di ubah.';
            $this->result['content'] = array('id' => $id_header);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id = $this->input->post('id');

        try {
            $m_wm = new \Model\Storage\WasteMenu_model();
            $d_wm = $m_wm->where('id', $id)->first();

            $m_wmi = new \Model\Storage\WasteMenuItem_model();
            $m_wmi->where('id_header', $id)->delete();

            $m_wm->where('id', $id)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_wm, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function mappingDataExportPdf($params)
    {
        $tanggal = $params['tanggal'];
        $branch_kode = $params['branch_kode'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                wmi.pesanan_kode as kode_pesanan,
                m.nama as nama_menu,
                wmi.jumlah,
                wmi.nama_user,
                wmi.keterangan
            from waste_menu_item wmi
            right join
                menu m
                on
                    m.kode_menu = wmi.menu_kode
            right join
                waste_menu wm
                on
                    wm.id = wmi.id_header
            where
                wm.tanggal = '".$tanggal."' and
                wm.branch_kode = '".$branch_kode."'
        ";
        $d_wm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_wm->count() > 0 ) {
            $data = $d_wm->toArray();
        }

        return $data;;
    }

    public function excryptParamsExportPdf()
    {
        $params = $this->input->post('params');

        try {
            $paramsEncrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = array('data' => $paramsEncrypt);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportPdf($_params)
    {
        $this->load->library('PDFGenerator');

        $_data_params = json_decode( exDecrypt( $_params ), true );

        $params = array(
            'tanggal' => $_data_params['date'],
            'branch_kode' => $_data_params['branch_kode'][0]
        );

        $data = $this->mappingDataExportPdf( $params );

        $content['branch'] = $_data_params['branch_kode'][0];
        $content['tanggal'] = $_data_params['date'];
        $content['nama_user'] = $this->userdata['detail_user']['nama_detuser'];
        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'exportPdf', $content, true);

        // echo $html;

        $this->pdfgenerator->generate($html, "MENU GAGAL", 'a4', 'landscape');
    }
}
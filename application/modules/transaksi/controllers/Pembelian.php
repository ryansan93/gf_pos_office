<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends Public_Controller {

    private $pathView = 'transaksi/pembelian/';
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
                "assets/transaksi/pembelian/js/pembelian.js",
            ));
            $this->add_external_css(array(
                "assets/transaksi/pembelian/css/pembelian.css",
            ));

            $data = $this->includes;

            // $m_item = new \Model\Storage\Item_model();
            // $d_item = $m_item->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Pembelian Barang';

            // Load Indexx
            $data['title_menu'] = 'Pembelian Barang';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else if ( !empty($id) && !empty($resubmit) ) {
            $html = $this->editForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['group'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function getSupplier()
    {
        $m_supl = new \Model\Storage\Supplier_model();
        $d_supl = $m_supl->orderBy('nama', 'asc')->get();

        $data_supl = null;
        if ( $d_supl->count() > 0 ) {
            $data_supl = $d_supl->toArray();
        }

        return $data_supl;
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data_branch = null;
        if ( $d_branch->count() > 0 ) {
            $data_branch = $d_branch->toArray();
        }

        return $data_branch;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $tgl_stok_opname = $this->config->item('tgl_stok_opname');

        $m_beli = new \Model\Storage\Beli_model();
        $d_beli = $m_beli->where('tgl_beli', '>=', $tgl_stok_opname)->with(['supplier', 'branch'])->orderBy('tgl_beli', 'desc')->get();

        $data = null;
        if ( $d_beli->count() > 0 ) {
            $data = $d_beli->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view('transaksi/pembelian/list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_beli = new \Model\Storage\Beli_model();
        $d_beli = $m_beli->where('kode_beli', $kode)->with(['supplier', 'branch', 'detail'])->first()->toArray();

        $content['data'] = $d_beli;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['data_item'] = $this->getItem();
        $content['supplier'] = $this->getSupplier();
        $content['branch'] = $this->getBranch();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = json_decode($this->input->post('data'),TRUE);
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;

        try {
            $path_name = null;
            if ( !empty($file) ) {
                $moved = uploadFile($file);
                if ( $moved ) {
                    $path_name = $moved['path'];
                }
            }
            
            $m_beli = new \Model\Storage\Beli_model();

            $kode_beli = $m_beli->getNextIdRibuan();

            $m_beli->kode_beli = $kode_beli;
            $m_beli->tgl_beli = $params['tgl_beli'];
            $m_beli->supplier_kode = $params['supplier'];
            $m_beli->branch_kode = $params['branch'];
            $m_beli->nama_pic = $params['nama_pic'];
            $m_beli->total = $params['total'];
            $m_beli->lampiran = $path_name;
            $m_beli->keterangan = $params['keterangan'];
            $m_beli->no_faktur = $params['no_faktur'];
            $m_beli->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_belii = new \Model\Storage\BeliItem_model();
                $m_belii->beli_kode = $kode_beli;
                $m_belii->item_kode = $v_det['item_kode'];
                $m_belii->jumlah = $v_det['jumlah'];
                $m_belii->harga = $v_det['harga'];
                $m_belii->total = $v_det['total'];
                $m_belii->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_beli, $deskripsi_log, $kode_beli );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_beli);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function editForm($kode)
    {
        $m_beli = new \Model\Storage\Beli_model();
        $d_beli = $m_beli->where('kode_beli', $kode)->with(['detail'])->first()->toArray();

        $content['data'] = $d_beli;
        $content['data_item'] = $this->getItem();
        $content['supplier'] = $this->getSupplier();
        $content['branch'] = $this->getBranch();

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        return $html;
    }

    public function edit()
    {
        $params = json_decode($this->input->post('data'),TRUE);
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;

        try {
            $path_name = null;
            if ( !empty($file) ) {
                $moved = uploadFile($file);
                if ( $moved ) {
                    $path_name = $moved['path'];
                }
            }

            $kode_beli = $params['kode_beli'];

            $m_beli = new \Model\Storage\Beli_model();
            $_d_beli = $m_beli->where('kode_beli', $kode_beli)->first();

            if ( empty($path_name) ) {
                $path_name = $_d_beli->lampiran;
            }

            $m_beli->where('kode_beli', $kode_beli)->update(
                array(
                    'tgl_beli' => $params['tgl_beli'],
                    'supplier_kode' => $params['supplier'],
                    'branch_kode' => $params['branch'],
                    'nama_pic' => $params['nama_pic'],
                    'total' => $params['total'],
                    'lampiran' => $path_name,
                    'keterangan' => $params['keterangan'],
                    'no_faktur' => $params['no_faktur']
                )
            );

            $m_belii = new \Model\Storage\BeliItem_model();
            $m_belii->where('beli_kode', $kode_beli)->delete();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_belii = new \Model\Storage\BeliItem_model();
                $m_belii->beli_kode = $kode_beli;
                $m_belii->item_kode = $v_det['item_kode'];
                $m_belii->jumlah = $v_det['jumlah'];
                $m_belii->harga = $v_det['harga'];
                $m_belii->total = $v_det['total'];
                $m_belii->save();
            }

            $d_beli = $m_beli->where('kode_beli', $kode_beli)->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_beli, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_beli);
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
            $m_beli = new \Model\Storage\Beli_model();
            $d_beli = $m_beli->where('kode_beli', $kode)->first();

            $m_beli->where('kode_beli', $kode)->delete();
            $m_belii = new \Model\Storage\BeliItem_model();
            $m_belii->where('beli_kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_beli, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
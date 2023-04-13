<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan extends Public_Controller {

    private $pathView = 'transaksi/penerimaan/';
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
                "assets/transaksi/penerimaan/js/penerimaan.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/penerimaan/css/penerimaan.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Penerimaan Barang';

            // Load Indexx
            $data['title_menu'] = 'Penerimaan Barang';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['satuan'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function getGudang()
    {
        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gudang->count() > 0 ) {
            $data = $d_gudang->toArray();
        }

        return $data;
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

    public function getLists()
    {
        $params = $this->input->get('params');

        $tgl_stok_opname = $this->config->item('tgl_stok_opname');

        $m_terima = new \Model\Storage\Terima_model();
        $d_terima = $m_terima->where('tgl_terima', '>', $tgl_stok_opname)->with(['gudang'])->orderBy('tgl_terima', 'desc')->get();

        $data = null;
        if ( $d_terima->count() > 0 ) {
            $data = $d_terima->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_terima = new \Model\Storage\Terima_model();
        $d_terima = $m_terima->where('kode_terima', $kode)->with(['gudang', 'detail'])->first();

        $data = null;
        if ( $d_terima ) {
            $data = $d_terima->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {            
            $m_terima = new \Model\Storage\Terima_model();
            $now = $m_terima->getDate();

            $kode_terima = $m_terima->getNextIdRibuan();
            $no_invoice = $m_terima->getNextNoInvoice();

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$params['tgl_terima']."'";

            $d_conf = $conf->hydrateRaw($sql);

            $m_terima->kode_terima = $kode_terima;
            $m_terima->tgl_terima = $params['tgl_terima'];
            $m_terima->no_faktur = $no_invoice;
            $m_terima->supplier = $params['supplier'];
            $m_terima->pic = $params['nama_pic'];
            $m_terima->gudang_kode = $params['gudang'];
            $m_terima->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_terimai = new \Model\Storage\TerimaItem_model();
                $m_terimai->terima_kode = $kode_terima;
                $m_terimai->item_kode = $v_det['item_kode'];
                $m_terimai->harga = $v_det['harga'];
                $m_terimai->jumlah_terima = $v_det['jumlah_terima'];
                $m_terimai->satuan = $v_det['satuan'];
                $m_terimai->pengali = $v_det['pengali'];
                $m_terimai->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_terima, $deskripsi_log, $kode_terima );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_terima);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungStok()
    {
        $params = $this->input->post('params');

        try {
            $kode = $params['kode'];

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_tambah_stok @kode = '".$kode."', @table = 'terima'";

            $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $m_terima = new \Model\Storage\Terima_model();
        $no_invoice = $m_terima->getNextNoInvoice();

        cetak_r( $no_invoice );
    }
}
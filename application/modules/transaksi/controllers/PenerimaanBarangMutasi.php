<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PenerimaanBarangMutasi extends Public_Controller {

    private $pathView = 'transaksi/penerimaan_barang_mutasi/';
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
                "assets/transaksi/penerimaan_barang_mutasi/js/penerimaan-barang-mutasi.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/penerimaan_barang_mutasi/css/penerimaan-barang-mutasi.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['title_panel'] = 'Penerimaan Barang Mutasi';

            // Load Indexx
            $data['title_menu'] = 'Penerimaan Barang Mutasi';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
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

        $start_date = ($params['start_date'] >= $tgl_stok_opname) ? $params['start_date'] : $tgl_stok_opname;
        $end_date = $params['end_date'];

        $m_mutasi = new \Model\Storage\Mutasi_model();
        $d_mutasi = $m_mutasi->whereBetween('tgl_mutasi', [$start_date, $end_date])->with(['gudang_asal', 'gudang_tujuan'])->orderBy('tgl_mutasi', 'desc')->get();

        $data = null;
        if ( $d_mutasi->count() > 0 ) {
            $data = $d_mutasi->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_mutasi = new \Model\Storage\Mutasi_model();
        $d_mutasi = $m_mutasi->where('kode_mutasi', $kode)->with(['gudang_asal', 'gudang_tujuan', 'detail'])->first();

        $data = null;
        if ( $d_mutasi ) {
            $data = $d_mutasi->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function approve()
    {
        $kode_mutasi = $this->input->post('kode_mutasi');

        try {
            $m_mutasi = new \Model\Storage\Mutasi_model();
            $now = $m_mutasi->getDate();

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$now['waktu']."'";

            $m_mutasi->where('kode_mutasi', $kode_mutasi)->update(
                array(
                    'g_status' => getStatus('approve')
                )
            );

            $d_mutasi = $m_mutasi->where('kode_mutasi', $kode_mutasi)->first();

            $deskripsi_log = 'di-terima oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_mutasi, $deskripsi_log, $kode_mutasi );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_mutasi);
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
            $sql = "EXEC sp_tambah_stok @kode = '".$kode."', @table = 'mutasi'";

            $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di terima.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
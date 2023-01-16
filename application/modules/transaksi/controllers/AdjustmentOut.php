<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdjustmentOut extends Public_Controller {

    private $pathView = 'transaksi/adjustment_out/';
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
                "assets/jquery/list.min.js",
                "assets/transaksi/adjustment_out/js/adjustment-out.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/adjustment_out/css/adjustment-out.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $r_content['gudang'] = $this->getGudang();
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', $r_content, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Adjustment Out';

            // Load Indexx
            $data['title_menu'] = 'Adjustment Out';
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

        $m_adjout = new \Model\Storage\Adjout_model();
        $d_adjout = $m_adjout->whereBetween('tgl_adjout', [$start_date, $end_date])->whereIn('gudang_kode', $params['gudang_kode'])->with(['gudang'])->orderBy('kode_adjout', 'desc')->get();

        $data = null;
        if ( $d_adjout->count() > 0 ) {
            $data = $d_adjout->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_adjout = new \Model\Storage\Adjout_model();
        $d_adjout = $m_adjout->where('kode_adjout', $kode)->with(['gudang', 'detail', 'logs'])->first();

        $data = null;
        if ( $d_adjout ) {
            $data = $d_adjout->toArray();
        }

        $content['akses'] = $this->hakAkses;
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
            $m_adjout = new \Model\Storage\Adjout_model();
            $now = $m_adjout->getDate();

            $kode_adjout = $m_adjout->getNextIdRibuan();

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$now['waktu']."'";

            $m_adjout->kode_adjout = $kode_adjout;
            $m_adjout->tgl_adjout = $params['tgl_adjust'];
            $m_adjout->gudang_kode = $params['gudang'];
            $m_adjout->keterangan = $params['keterangan'];
            $m_adjout->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_adjouti = new \Model\Storage\AdjoutItem_model();
                $m_adjouti->adjout_kode = $kode_adjout;
                $m_adjouti->item_kode = $v_det['item_kode'];
                $m_adjouti->jumlah = $v_det['jumlah'];
                $m_adjouti->satuan = $v_det['satuan'];
                $m_adjouti->pengali = $v_det['pengali'];
                $m_adjouti->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_adjout, $deskripsi_log, $kode_adjout );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_adjout);
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
            $sql = "EXEC sp_kurang_stok @kode = '".$kode."', @table = 'adjout'";

            $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
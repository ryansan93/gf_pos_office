<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdjustmentIn extends Public_Controller {

    private $pathView = 'transaksi/adjustment_in/';
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
                "assets/transaksi/adjustment_in/js/adjustment-in.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/adjustment_in/css/adjustment-in.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $r_content['gudang'] = $this->getGudang();
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', $r_content, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Adjustment In';

            // Load Indexx
            $data['title_menu'] = 'Adjustment In';
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

        $m_adjin = new \Model\Storage\Adjin_model();
        $d_adjin = $m_adjin->whereBetween('tgl_adjin', [$start_date, $end_date])->whereIn('gudang_kode', $params['gudang_kode'])->with(['gudang'])->orderBy('tgl_adjin', 'desc')->get();

        $data = null;
        if ( $d_adjin->count() > 0 ) {
            $data = $d_adjin->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_adjin = new \Model\Storage\Adjin_model();
        $d_adjin = $m_adjin->where('kode_adjin', $kode)->with(['gudang', 'detail', 'logs'])->first();

        $data = null;
        if ( $d_adjin ) {
            $data = $d_adjin->toArray();
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
            
            $m_adjin = new \Model\Storage\Adjin_model();
            $now = $m_adjin->getDate();

            $kode_adjin = $m_adjin->getNextIdRibuan();

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$params['tgl_adjust']."'";

            $d_conf = $conf->hydrateRaw($sql);

            $m_adjin->kode_adjin = $kode_adjin;
            $m_adjin->tgl_adjin = $params['tgl_adjust'];
            $m_adjin->gudang_kode = $params['gudang'];
            $m_adjin->keterangan = $params['keterangan'];
            $m_adjin->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_adjini = new \Model\Storage\AdjinItem_model();
                $m_adjini->adjin_kode = $kode_adjin;
                $m_adjini->item_kode = $v_det['item_kode'];
                $m_adjini->jumlah = $v_det['jumlah'];
                $m_adjini->harga = $v_det['harga'];
                $m_adjini->satuan = $v_det['satuan'];
                $m_adjini->pengali = $v_det['pengali'];
                $m_adjini->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_adjin, $deskripsi_log, $kode_adjin );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_adjin);
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

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_tambah_stok @kode = '".$kode."', @table = 'adjin'";

            $m_conf = new \Model\Storage\Conf();

            $tgl_transaksi = null;
            $gudang = null;
            $barang = null;

            $sql_tgl_dan_gudang = "
                select a.* from adjin a
                where
                    a.kode_adjin = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                $tgl_transaksi = $d_tgl_dan_gudang['tgl_adjin'];
                $gudang = $d_tgl_dan_gudang['gudang_kode'];
            }

            $sql_barang = "
                select a.tgl_adjin, ai.item_kode from adjin_item ai
                right join
                    adjin a
                    on
                        a.kode_adjin = ai.adjin_kode
                where
                    ai.adjin_kode = '".$kode."'
                group by
                    a.tgl_adjin,
                    ai.item_kode
            ";
            $d_barang = $m_conf->hydrateRaw( $sql_barang );
            if ( $d_barang->count() > 0 ) {
                $d_barang = $d_barang->toArray();

                foreach ($d_barang as $key => $value) {
                    $barang[] = $value['item_kode'];
                }
            }

            $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

            $d_conf = $m_conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $kode = 'AI23020001';

        $m_conf = new \Model\Storage\Conf();

        $tgl_transaksi = null;
        $gudang = null;
        $barang = null;

        $sql_tgl_dan_gudang = "
            select a.* from adjin a
            where
                a.kode_adjin = '".$kode."'
        ";
        $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
        if ( $d_tgl_dan_gudang->count() > 0 ) {
            $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
            $tgl_transaksi = $d_tgl_dan_gudang['tgl_adjin'];
            $gudang = $d_tgl_dan_gudang['gudang_kode'];
        }

        $sql_barang = "
            select a.tgl_adjin, ai.item_kode from adjin_item ai
            right join
                adjin a
                on
                    a.kode_adjin = ai.adjin_kode
            where
                ai.adjin_kode = '".$kode."'
            group by
                a.tgl_adjin,
                ai.item_kode
        ";
        $d_barang = $m_conf->hydrateRaw( $sql_barang );
        if ( $d_barang->count() > 0 ) {
            $d_barang = $d_barang->toArray();

            foreach ($d_barang as $key => $value) {
                $barang[] = $value['item_kode'];
            }
        }

        cetak_r( $tgl_transaksi );
        cetak_r( $gudang );
        cetak_r( $barang );

        // $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";
    }
}
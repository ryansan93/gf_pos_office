<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StokOpname extends Public_Controller {

    private $pathView = 'transaksi/stok_opname/';
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
                "assets/transaksi/stok_opname/js/stok-opname.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/stok_opname/css/stok-opname.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Stok Opname';

            $r_content['gudang'] = $this->getGudang();
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', $r_content, TRUE);

            // Load Indexx
            $data['title_menu'] = 'Stok Opname';
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

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $gudang_kode = $params['gudang_kode'];

        $m_so = new \Model\Storage\StokOpname_model();
        $sql = "
            select so.id, so.tanggal, g.nama from stok_opname so
            right join
                gudang g
                on
                    so.gudang_kode = g.kode_gudang
            where
                so.tanggal between '".$start_date."' and '".$end_date."' and
                g.kode_gudang in ('".implode("', '", $gudang_kode)."')
            order by
                so.tanggal desc,
                g.nama asc
        ";
        $d_so = $m_so->hydrateRaw( $sql );

        $data = null;
        if ( $d_so->count() > 0 ) {
            $data = $d_so->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function addForm()
    {
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function viewForm($id)
    {
        $m_so = new \Model\Storage\StokOpname_model();
        $d_so = $m_so->where('id', $id)->with(['detail', 'gudang'])->first();

        $data = null;
        if ( $d_so ) {
            $data = $d_so->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_so = new \Model\Storage\StokOpname_model();

            $kode_stok_opname = $m_so->getNextIdRibuan();

            $m_so->tanggal = $params['tanggal'];
            $m_so->gudang_kode = $params['gudang_kode'];
            $m_so->kode_stok_opname = $kode_stok_opname;
            $m_so->save();

            foreach ($params['list_item'] as $k_li => $v_li) {
                $m_sod = new \Model\Storage\StokOpnameDet_model();
                $m_sod->id_header = $m_so->id;
                $m_sod->item_kode = $v_li['item_kode'];
                $m_sod->satuan = $v_li['satuan'];
                $m_sod->pengali = $v_li['pengali'];
                $m_sod->jumlah = $v_li['jumlah'];
                $m_sod->harga = $v_li['harga'];
                $m_sod->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_so, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['content'] = array('kode' => $kode_stok_opname);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungStokOpname()
    {
        $params = $this->input->post('params');

        try {
            $kode = $params['kode'];

            $conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_stok_opname @kode = '$kode'";

            $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
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

    public function getListItem()
    {
        $params = $this->input->get('params');

        $tanggal = $params['tanggal'];
        $gudang_kode = $params['gudang_kode'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                i.*,
                sh.harga 
            from item i
            left join
                (
                    select sh.* from stok_harga sh
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = '".$gudang_kode."' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            sh.id_header = st.id
                ) sh
                on
                    i.kode = sh.item_kode
            order by
                i.nama asc
        ";
        $d_item = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_item->count() > 0 ) {
            $d_item = $d_item->toArray();

            $idx = 0;
            foreach ($d_item as $k_item => $v_item) {
                $m_satuan = new \Model\Storage\ItemSatuan_model();
                $d_satuan = $m_satuan->where('item_kode', $v_item['kode'])->get();

                $data[ $idx ] = $v_item;
                $data[ $idx ]['satuan'] = ( $d_satuan->count() > 0 ) ? $d_satuan->toArray() : null;

                $idx++;
            }
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'listItem', $content, true);

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

    public function injekHargaItem($tanggal, $kode_gudang)
    {
        $data = array(
                    array('kode_brg' => 'FD00001', 'harga' => 320.00),
                    array('kode_brg' => 'FD00002', 'harga' => 110.00),
                    array('kode_brg' => 'FD00003', 'harga' => 30.00),
                    array('kode_brg' => 'FD00004', 'harga' => 85.00),
                    array('kode_brg' => 'FD00005', 'harga' => 49.00),
                    array('kode_brg' => 'FD00006', 'harga' => 12.00),
                    array('kode_brg' => 'FD00007', 'harga' => 35.00),
                    array('kode_brg' => 'FD00008', 'harga' => 45.00),
                    array('kode_brg' => 'FD00009', 'harga' => 750.00),
                    array('kode_brg' => 'FD00010', 'harga' => 260.00),
                    array('kode_brg' => 'FD00011', 'harga' => 265.00),
                    array('kode_brg' => 'FD00012', 'harga' => 265.00),
                    array('kode_brg' => 'FD00013', 'harga' => 156.00),
                    array('kode_brg' => 'FD00014', 'harga' => 262.50),
                    array('kode_brg' => 'FD00015', 'harga' => 85.00),
                    array('kode_brg' => 'FD00016', 'harga' => 66.00),
                    array('kode_brg' => 'FD00017', 'harga' => 30.00),
                    array('kode_brg' => 'FD00018', 'harga' => 24.00),
                    array('kode_brg' => 'FD00019', 'harga' => 892.86),
                    array('kode_brg' => 'FD00020', 'harga' => 56.45),
                    array('kode_brg' => 'FD00021', 'harga' => 67.20),
                    array('kode_brg' => 'FD00022', 'harga' => 14.00),
                    array('kode_brg' => 'FD00023', 'harga' => 12.00),
                    array('kode_brg' => 'FD00024', 'harga' => 56.00),
                    array('kode_brg' => 'FD00025', 'harga' => 20.00),
                    array('kode_brg' => 'FD00026', 'harga' => 15.00),
                    array('kode_brg' => 'FD00027', 'harga' => 51.76),
                    array('kode_brg' => 'FD00028', 'harga' => 120.00),
                    array('kode_brg' => 'FD00029', 'harga' => 1540.00),
                    array('kode_brg' => 'FD00030', 'harga' => 110.00),
                    array('kode_brg' => 'FD00031', 'harga' => 775.00),
                    array('kode_brg' => 'FD00032', 'harga' => 35.00),
                    array('kode_brg' => 'FD00033', 'harga' => 15.00),
                    array('kode_brg' => 'FD00034', 'harga' => 90.00),
                    array('kode_brg' => 'FD00035', 'harga' => 38.04),
                    array('kode_brg' => 'FD00036', 'harga' => 675.70),
                    array('kode_brg' => 'FD00037', 'harga' => 65.00),
                    array('kode_brg' => 'FD00038', 'harga' => 18.00),
                    array('kode_brg' => 'FD00039', 'harga' => 26.50),
                    array('kode_brg' => 'FD00040', 'harga' => 104.00),
                    array('kode_brg' => 'FD00041', 'harga' => 32.00),
                    array('kode_brg' => 'FD00042', 'harga' => 34.00),
                    array('kode_brg' => 'FD00043', 'harga' => 14.00),
                    array('kode_brg' => 'FD00044', 'harga' => 27.00),
                    array('kode_brg' => 'FD00045', 'harga' => 24.00),
                    array('kode_brg' => 'FD00046', 'harga' => 427.27),
                    array('kode_brg' => 'FD00047', 'harga' => 1250.00),
                    array('kode_brg' => 'FD00048', 'harga' => 75.00),
                    array('kode_brg' => 'FD00049', 'harga' => 42.00),
                    array('kode_brg' => 'FD00050', 'harga' => 102.50),
                    array('kode_brg' => 'FD00051', 'harga' => 47.50),
                    array('kode_brg' => 'FD00052', 'harga' => 14.55),
                    array('kode_brg' => 'FD00053', 'harga' => 78.00),
                    array('kode_brg' => 'FD00054', 'harga' => 143.90),
                    array('kode_brg' => 'FD00055', 'harga' => 91.70),
                    array('kode_brg' => 'FD00056', 'harga' => 14.25),
                    array('kode_brg' => 'FD00057', 'harga' => 81.80),
                    array('kode_brg' => 'FD00058', 'harga' => 66.90),
                    array('kode_brg' => 'FD00059', 'harga' => 67.00),
                    array('kode_brg' => 'FD00060', 'harga' => 53.50),
                    array('kode_brg' => 'FD00061', 'harga' => 10.00),
                    array('kode_brg' => 'FD00062', 'harga' => 15.00),
                    array('kode_brg' => 'FD00063', 'harga' => 14.00),
                    array('kode_brg' => 'FD00064', 'harga' => 35.00),
                    array('kode_brg' => 'FD00065', 'harga' => 13.00),
                    array('kode_brg' => 'FD00066', 'harga' => 12.40),
                    array('kode_brg' => 'FD00067', 'harga' => 10.00),
                    array('kode_brg' => 'FD00068', 'harga' => 15.25),
                    array('kode_brg' => 'FD00069', 'harga' => 24.00),
                    array('kode_brg' => 'FD00070', 'harga' => 24.00),
                    array('kode_brg' => 'FD00071', 'harga' => 19.50),
                    array('kode_brg' => 'FD00072', 'harga' => 128.75),
                    array('kode_brg' => 'FD00073', 'harga' => 280.00),
                    array('kode_brg' => 'FD00074', 'harga' => 20.00),
                    array('kode_brg' => 'FD00075', 'harga' => 42.22),
                    array('kode_brg' => 'FD00076', 'harga' => 198.00),
                    array('kode_brg' => 'FD00077', 'harga' => 25.00),
                    array('kode_brg' => 'FD00078', 'harga' => 146.00),
                    array('kode_brg' => 'FD00079', 'harga' => 50.00),
                    array('kode_brg' => 'FD00080', 'harga' => 13.50),
                    array('kode_brg' => 'FD00081', 'harga' => 26.00),
                    array('kode_brg' => 'FD00082', 'harga' => 31.00),
                    array('kode_brg' => 'FD00083', 'harga' => 47.50),
                    array('kode_brg' => 'FD00084', 'harga' => 40.00),
                    array('kode_brg' => 'FD00085', 'harga' => 29.00),
                    array('kode_brg' => 'FD00086', 'harga' => 21.50),
                    array('kode_brg' => 'FD00087', 'harga' => 78.00),
                    array('kode_brg' => 'FD00088', 'harga' => 70.27),
                    array('kode_brg' => 'FD00089', 'harga' => 66.00),
                    array('kode_brg' => 'FD00090', 'harga' => 105.00),
                    array('kode_brg' => 'FD00091', 'harga' => 140.00),
                    array('kode_brg' => 'FD00092', 'harga' => 28.00),
                    array('kode_brg' => 'FD00093', 'harga' => 234.37),
                    array('kode_brg' => 'FD00094', 'harga' => 186.50),
                    array('kode_brg' => 'FD00095', 'harga' => 27.50),
                    array('kode_brg' => 'FD00096', 'harga' => 25.00),
                    array('kode_brg' => 'FD00097', 'harga' => 17.00),
                    array('kode_brg' => 'FD00098', 'harga' => 85.00),
                    array('kode_brg' => 'FD00099', 'harga' => 27.00),
                    array('kode_brg' => 'FD00100', 'harga' => 20.00),
                    array('kode_brg' => 'FD00101', 'harga' => 48.00),
                    array('kode_brg' => 'FD00102', 'harga' => 8000.00),
                    array('kode_brg' => 'FD00103', 'harga' => 190.00),
                    array('kode_brg' => 'FD00104', 'harga' => 1080.00),
                    array('kode_brg' => 'FD00105', 'harga' => 46.00),
                    array('kode_brg' => 'FD00106', 'harga' => 35.00),
                    array('kode_brg' => 'FD00107', 'harga' => 12.00),
                    array('kode_brg' => 'FD00108', 'harga' => 210.00),
                    array('kode_brg' => 'FD00109', 'harga' => 88.00),
                    array('kode_brg' => 'FD00110', 'harga' => 74.20),
                    array('kode_brg' => 'FD00111', 'harga' => 49.00),
                    array('kode_brg' => 'FD00112', 'harga' => 62.00),
                    array('kode_brg' => 'FD00113', 'harga' => 72.10),
                    array('kode_brg' => 'FD00114', 'harga' => 147.50),
                    array('kode_brg' => 'FD00115', 'harga' => 53.40),
                    array('kode_brg' => 'FD00116', 'harga' => 214.78),
                    array('kode_brg' => 'FD00117', 'harga' => 47.00),
                    array('kode_brg' => 'FD00118', 'harga' => 57.50),
                    array('kode_brg' => 'FD00119', 'harga' => 234.38),
                    array('kode_brg' => 'FD00120', 'harga' => 35.00),
                    array('kode_brg' => 'FD00121', 'harga' => 80.00),
                    array('kode_brg' => 'FD00122', 'harga' => 340.00),
                    array('kode_brg' => 'FD00123', 'harga' => 513.64),
                    array('kode_brg' => 'FD00124', 'harga' => 231.08),
                    array('kode_brg' => 'FD00125', 'harga' => 165.00),
                    array('kode_brg' => 'FD00126', 'harga' => 86.00),
                    array('kode_brg' => 'FD00127', 'harga' => 74.80),
                    array('kode_brg' => 'FD00128', 'harga' => 75.00),
                    array('kode_brg' => 'FD00129', 'harga' => 3818.18),
                    array('kode_brg' => 'FD00130', 'harga' => 44.90),
                    array('kode_brg' => 'FD00131', 'harga' => 62.07),
                    array('kode_brg' => 'FD00132', 'harga' => 86.21),
                    array('kode_brg' => 'FD00133', 'harga' => 41.18),
                    array('kode_brg' => 'FD00134', 'harga' => 25.00),
                    array('kode_brg' => 'FD00135', 'harga' => 174.00),
                    array('kode_brg' => 'FD00136', 'harga' => 70.00),
                    array('kode_brg' => 'FD00137', 'harga' => 136.00),
                    array('kode_brg' => 'FD00138', 'harga' => 96.67),
                    array('kode_brg' => 'FD00139', 'harga' => 49.00),
                    array('kode_brg' => 'FD00140', 'harga' => 51.00),
                    array('kode_brg' => 'FD00141', 'harga' => 75.00),
                    array('kode_brg' => 'FD00142', 'harga' => 50.00),
                    array('kode_brg' => 'FD00143', 'harga' => 29.00),
                    array('kode_brg' => 'FD00144', 'harga' => 75.00),
                    array('kode_brg' => 'FD00145', 'harga' => 95.00),
                    array('kode_brg' => 'FD00146', 'harga' => 85.00),
                    array('kode_brg' => 'FD00147', 'harga' => 220.00),
                    array('kode_brg' => 'FD00148', 'harga' => 145.00),
                    array('kode_brg' => 'FD00149', 'harga' => 120.00),
                    array('kode_brg' => 'FD00150', 'harga' => 112.00),
                    array('kode_brg' => 'FD00151', 'harga' => 85.00),
                    array('kode_brg' => 'FD00152', 'harga' => 85.00),
                    array('kode_brg' => 'FD00153', 'harga' => 75.00),
                    array('kode_brg' => 'FD00154', 'harga' => 90.00),
                    array('kode_brg' => 'FD00155', 'harga' => 62.50),
                    array('kode_brg' => 'FD00156', 'harga' => 95.00),
                    array('kode_brg' => 'FD00157', 'harga' => 105.00),
                    array('kode_brg' => 'FD00158', 'harga' => 481.00),
                    array('kode_brg' => 'FD00159', 'harga' => 25.00),
                    array('kode_brg' => 'FD00160', 'harga' => 10.00),
                    array('kode_brg' => 'FD00161', 'harga' => 24.33),
                    array('kode_brg' => 'FD00162', 'harga' => 20.00),
                    array('kode_brg' => 'FD00163', 'harga' => 3.00),
                    array('kode_brg' => 'FD00164', 'harga' => 15.00),
                    array('kode_brg' => 'FD00165', 'harga' => 200.00),
                    array('kode_brg' => 'FD00166', 'harga' => 37.50),
                    array('kode_brg' => 'FD00167', 'harga' => 60.00),
                    array('kode_brg' => 'FD00168', 'harga' => 8.50),
                    array('kode_brg' => 'FD00169', 'harga' => 2.00),
                    array('kode_brg' => 'FD00170', 'harga' => 13.00),
                    array('kode_brg' => 'FD00171', 'harga' => 9.00),
                    array('kode_brg' => 'FD00172', 'harga' => 3.00),
                    array('kode_brg' => 'FD00173', 'harga' => 8.00),
                    array('kode_brg' => 'FD00174', 'harga' => 10.00),
                    array('kode_brg' => 'FD00175', 'harga' => 12.00),
                    array('kode_brg' => 'FD00176', 'harga' => 25.00),
                    array('kode_brg' => 'FD00177', 'harga' => 12.50),
                    array('kode_brg' => 'FD00178', 'harga' => 155.04),
                    array('kode_brg' => 'FD00179', 'harga' => 185.00),
                    array('kode_brg' => 'FD00180', 'harga' => 70.00),
                    array('kode_brg' => 'FD00181', 'harga' => 180.00),
                    array('kode_brg' => 'FD00182', 'harga' => 8.00),
                    array('kode_brg' => 'FD00183', 'harga' => 23.00),
                    array('kode_brg' => 'FD00184', 'harga' => 33.50),
                    array('kode_brg' => 'FD00185', 'harga' => 17.30),
                    array('kode_brg' => 'FD00186', 'harga' => 148.25),
                    array('kode_brg' => 'FD00187', 'harga' => 52.41),
                    array('kode_brg' => 'FD00188', 'harga' => 15.84),
                    array('kode_brg' => 'FD00189', 'harga' => 66.67),
                    array('kode_brg' => 'FD00190', 'harga' => 10000.00),
                    array('kode_brg' => 'FD00191', 'harga' => 8.00),
                    array('kode_brg' => 'FD00192', 'harga' => 260.00),
                    array('kode_brg' => 'FD00193', 'harga' => 37.84),
                    array('kode_brg' => 'FD00194', 'harga' => 25.00),
                    array('kode_brg' => 'FD00195', 'harga' => 16.09),
                    array('kode_brg' => 'FD00196', 'harga' => 12.83),
                    array('kode_brg' => 'FD00197', 'harga' => 47.00),
                    array('kode_brg' => 'FD00198', 'harga' => 26.00),
                    array('kode_brg' => 'FD00199', 'harga' => 48.33),
                    array('kode_brg' => 'FD00200', 'harga' => 50.77),
                    array('kode_brg' => 'FD00201', 'harga' => 2679.33),
                    array('kode_brg' => 'FD00202', 'harga' => 12.50),
                    array('kode_brg' => 'FD00203', 'harga' => 26000.00),
                    array('kode_brg' => 'FD00204', 'harga' => 90.00),
                    array('kode_brg' => 'FD00205', 'harga' => 140.00),
                    array('kode_brg' => 'FD00206', 'harga' => 145.01),
                    array('kode_brg' => 'FD00207', 'harga' => 50.00),
                    array('kode_brg' => 'FD00208', 'harga' => 47.00),
                    array('kode_brg' => 'FD00209', 'harga' => 36.00),
                    array('kode_brg' => 'FD00210', 'harga' => 35.00),
                    array('kode_brg' => 'FD00211', 'harga' => 80.00),
                    array('kode_brg' => 'FD00212', 'harga' => 50.00),
                    array('kode_brg' => 'FD00213', 'harga' => 93.00),
                    array('kode_brg' => 'FD00214', 'harga' => 265.00),
                    array('kode_brg' => 'FD00215', 'harga' => 55.00),
                    array('kode_brg' => 'FD00216', 'harga' => 18.50),
                    array('kode_brg' => 'FD00217', 'harga' => 6.50),
                    array('kode_brg' => 'FD00218', 'harga' => 12.00),
                    array('kode_brg' => 'FD00219', 'harga' => 145.00),
                    array('kode_brg' => 'FD00220', 'harga' => 10.00),
                    array('kode_brg' => 'FD00221', 'harga' => 31.76),
                    array('kode_brg' => 'FD00222', 'harga' => 40.00),
                    array('kode_brg' => 'FD00223', 'harga' => 16.00),
                    array('kode_brg' => 'FD00224', 'harga' => 285.00),
                    array('kode_brg' => 'FD00225', 'harga' => 55.00),
                    array('kode_brg' => 'FD00226', 'harga' => 230.00),
                    array('kode_brg' => 'FD00227', 'harga' => 13.75),
                    array('kode_brg' => 'FD00228', 'harga' => 31.76),
                    array('kode_brg' => 'FD00229', 'harga' => 40.00),
                    array('kode_brg' => 'FD00230', 'harga' => 13.50),
                    array('kode_brg' => 'FD00231', 'harga' => 15.00),
                    array('kode_brg' => 'FD00232', 'harga' => 65.00),
                    array('kode_brg' => 'FD00233', 'harga' => 290.00),
                    array('kode_brg' => 'FD00234', 'harga' => 24.31),
                    array('kode_brg' => 'FD00235', 'harga' => 185.00),
                    array('kode_brg' => 'FD00236', 'harga' => 10.00),
                    array('kode_brg' => 'FD00237', 'harga' => 27.00),
                    array('kode_brg' => 'FD00238', 'harga' => 19.00),
                    array('kode_brg' => 'FD00239', 'harga' => 35.00),
                    array('kode_brg' => 'FD00240', 'harga' => 70.00),
                    array('kode_brg' => 'FD00241', 'harga' => 65.00),
                    array('kode_brg' => 'FD00242', 'harga' => 27.50),
                    array('kode_brg' => 'FD00243', 'harga' => 1487550.00),
                    array('kode_brg' => 'FD00244', 'harga' => 8.00),
                    array('kode_brg' => 'FD00245', 'harga' => 225.00),
                    array('kode_brg' => 'FD00246', 'harga' => 240.00),
                    array('kode_brg' => 'FD00247', 'harga' => 150.00),
                    array('kode_brg' => 'FD00248', 'harga' => 127.67),
                    array('kode_brg' => 'FD00249', 'harga' => 10.42),
                    array('kode_brg' => 'FD00250', 'harga' => 13.60),
                    array('kode_brg' => 'FD00251', 'harga' => 38.40),
                    array('kode_brg' => 'FD00252', 'harga' => 19.90),
                    array('kode_brg' => 'FD00253', 'harga' => 24.40),
                    array('kode_brg' => 'FD00254', 'harga' => 19.50),
                    array('kode_brg' => 'FD00255', 'harga' => 12.50),
                    array('kode_brg' => 'FD00256', 'harga' => 119.19),
                    array('kode_brg' => 'FD00257', 'harga' => 141.20),
                    array('kode_brg' => 'FD00258', 'harga' => 9500.00),
                    array('kode_brg' => 'FD00259', 'harga' => 12.50),
                    array('kode_brg' => 'FD00260', 'harga' => 54.00),
                    array('kode_brg' => 'FD00261', 'harga' => 25.00),
                    array('kode_brg' => 'FD00262', 'harga' => 15.00),
                    array('kode_brg' => 'FD00263', 'harga' => 35.82),
                    array('kode_brg' => 'FD00264', 'harga' => 40.00),
                    array('kode_brg' => 'FD00265', 'harga' => 11.67),
                    array('kode_brg' => 'FD00266', 'harga' => 244.44),
                    array('kode_brg' => 'FD00267', 'harga' => 12.50),
                    array('kode_brg' => 'FD00268', 'harga' => 10.00),
                    array('kode_brg' => 'FD00269', 'harga' => 10.00),
                    array('kode_brg' => 'FD00270', 'harga' => 35.00),
                    array('kode_brg' => 'FD00271', 'harga' => 2.30),
                    array('kode_brg' => 'FD00272', 'harga' => 160.00),
                    array('kode_brg' => 'FD00273', 'harga' => 55.00),
                    array('kode_brg' => 'FD00274', 'harga' => 20.00),
                    array('kode_brg' => 'FD00275', 'harga' => 10.00),
                    array('kode_brg' => 'FD00276', 'harga' => 8.00),
                    array('kode_brg' => 'FD00277', 'harga' => 6.00),
                    array('kode_brg' => 'FD00278', 'harga' => 9.00),
                    array('kode_brg' => 'FD00279', 'harga' => 12.00),
                    array('kode_brg' => 'FD00280', 'harga' => 30.00),
                    array('kode_brg' => 'FD00281', 'harga' => 156.25),
                    array('kode_brg' => 'FD00282', 'harga' => 77.00),
                    array('kode_brg' => 'FD00283', 'harga' => 92.00),
                    array('kode_brg' => 'FD00284', 'harga' => 68.75),
                    array('kode_brg' => 'FD00285', 'harga' => 40.00),
                    array('kode_brg' => 'FD00286', 'harga' => 101.85),
                    array('kode_brg' => 'FD00287', 'harga' => 1225.00),
                    array('kode_brg' => 'FD00288', 'harga' => 95.00),
                    array('kode_brg' => 'FD00289', 'harga' => 16.88),
                    array('kode_brg' => 'FD00290', 'harga' => 12.00),
                    array('kode_brg' => 'FD00291', 'harga' => 71.34),
                    array('kode_brg' => 'FD00292', 'harga' => 156.00),
                    array('kode_brg' => 'FD00293', 'harga' => 49.64),
                    array('kode_brg' => 'FD00294', 'harga' => 178.27),
                    array('kode_brg' => 'FD00295', 'harga' => 4.50),
                    array('kode_brg' => 'FD00296', 'harga' => 35.00),
                    array('kode_brg' => 'FD00297', 'harga' => 30.00),
                    array('kode_brg' => 'FD00298', 'harga' => 25.00),
                    array('kode_brg' => 'FD00299', 'harga' => 26.03),
                    array('kode_brg' => 'FD00300', 'harga' => 51.35),
                    array('kode_brg' => 'FD00301', 'harga' => 32.00),
                    array('kode_brg' => 'FD00302', 'harga' => 20.00),
                    array('kode_brg' => 'FD00303', 'harga' => 12.00),
                    array('kode_brg' => 'FD00304', 'harga' => 32.00),
                    array('kode_brg' => 'FD00305', 'harga' => 30.00),
                    array('kode_brg' => 'FD00306', 'harga' => 8.00),
                    array('kode_brg' => 'FD00307', 'harga' => 85.00),
                    array('kode_brg' => 'FD00308', 'harga' => 25.00),
                    array('kode_brg' => 'FD00309', 'harga' => 7.30),
                    array('kode_brg' => 'FD00310', 'harga' => 44.09),
                    array('kode_brg' => 'FD00311', 'harga' => 20.00),
                    array('kode_brg' => 'FD00312', 'harga' => 6.00),
                    array('kode_brg' => 'FD00313', 'harga' => 1100.00),
                    array('kode_brg' => 'FD00314', 'harga' => 20.00),
                    array('kode_brg' => 'FD00315', 'harga' => 95.00),
                    array('kode_brg' => 'FD00316', 'harga' => 55.00),
                    array('kode_brg' => 'FD00317', 'harga' => 8.00),
                    array('kode_brg' => 'FD00318', 'harga' => 54.86),
                    array('kode_brg' => 'FD00319', 'harga' => 1800.00),
                    array('kode_brg' => 'FD00320', 'harga' => 98.10),
                    array('kode_brg' => 'FD00321', 'harga' => 90.91),
                    array('kode_brg' => 'FD00322', 'harga' => 150.00),
                    array('kode_brg' => 'FD00323', 'harga' => 31.25),
                    array('kode_brg' => 'FD00324', 'harga' => 93.75),
                    array('kode_brg' => 'FD00325', 'harga' => 20.00),
                    array('kode_brg' => 'FD00326', 'harga' => 14.50),
                    array('kode_brg' => 'FD00327', 'harga' => 5.00),
                    array('kode_brg' => 'FD00328', 'harga' => 153.33),
                    array('kode_brg' => 'FD00329', 'harga' => 30.00),
                    array('kode_brg' => 'FD00330', 'harga' => 70.00),
                    array('kode_brg' => 'FD00331', 'harga' => 131.00),
                    array('kode_brg' => 'FD00332', 'harga' => 97.35),
                    array('kode_brg' => 'FD00333', 'harga' => 11.00),
                    array('kode_brg' => 'FD00334', 'harga' => 110.00),
                    array('kode_brg' => 'FD00335', 'harga' => 223.08),
                    array('kode_brg' => 'FD00336', 'harga' => 44.44),
                    array('kode_brg' => 'FD00337', 'harga' => 78.00),
                    array('kode_brg' => 'FD00338', 'harga' => 13.50),
                    array('kode_brg' => 'FD00339', 'harga' => 115.66),
                    array('kode_brg' => 'FD00340', 'harga' => 80.00),
                    array('kode_brg' => 'FD00341', 'harga' => 19465.95),
                    array('kode_brg' => 'FD00342', 'harga' => 45.00),
                    array('kode_brg' => 'FD00343', 'harga' => 109.38),
                    array('kode_brg' => 'FD00344', 'harga' => 45.45),
                    array('kode_brg' => 'FD00345', 'harga' => 175.00),
                    array('kode_brg' => 'FD00346', 'harga' => 113.97),
                    array('kode_brg' => 'FD00347', 'harga' => 94.17),
                    array('kode_brg' => 'FD00348', 'harga' => 34.29),
                    array('kode_brg' => 'FD00349', 'harga' => 54.05),
                    array('kode_brg' => 'FD00350', 'harga' => 368.64),
                    array('kode_brg' => 'FD00351', 'harga' => 35.00),
                    array('kode_brg' => 'FD00352', 'harga' => 4500.00),
                    array('kode_brg' => 'FD00353', 'harga' => 45.00),
                    array('kode_brg' => 'FD00354', 'harga' => 11.00),
                    array('kode_brg' => 'FD00355', 'harga' => 27.63),
                    array('kode_brg' => 'FD00356', 'harga' => 48.00),
                    array('kode_brg' => 'FD00357', 'harga' => 305.56),
                    array('kode_brg' => 'FD00358', 'harga' => 13.63),
                    array('kode_brg' => 'FD00359', 'harga' => 76.00),
                    array('kode_brg' => 'FD00360', 'harga' => 120.00),
                    array('kode_brg' => 'FD00361', 'harga' => 300.00),
                    array('kode_brg' => 'FD00362', 'harga' => 40.00),
                    array('kode_brg' => 'FD00363', 'harga' => 60.00),
                    array('kode_brg' => 'FD00364', 'harga' => 54.00),
                    array('kode_brg' => 'FD00365', 'harga' => 204.55),
                    array('kode_brg' => 'FD00366', 'harga' => 320.00),
                    array('kode_brg' => 'FD00367', 'harga' => 60.00),
                    array('kode_brg' => 'FD00368', 'harga' => 110.00),
                    array('kode_brg' => 'FD00369', 'harga' => 72.00),
                    array('kode_brg' => 'FD00370', 'harga' => 11.25),
                    array('kode_brg' => 'FD00371', 'harga' => 20.00),
                    array('kode_brg' => 'FD00372', 'harga' => 35.00),
                    array('kode_brg' => 'FD00373', 'harga' => 250.00),
                    array('kode_brg' => 'FD00374', 'harga' => 252.45),
                    array('kode_brg' => 'FD00375', 'harga' => 208.33),
                    array('kode_brg' => 'FD00376', 'harga' => 77.25),
                    array('kode_brg' => 'FD00377', 'harga' => 39375.00),
                    array('kode_brg' => 'FD00378', 'harga' => 25000.00),
                    array('kode_brg' => 'FD00379', 'harga' => 14000.00),
                    array('kode_brg' => 'FD00380', 'harga' => 80000.00),
                    array('kode_brg' => 'FD00381', 'harga' => 85000.00),
                    array('kode_brg' => 'FD00382', 'harga' => 45000.00),
                    array('kode_brg' => 'FD00383', 'harga' => 5000.00),
                    array('kode_brg' => 'FD00384', 'harga' => 55.00),
                    array('kode_brg' => 'FD00385', 'harga' => 44.80),
                    array('kode_brg' => 'FD00386', 'harga' => 48.64),
                    array('kode_brg' => 'FD00387', 'harga' => 95.00),
                    array('kode_brg' => 'FD00388', 'harga' => 50.00),
                    array('kode_brg' => 'FD00389', 'harga' => 519.46),
                    array('kode_brg' => 'FD00390', 'harga' => 3230.77),
                    array('kode_brg' => 'FD00391', 'harga' => 225.00),
                    array('kode_brg' => 'FD00392', 'harga' => 119.00),
                    array('kode_brg' => 'FD00393', 'harga' => 45.95),
                    array('kode_brg' => 'FD00394', 'harga' => 17.50),
                    array('kode_brg' => 'FD00395', 'harga' => 175.00),
                    array('kode_brg' => 'FD00396', 'harga' => 89.19),
                    array('kode_brg' => 'FD00397', 'harga' => 60.83),
                    array('kode_brg' => 'FD00398', 'harga' => 36.00),
                    array('kode_brg' => 'FD00399', 'harga' => 9500.00),
                    array('kode_brg' => 'FD00400', 'harga' => 30.83),
                    array('kode_brg' => 'FD00401', 'harga' => 212.39),
                    array('kode_brg' => 'FD00402', 'harga' => 36.64),
                    array('kode_brg' => 'FD00403', 'harga' => 5.26),
                    array('kode_brg' => 'FD00404', 'harga' => 716.94),
                    array('kode_brg' => 'FD00405', 'harga' => 44.71),
                    array('kode_brg' => 'FD00406', 'harga' => 41.82),
                    array('kode_brg' => 'FD00407', 'harga' => 13.02),
                    array('kode_brg' => 'FD00408', 'harga' => 589.03),
                    array('kode_brg' => 'FD00409', 'harga' => 8.55),
                    array('kode_brg' => 'FD00410', 'harga' => 45.00),
                    array('kode_brg' => 'FD00411', 'harga' => 210.00),
                    array('kode_brg' => 'FD00412', 'harga' => 220.00),
                    array('kode_brg' => 'FD00413', 'harga' => 230.00),
                    array('kode_brg' => 'FD00414', 'harga' => 27.00),
                    array('kode_brg' => 'FD00415', 'harga' => 6.50),
                    array('kode_brg' => 'FD00416', 'harga' => 6.00),
                    array('kode_brg' => 'FD00417', 'harga' => 7.00),
                    array('kode_brg' => 'FD00418', 'harga' => 26.00),
                    array('kode_brg' => 'FD00419', 'harga' => 172.50),
                    array('kode_brg' => 'FD00420', 'harga' => 12.50),
                    array('kode_brg' => 'FD00421', 'harga' => 45.00),
                    array('kode_brg' => 'FD00422', 'harga' => 75.00),
                    array('kode_brg' => 'FD00423', 'harga' => 34.80),
                    array('kode_brg' => 'FD00424', 'harga' => 12.00),
                    array('kode_brg' => 'FD00425', 'harga' => 9.00),
                    array('kode_brg' => 'FD00426', 'harga' => 8.00),
                    array('kode_brg' => 'FD00427', 'harga' => 8.00),
                    array('kode_brg' => 'FD00428', 'harga' => 10.00),
                    array('kode_brg' => 'FD00429', 'harga' => 2500.00),
                    array('kode_brg' => 'FD00430', 'harga' => 600.00),
                    array('kode_brg' => 'FD00431', 'harga' => 2500.00),
                    array('kode_brg' => 'FD00432', 'harga' => 125.00),
                    array('kode_brg' => 'FD00433', 'harga' => 175.00),
                    array('kode_brg' => 'FD00434', 'harga' => 17.00),
                    array('kode_brg' => 'FD00435', 'harga' => 274.00),
                    array('kode_brg' => 'FD00436', 'harga' => 92.70),
                    array('kode_brg' => 'FD00437', 'harga' => 58.00),
                    array('kode_brg' => 'FD00438', 'harga' => 123.19),
                    array('kode_brg' => 'FD00439', 'harga' => 12.50),
                    array('kode_brg' => 'FD00440', 'harga' => 43.33),
                    array('kode_brg' => 'FD00441', 'harga' => 33.80),
                    array('kode_brg' => 'FD00442', 'harga' => 40.00),
                    array('kode_brg' => 'FD00443', 'harga' => 48.50),
                    array('kode_brg' => 'FD00444', 'harga' => 213.86),
                    array('kode_brg' => 'FD00445', 'harga' => 29.09),
                    array('kode_brg' => 'FD00446', 'harga' => 160.00),
                    array('kode_brg' => 'FD00447', 'harga' => 87.00),
                    array('kode_brg' => 'FD00448', 'harga' => 35.00),
                    array('kode_brg' => 'FD00449', 'harga' => 17.00),
                    array('kode_brg' => 'FD00450', 'harga' => 1100.00),
                    array('kode_brg' => 'FD00451', 'harga' => 107.78),
                    array('kode_brg' => 'FD00452', 'harga' => 62.00),
                    array('kode_brg' => 'FD00453', 'harga' => 58.00),
                    array('kode_brg' => 'FD00454', 'harga' => 26.03),
                    array('kode_brg' => 'FD00455', 'harga' => 26.15),
                    array('kode_brg' => 'FD00456', 'harga' => 30.00),
                    array('kode_brg' => 'FD00457', 'harga' => 41.18),
                    array('kode_brg' => 'FD00458', 'harga' => 29.58),
                    array('kode_brg' => 'FD00459', 'harga' => 5400.00),
                    array('kode_brg' => 'FD00460', 'harga' => 3500.00),
                    array('kode_brg' => 'FD00461', 'harga' => 4800.00),
                    array('kode_brg' => 'FD00462', 'harga' => 145.00),
                    array('kode_brg' => 'FD00463', 'harga' => 34.00),
                    array('kode_brg' => 'FD00464', 'harga' => 23.00),
                    array('kode_brg' => 'FD00465', 'harga' => 19.50),
                    array('kode_brg' => 'FD00466', 'harga' => 102.00),
                    array('kode_brg' => 'FD00467', 'harga' => 8.50),
                    array('kode_brg' => 'FD00468', 'harga' => 20.00),
                    array('kode_brg' => 'FD00469', 'harga' => 8740.00),
                    array('kode_brg' => 'FD00470', 'harga' => 4000.00),
                    array('kode_brg' => 'FD00471', 'harga' => 4000.00),
                    array('kode_brg' => 'FD00472', 'harga' => 24.60),
                    array('kode_brg' => 'FD00473', 'harga' => 15000.00),
                    array('kode_brg' => 'FD00474', 'harga' => 600.00),
                    array('kode_brg' => 'FD00475', 'harga' => 4500.00),
                    array('kode_brg' => 'FD00476', 'harga' => 290.00),
                    array('kode_brg' => 'FD00477', 'harga' => 135.00),
                    array('kode_brg' => 'FD00478', 'harga' => 28.00),
                    array('kode_brg' => 'FD00479', 'harga' => 15.00),
                    array('kode_brg' => 'FD00480', 'harga' => 12.50),
                    array('kode_brg' => 'FD00481', 'harga' => 28.00),
                    array('kode_brg' => 'FD00482', 'harga' => 61.27),
                    array('kode_brg' => 'FD00483', 'harga' => 30.00),
                    array('kode_brg' => 'FD00484', 'harga' => 19.09),
                    array('kode_brg' => 'FD00485', 'harga' => 12.50),
                    array('kode_brg' => 'FD00486', 'harga' => 14.00),
                    array('kode_brg' => 'FD00487', 'harga' => 8.80),
                    array('kode_brg' => 'FD00488', 'harga' => 50.00),
                    array('kode_brg' => 'FD00489', 'harga' => 70.00),
                    array('kode_brg' => 'FD00490', 'harga' => 110.00),
                    array('kode_brg' => 'FD00491', 'harga' => 5.00),
                    array('kode_brg' => 'FD00492', 'harga' => 7.50),
                    array('kode_brg' => 'FD00493', 'harga' => 8.00),
                    array('kode_brg' => 'FD00494', 'harga' => 12.00),
                    array('kode_brg' => 'FD00495', 'harga' => 6.51),
                    array('kode_brg' => 'FD00496', 'harga' => 285.00),
                    array('kode_brg' => 'FD00497', 'harga' => 8.01),
                    array('kode_brg' => 'FD00498', 'harga' => 10.00),
                    array('kode_brg' => 'FD00499', 'harga' => 45.00),
                    array('kode_brg' => 'FD00500', 'harga' => 64.58),
                    array('kode_brg' => 'FD00501', 'harga' => 15.83),
                    array('kode_brg' => 'FD00502', 'harga' => 34.00),
                    array('kode_brg' => 'FD00503', 'harga' => 125.00),
                    array('kode_brg' => 'FD00504', 'harga' => 243.00),
                    array('kode_brg' => 'FD00505', 'harga' => 64.00),
                    array('kode_brg' => 'FD00506', 'harga' => 25.00),
                    array('kode_brg' => 'FD00507', 'harga' => 20.00),
                    array('kode_brg' => 'FD00508', 'harga' => 100.00),
                    array('kode_brg' => 'FD00509', 'harga' => 95.00),
                    array('kode_brg' => 'FD00510', 'harga' => 110.00),
                    array('kode_brg' => 'FD00511', 'harga' => 90.00),
                    array('kode_brg' => 'FD00512', 'harga' => 60.00),
                    array('kode_brg' => 'FD00513', 'harga' => 130.00),
                    array('kode_brg' => 'FD00514', 'harga' => 130.00),
                    array('kode_brg' => 'FD00515', 'harga' => 145.00),
                    array('kode_brg' => 'FD00516', 'harga' => 130.00),
                    array('kode_brg' => 'FD00517', 'harga' => 110.00),
                    array('kode_brg' => 'FD00518', 'harga' => 65.00),
                    array('kode_brg' => 'FD00519', 'harga' => 138.00),
                    array('kode_brg' => 'FD00520', 'harga' => 115.00),
                    array('kode_brg' => 'FD00521', 'harga' => 162.00),
                    array('kode_brg' => 'FD00522', 'harga' => 165.00),
                    array('kode_brg' => 'FD00523', 'harga' => 145.00),
                    array('kode_brg' => 'FD00524', 'harga' => 1500.00),
                    array('kode_brg' => 'FD00525', 'harga' => 1180.00),
                    array('kode_brg' => 'FD00526', 'harga' => 152.00),
                    array('kode_brg' => 'FD00527', 'harga' => 230.00),
                    array('kode_brg' => 'FD00528', 'harga' => 325.00),
                    array('kode_brg' => 'FD00529', 'harga' => 130.00),
                    array('kode_brg' => 'FD00530', 'harga' => 150.00),
                    array('kode_brg' => 'FD00531', 'harga' => 170.00),
                    array('kode_brg' => 'FD00532', 'harga' => 126.00),
                    array('kode_brg' => 'FD00533', 'harga' => 335.00),
                    array('kode_brg' => 'FD00534', 'harga' => 356.00),
                    array('kode_brg' => 'FD00535', 'harga' => 74.00),
                    array('kode_brg' => 'FD00536', 'harga' => 90.00),
                    array('kode_brg' => 'FD00537', 'harga' => 260.00),
                    array('kode_brg' => 'FD00538', 'harga' => 196.00),
                    array('kode_brg' => 'FD00539', 'harga' => 15.00),
                    array('kode_brg' => 'FD00540', 'harga' => 82.21),
                    array('kode_brg' => 'FD00541', 'harga' => 697.50),
                    array('kode_brg' => 'FD00542', 'harga' => 55.00),
                    array('kode_brg' => 'FD00543', 'harga' => 10.50),
                    array('kode_brg' => 'FD00544', 'harga' => 14.00),
                    array('kode_brg' => 'FD00545', 'harga' => 40.00),
                    array('kode_brg' => 'FD00546', 'harga' => 1.00),
                    array('kode_brg' => 'FD00547', 'harga' => 22.00),
                    array('kode_brg' => 'FD00548', 'harga' => 32.00),
                    array('kode_brg' => 'FD00549', 'harga' => 7.00),
                    array('kode_brg' => 'FD00550', 'harga' => 1.00),
                    array('kode_brg' => 'FD00551', 'harga' => 150.00),
                    array('kode_brg' => 'FD00552', 'harga' => 24.00),
                    array('kode_brg' => 'FD00553', 'harga' => 105.45),
                    array('kode_brg' => 'FD00554', 'harga' => 32.50),
                    array('kode_brg' => 'FD00555', 'harga' => 12.50),
                    array('kode_brg' => 'FD00556', 'harga' => 900.00),
                    array('kode_brg' => 'FD00557', 'harga' => 55.00),
                    array('kode_brg' => 'FD00558', 'harga' => 321.67),
                    array('kode_brg' => 'FD00559', 'harga' => 45.00),
                    array('kode_brg' => 'BEV00001', 'harga' => 25619.38),
                    array('kode_brg' => 'BEV00002', 'harga' => 16028.54),
                    array('kode_brg' => 'BEV00003', 'harga' => 1088.58),
                    array('kode_brg' => 'BEV00004', 'harga' => 3941.46),
                    array('kode_brg' => 'BEV00005', 'harga' => 3941.65),
                    array('kode_brg' => 'BEV00006', 'harga' => 13000.00),
                    array('kode_brg' => 'BEV00007', 'harga' => 4000.00),
                    array('kode_brg' => 'BEV00008', 'harga' => 22525.00),
                    array('kode_brg' => 'BEV00009', 'harga' => 34151.52),
                    array('kode_brg' => 'BEV00010', 'harga' => 5360.33),
                    array('kode_brg' => 'BEV00011', 'harga' => 86843.00),
                    array('kode_brg' => 'BEV00012', 'harga' => 4734.83),
                    array('kode_brg' => 'BEV00013', 'harga' => 5930.58),
                    array('kode_brg' => 'BEV00014', 'harga' => 4527.04),
                    array('kode_brg' => 'BEV00015', 'harga' => 5433.33),
                    array('kode_brg' => 'BEV00016', 'harga' => 2274.83),
                    array('kode_brg' => 'BEV00017', 'harga' => 4042.82),
                    array('kode_brg' => 'BEV00018', 'harga' => 4268.29),
                    array('kode_brg' => 'BEV00019', 'harga' => 4268.29),
                    array('kode_brg' => 'BEV00020', 'harga' => 4268.29),
                    array('kode_brg' => 'BEV00021', 'harga' => 19000.00),
                    array('kode_brg' => 'TCG00001', 'harga' => 34400.00),
                    array('kode_brg' => 'TCG00002', 'harga' => 34400.00),
                    array('kode_brg' => 'TCG00003', 'harga' => 34400.00),
                    array('kode_brg' => 'TCG00004', 'harga' => 27600.00),
                    array('kode_brg' => 'TCG00005', 'harga' => 27600.00),
                    array('kode_brg' => 'TCG00006', 'harga' => 32800.00),
                    array('kode_brg' => 'TCG00007', 'harga' => 32350.00),
                    array('kode_brg' => 'TCG00008', 'harga' => 18400.00),
                    array('kode_brg' => 'TCG00009', 'harga' => 25400.00),
                    array('kode_brg' => 'TCG00010', 'harga' => 25400.00),
                    array('kode_brg' => 'TCG00011', 'harga' => 20600.00),
                    array('kode_brg' => 'TCG00012', 'harga' => 31450.00),
                    array('kode_brg' => 'TCG00013', 'harga' => 10187.50)
                );

        $keterangan_barang = '';
        $idx_barang_tidak_ditemukan = 0;
        foreach ($data as $k_data => $v_data) {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('kode_text', $v_data['kode_brg'])->first();

            if ( !$d_item ) {
                if ( $keterangan_barang != '' ) {
                    $keterangan_barang .= '<br>';
                }
                $keterangan_barang .= $v_data;

                $idx_barang_tidak_ditemukan++;
            }
        }

        if ( $idx_barang_tidak_ditemukan > 0 ) {
            $keterangan_barang .= '<br>List barang yang tidak ada di program.';

            echo $keterangan_barang;
        } else {
            $m_st = new \Model\Storage\StokTanggal_model();
            $d_st = $m_st->where('tanggal', $tanggal)->where('gudang_kode', $kode_gudang)->first();

            $id_header = null;
            if ( $d_st ) {
                $id_header = $d_st->id;
            } else {
                $m_st = new \Model\Storage\StokTanggal_model();
                $m_st->tanggal = $tanggal;
                $m_st->gudang_kode = $kode_gudang;
                $m_st->save();

                $id_header = $m_st->id;
            }

            foreach ($data as $k_data => $v_data) {
                $m_item = new \Model\Storage\Item_model();
                $d_item = $m_item->where('kode_text', $v_data['kode_brg'])->first();

                $m_sh = new \Model\Storage\StokHarga_model();
                $m_sh->id_header = $id_header;
                $m_sh->item_kode = $d_item->kode;
                $m_sh->harga = $v_data['harga'];
                $m_sh->save();
            }
        }
    }
}
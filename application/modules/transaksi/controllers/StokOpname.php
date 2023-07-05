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

    public function getGroupItem()
    {
        $m_gi = new \Model\Storage\GroupItem_model();
        $d_gi = $m_gi->orderBy('nama', 'asc')->get();

        $data_gi = null;
        if ( $d_gi->count() > 0 ) {
            $data_gi = $d_gi->toArray();
        }

        return $data_gi;
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
        $group_item = $params['group_item'];

        $sql_group_item = null;
        if ( !empty($group_item) ) {
            $sql_group_item = "where gi.kode in ('".implode("', '", $group_item)."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                i.kode,
                i.nama,
                gi.nama as nama_group,
                sh.harga,
                s.jumlah
            from item i
            right join
                group_item gi
                on
                    i.group_kode = gi.kode
            left join
                (
                    select sh.item_kode, sh.harga from stok_harga sh
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = '".$gudang_kode."' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            sh.id_header = st.id
                     group by
                        sh.item_kode, 
                        sh.harga
                ) sh
                on
                    i.kode = sh.item_kode
            left join
                (
                    select s.gudang_kode, s.item_kode, sum(s.jumlah) as jumlah from stok s
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = '".$gudang_kode."' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            s.id_header = st.id
                    group by
                        s.gudang_kode, 
                        s.item_kode
                ) s
                on
                    i.kode = s.item_kode
            ".$sql_group_item."
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
                $data[ $idx ]['satuan'] = ($d_satuan->count() > 0) ? $d_satuan->toArray() : null;

                // $key = $v_item['kode'];

                // $data[ $key ] = $v_item;
                // $data[ $key ]['satuan'][] = array(
                //     'satuan' => $v_item['satuan'],
                //     'pengali' => $v_item['pengali']
                // );

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
        $content['group_item'] = $this->getGroupItem();
        // $content['item'] = $this->getItem();
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

            $m_conf = new \Model\Storage\Conf();

            $tgl_transaksi = null;
            $gudang = null;
            $barang = null;

            $sql_tgl_dan_gudang = "
                select so.* from stok_opname so
                where
                    so.kode_stok_opname = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
                $gudang = $d_tgl_dan_gudang['gudang_kode'];
            }

            $sql_barang = "
                select so.tanggal, sod.item_kode from stok_opname_det sod
                right join
                    stok_opname so
                    on
                        so.id = sod.id_header
                where
                    so.kode_stok_opname = '".$kode."' and
                    sod.jumlah > 0
                group by
                    so.tanggal,
                    sod.item_kode
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

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_stok_opname @kode = '$kode'";

            // $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $kode = 'SO23060001';

        $m_conf = new \Model\Storage\Conf();

        $tgl_transaksi = null;
        $gudang = null;
        $barang = null;

        $sql_tgl_dan_gudang = "
            select so.* from stok_opname so
            where
                so.kode_stok_opname = '".$kode."'
        ";
        $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
        if ( $d_tgl_dan_gudang->count() > 0 ) {
            $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
            $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
            $gudang = $d_tgl_dan_gudang['gudang_kode'];
        }

        $sql_barang = "
            select so.tanggal, sod.item_kode from stok_opname_det sod
            right join
                stok_opname so
                on
                    so.id = sod.id_header
            where
                so.kode_stok_opname = '".$kode."' and
                sod.jumlah > 0
            group by
                so.tanggal,
                sod.item_kode
        ";
        $d_barang = $m_conf->hydrateRaw( $sql_barang );
        if ( $d_barang->count() > 0 ) {
            $d_barang = $d_barang->toArray();

            foreach ($d_barang as $key => $value) {
                $barang[] = $value['item_kode'];
            }
        }

        cetak_r($tgl_transaksi);
        cetak_r($gudang);
        cetak_r($barang);
    }

    public function injekStokOpname()
    {
        $data = array(
            array(
                'kode_barang' => 'BRG2302005',
                'satuan' => 'PAX',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 50000.00
            ),
            array(
                'kode_barang' => 'BRG2302009',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 9000.00
            ),
            array(
                'kode_barang' => 'BRG2302020',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 7.00,
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302023',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 15.00,
                'harga' => 14000.00
            ),
            array(
                'kode_barang' => 'BRG2302027',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 18000.00
            ),
            array(
                'kode_barang' => 'BRG2302034',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 65000.00
            ),
            array(
                'kode_barang' => 'BRG2302035',
                'satuan' => 'GRM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 21000.00
            ),
            array(
                'kode_barang' => 'BRG2302036',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 14414.50
            ),
            array(
                'kode_barang' => 'BRG2302040',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 19.00,
                'harga' => 26000.00
            ),
            array(
                'kode_barang' => 'BRG2302047',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 13513.00
            ),
            array(
                'kode_barang' => 'BRG2302053',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 7.50,
                'harga' => 83950.00
            ),
            array(
                'kode_barang' => 'BRG2302054',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 108500.00
            ),
            array(
                'kode_barang' => 'BRG2302075',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 27.00,
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302560',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 80.00,
                'harga' => 27027.00
            ),
            array(
                'kode_barang' => 'BRG2302561',
                'satuan' => 'CAN',
                'pengali' => 0,
                'jumlah' => 48.00,
                'harga' => 16929.42
            ),
            array(
                'kode_barang' => 'BRG2302079',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 32500.00
            ),
            array(
                'kode_barang' => 'BRG2302082',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 155000.00
            ),
            array(
                'kode_barang' => 'BRG2302085',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 29000.00
            ),
            array(
                'kode_barang' => 'BRG2302087',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 78000.00
            ),
            array(
                'kode_barang' => 'BRG2302088',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 21.00,
                'harga' => 15600.00
            ),
            array(
                'kode_barang' => 'BRG2302089',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 48.00,
                'harga' => 16500.00
            ),
            array(
                'kode_barang' => 'BRG2302090',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 105000.00
            ),
            array(
                'kode_barang' => 'BRG2302091',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 140000.00
            ),
            array(
                'kode_barang' => 'BRG2302093',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 75.00,
                'harga' => 2513.75
            ),
            array(
                'kode_barang' => 'BRG2302098',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 84375.00
            ),
            array(
                'kode_barang' => 'BRG2302104',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 54000.00
            ),
            array(
                'kode_barang' => 'BRG2302108',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.25,
                'harga' => 210000.00
            ),
            array(
                'kode_barang' => 'BRG2302110',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 74200.00
            ),
            array(
                'kode_barang' => 'BRG2302580',
                'satuan' => 'GLN',
                'pengali' => 0,
                'jumlah' => 26.00,
                'harga' => 13288.00
            ),
            array(
                'kode_barang' => 'BRG2302562',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 384.00,
                'harga' => 1221.83
            ),
            array(
                'kode_barang' => 'BRG2302122',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 7.00,
                'harga' => 170000.00
            ),
            array(
                'kode_barang' => 'BRG2302124',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 17.00,
                'harga' => 253221.77
            ),
            array(
                'kode_barang' => 'BRG2302125',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 165000.00
            ),
            array(
                'kode_barang' => 'BRG2302127',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 57000.00
            ),
            array(
                'kode_barang' => 'BRG2302129',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 84000.00
            ),
            array(
                'kode_barang' => 'BRG2302131',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 56859.38
            ),
            array(
                'kode_barang' => 'BRG2302134',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302136',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 70000.00
            ),
            array(
                'kode_barang' => 'BRG2302138',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 29000.00
            ),
            array(
                'kode_barang' => 'BRG2302139',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 65500.00
            ),
            array(
                'kode_barang' => 'BRG2302143',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 39500.00
            ),
            array(
                'kode_barang' => 'BRG2302150',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 9.75,
                'harga' => 112000.00
            ),
            array(
                'kode_barang' => 'BRG2302591',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 20.00,
                'harga' => 22750.00
            ),
            array(
                'kode_barang' => 'BRG2302588',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 10.00,
                'harga' => 18400.00
            ),
            array(
                'kode_barang' => 'BRG2302179',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 226000.00
            ),
            array(
                'kode_barang' => 'BRG2302180',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.50,
                'harga' => 80000.00
            ),
            array(
                'kode_barang' => 'BRG2302181',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.25,
                'harga' => 180000.00
            ),
            array(
                'kode_barang' => 'BRG2302185',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 19200.00
            ),
            array(
                'kode_barang' => 'BRG2302186',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 20.00,
                'harga' => 50270.00
            ),
            array(
                'kode_barang' => 'BRG2302188',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 37.00,
                'harga' => 13964.00
            ),
            array(
                'kode_barang' => 'BRG2302189',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 60000.00
            ),
            array(
                'kode_barang' => 'BRG2302191',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 15.00,
                'harga' => 4500.00
            ),
            array(
                'kode_barang' => 'BRG2302192',
                'satuan' => 'KG',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 260000.00
            ),
            array(
                'kode_barang' => 'BRG2302193',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 41017.19
            ),
            array(
                'kode_barang' => 'BRG2302568',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 80.00,
                'harga' => 37550.18
            ),
            array(
                'kode_barang' => 'BRG2302194',
                'satuan' => 'KG',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302195',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.50,
                'harga' => 17500.00
            ),
            array(
                'kode_barang' => 'BRG2302196',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 82.00,
                'harga' => 12725.00
            ),
            array(
                'kode_barang' => 'BRG2302199',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 148333.33
            ),
            array(
                'kode_barang' => 'BRG2302201',
                'satuan' => 'PCS',
                'pengali' => 0,
                'jumlah' => 26.00,
                'harga' => 2701.65
            ),
            array(
                'kode_barang' => 'BRG2302206',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 151002.78
            ),
            array(
                'kode_barang' => 'BRG2302211',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.80,
                'harga' => 80000.00
            ),
            array(
                'kode_barang' => 'BRG2302569',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 48.00,
                'harga' => 5360.33
            ),
            array(
                'kode_barang' => 'BRG2302221',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 12.00,
                'harga' => 13500.00
            ),
            array(
                'kode_barang' => 'BRG2302226',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.75,
                'harga' => 230000.00
            ),
            array(
                'kode_barang' => 'BRG2302228',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302230',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 15500.00
            ),
            array(
                'kode_barang' => 'BRG2302231',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 20.00,
                'harga' => 16000.00
            ),
            array(
                'kode_barang' => 'BRG2302233',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 22000.00
            ),
            array(
                'kode_barang' => 'BRG2302234',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 7.00,
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302235',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 185000.00
            ),
            array(
                'kode_barang' => 'BRG2302237',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 29000.00
            ),
            array(
                'kode_barang' => 'BRG2302240',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 27.25,
                'harga' => 70000.00
            ),
            array(
                'kode_barang' => 'BRG2302248',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302249',
                'satuan' => 'GLN',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 64408.57
            ),
            array(
                'kode_barang' => 'BRG2302252',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 124598.10
            ),
            array(
                'kode_barang' => 'BRG2302256',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 22006.15
            ),
            array(
                'kode_barang' => 'BRG2302257',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 11.00,
                'harga' => 20445.31
            ),
            array(
                'kode_barang' => 'BRG2302258',
                'satuan' => 'PCS',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 9500.00
            ),
            array(
                'kode_barang' => 'BRG2302263',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 57000.00
            ),
            array(
                'kode_barang' => 'BRG2302280',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302284',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 120000.00
            ),
            array(
                'kode_barang' => 'BRG2302286',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 92000.00
            ),
            array(
                'kode_barang' => 'BRG2302288',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 9.00,
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302291',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 43.00,
                'harga' => 27109.00
            ),
            array(
                'kode_barang' => 'BRG2302293',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 10.00,
                'harga' => 24818.00
            ),
            array(
                'kode_barang' => 'BRG2302299',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 32500.00
            ),
            array(
                'kode_barang' => 'BRG2302300',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 13.00,
                'harga' => 25675.00
            ),
            array(
                'kode_barang' => 'BRG2302301',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302305',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302590',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 40.00,
                'harga' => 27900.00
            ),
            array(
                'kode_barang' => 'BRG2302310',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 39.00,
                'harga' => 25000.00
            ),
            array(
                'kode_barang' => 'BRG2302318',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 31000.00
            ),
            array(
                'kode_barang' => 'BRG2302322',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302570',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 173700.00
            ),
            array(
                'kode_barang' => 'BRG2302324',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 75000.00
            ),
            array(
                'kode_barang' => 'BRG2302583',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 100.00,
                'harga' => 36300.00
            ),
            array(
                'kode_barang' => 'BRG2302581',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 60.00,
                'harga' => 36300.00
            ),
            array(
                'kode_barang' => 'BRG2302582',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 40.00,
                'harga' => 36300.00
            ),
            array(
                'kode_barang' => 'BRG2302329',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 31000.00
            ),
            array(
                'kode_barang' => 'BRG2302330',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 210000.00
            ),
            array(
                'kode_barang' => 'BRG2302332',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 11000.00
            ),
            array(
                'kode_barang' => 'BRG2302335',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 14500.00
            ),
            array(
                'kode_barang' => 'BRG2302337',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 78000.00
            ),
            array(
                'kode_barang' => 'BRG2302339',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 180.00,
                'harga' => 3243.25
            ),
            array(
                'kode_barang' => 'BRG2302341',
                'satuan' => 'LTR',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 350343.00
            ),
            array(
                'kode_barang' => 'BRG2302342',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 27000.00
            ),
            array(
                'kode_barang' => 'BRG2302343',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 9.00,
                'harga' => 70000.00
            ),
            array(
                'kode_barang' => 'BRG2302344',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 93338.67
            ),
            array(
                'kode_barang' => 'BRG2302347',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 8.58,
                'harga' => 94170.00
            ),
            array(
                'kode_barang' => 'BRG2302350',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 31000.00
            ),
            array(
                'kode_barang' => 'BRG2302351',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 18.00,
                'harga' => 3500.00
            ),
            array(
                'kode_barang' => 'BRG2302355',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 9945.45
            ),
            array(
                'kode_barang' => 'BRG2302571',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 168.00,
                'harga' => 4734.83
            ),
            array(
                'kode_barang' => 'BRG2302357',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 72500.00
            ),
            array(
                'kode_barang' => 'BRG2302572',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 216.00,
                'harga' => 5930.58
            ),
            array(
                'kode_barang' => 'BRG2302358',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 15135.00
            ),
            array(
                'kode_barang' => 'BRG2302359',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.25,
                'harga' => 76000.00
            ),
            array(
                'kode_barang' => 'BRG2302360',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 125000.00
            ),
            array(
                'kode_barang' => 'BRG2302361',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 27000.00
            ),
            array(
                'kode_barang' => 'BRG2302366',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 320000.00
            ),
            array(
                'kode_barang' => 'BRG2302369',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 72000.00
            ),
            array(
                'kode_barang' => 'BRG2302373',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 10.00,
                'harga' => 3000.00
            ),
            array(
                'kode_barang' => 'BRG2302374',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 9.00,
                'harga' => 3500.00
            ),
            array(
                'kode_barang' => 'BRG2302375',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 2500.00
            ),
            array(
                'kode_barang' => 'BRG2302573',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 288.00,
                'harga' => 4527.04
            ),
            array(
                'kode_barang' => 'BRG2302388',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 30000.00
            ),
            array(
                'kode_barang' => 'BRG2302389',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 17340.33
            ),
            array(
                'kode_barang' => 'BRG2302391',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302392',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 57500.00
            ),
            array(
                'kode_barang' => 'BRG2302393',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 229730.00
            ),
            array(
                'kode_barang' => 'BRG2302395',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.60,
                'harga' => 185000.00
            ),
            array(
                'kode_barang' => 'BRG2302396',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 16500.00
            ),
            array(
                'kode_barang' => 'BRG2302584',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 100.00,
                'harga' => 29100.00
            ),
            array(
                'kode_barang' => 'BRG2302587',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 33525.00
            ),
            array(
                'kode_barang' => 'BRG2302586',
                'satuan' => 'PACK',
                'pengali' => 0,
                'jumlah' => 20.00,
                'harga' => 33525.00
            ),
            array(
                'kode_barang' => 'BRG2302398',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 36000.00
            ),
            array(
                'kode_barang' => 'BRG2302400',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 11.00,
                'harga' => 18500.00
            ),
            array(
                'kode_barang' => 'BRG2302402',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 22900.00
            ),
            array(
                'kode_barang' => 'BRG2302403',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 114507.06
            ),
            array(
                'kode_barang' => 'BRG2302404',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 21.00,
                'harga' => 5540.76
            ),
            array(
                'kode_barang' => 'BRG2302406',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 105000.00
            ),
            array(
                'kode_barang' => 'BRG2302407',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 81512.24
            ),
            array(
                'kode_barang' => 'BRG2302408',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 15.00,
                'harga' => 4568.70
            ),
            array(
                'kode_barang' => 'BRG2302410',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 25.00,
                'harga' => 47000.00
            ),
            array(
                'kode_barang' => 'BRG2302425',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.50,
                'harga' => 10500.00
            ),
            array(
                'kode_barang' => 'BRG2302437',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 29000.00
            ),
            array(
                'kode_barang' => 'BRG2302575',
                'satuan' => 'BTL',
                'pengali' => 0,
                'jumlah' => 96.00,
                'harga' => 2402.42
            ),
            array(
                'kode_barang' => 'BRG2302438',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 5.00,
                'harga' => 34000.00
            ),
            array(
                'kode_barang' => 'BRG2302440',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 26000.00
            ),
            array(
                'kode_barang' => 'BRG2302441',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 8.00,
                'harga' => 16900.00
            ),
            array(
                'kode_barang' => 'BRG2302576',
                'satuan' => 'CAN',
                'pengali' => 0,
                'jumlah' => 48.00,
                'harga' => 3941.46
            ),
            array(
                'kode_barang' => 'BRG2302443',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 48500.00
            ),
            array(
                'kode_barang' => 'BRG2302444',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 41.00,
                'harga' => 1778.86
            ),
            array(
                'kode_barang' => 'BRG2302445',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 16.00,
                'harga' => 29090.93
            ),
            array(
                'kode_barang' => 'BRG2302451',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 33002.92
            ),
            array(
                'kode_barang' => 'BRG2302453',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 1.00,
                'harga' => 58000.00
            ),
            array(
                'kode_barang' => 'BRG2302454',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 95.00,
                'harga' => 10876.38
            ),
            array(
                'kode_barang' => 'BRG2302455',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 24.00,
                'harga' => 10322.72
            ),
            array(
                'kode_barang' => 'BRG2302456',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302457',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 7.00,
                'harga' => 19000.00
            ),
            array(
                'kode_barang' => 'BRG2302458',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 10.00,
                'harga' => 17900.00
            ),
            array(
                'kode_barang' => 'BRG2302459',
                'satuan' => 'PCS',
                'pengali' => 0,
                'jumlah' => 31.00,
                'harga' => 5400.00
            ),
            array(
                'kode_barang' => 'BRG2302469',
                'satuan' => 'SACT',
                'pengali' => 0,
                'jumlah' => 21.15,
                'harga' => 137633.01
            ),
            array(
                'kode_barang' => 'BRG2302472',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 25.00,
                'harga' => 28000.00
            ),
            array(
                'kode_barang' => 'BRG2302477',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 39.85,
                'harga' => 138000.00
            ),
            array(
                'kode_barang' => 'BRG2302479',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.50,
                'harga' => 15000.00
            ),
            array(
                'kode_barang' => 'BRG2302480',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 12500.00
            ),
            array(
                'kode_barang' => 'BRG2302483',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 6.00,
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302484',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 7.75,
                'harga' => 19090.00
            ),
            array(
                'kode_barang' => 'BRG2302485',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 13.00,
                'harga' => 14500.00
            ),
            array(
                'kode_barang' => 'BRG2302486',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 23.00,
                'harga' => 14000.00
            ),
            array(
                'kode_barang' => 'BRG2302489',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 35000.00
            ),
            array(
                'kode_barang' => 'BRG2302490',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 110000.00
            ),
            array(
                'kode_barang' => 'BRG2302502',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.00,
                'harga' => 8000.00
            ),
            array(
                'kode_barang' => 'BRG2302508',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 4.00,
                'harga' => 18500.00
            ),
            array(
                'kode_barang' => 'BRG2302552',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 2.50,
                'harga' => 24000.00
            ),
            array(
                'kode_barang' => 'BRG2302509',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 95000.00
            ),
            array(
                'kode_barang' => 'BRG2302516',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 10.00,
                'harga' => 130000.00
            ),
            array(
                'kode_barang' => 'BRG2302524',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 16.00,
                'harga' => 6000.00
            ),
            array(
                'kode_barang' => 'BRG2302525',
                'satuan' => 'ML',
                'pengali' => 0,
                'jumlah' => 3.00,
                'harga' => 12000.00
            ),
            array(
                'kode_barang' => 'BRG2302542',
                'satuan' => 'GRAM',
                'pengali' => 0,
                'jumlah' => 0.50,
                'harga' => 55000.00
            ),
        );

        $keterangan_barang = '';
        $idx_barang_tidak_ditemukan = 0;
        foreach ($data as $k_data => $v_data) {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('kode', $v_data['kode_barang'])->first();

            $m_is = new \Model\Storage\ItemSatuan_model();
            $d_is = $m_is->where('item_kode', $v_data['kode_barang'])->where('satuan', $v_data['satuan'])->first();

            if ( !$d_item || !$d_is ) {
                cetak_r('KODE : '.$v_data['kode_barang']);
                cetak_r('KODE : '.$v_data['kode_barang'].' | SATUAN : '.$v_data['satuan']);

                if ( $keterangan_barang != '' ) {
                    $keterangan_barang .= '<br>';
                }
                if ( !$d_item ) {
                    $keterangan_barang .= 'KODE : '.$v_data['kode_barang'];
                }
                if ( !$d_item ) {
                    $keterangan_barang .= 'KODE : '.$v_data['kode_barang'].' | SATUAN : '.$v_data['satuan'];
                }

                $idx_barang_tidak_ditemukan++;
            } else {
                $data[ $k_data ]['pengali'] = $d_is->pengali;
            }
        }

        if ( $idx_barang_tidak_ditemukan > 0 ) {
            $keterangan_barang .= '<br>List barang yang tidak ada di program.';

            echo $keterangan_barang;
        } else {
            cetak_r('lengkap');
            // $m_so = new \Model\Storage\StokOpname_model();

            // $kode_stok_opname = $m_so->getNextIdRibuan();

            // $m_so->tanggal = '2023-07-01';
            // $m_so->gudang_kode = 'GDG-PUSAT';
            // $m_so->kode_stok_opname = $kode_stok_opname;
            // $m_so->save();

            // foreach ($data as $k_li => $v_li) {
            //     $m_sod = new \Model\Storage\StokOpnameDet_model();
            //     $m_sod->id_header = $m_so->id;
            //     $m_sod->item_kode = $v_li['kode_barang'];
            //     $m_sod->satuan = $v_li['satuan'];
            //     $m_sod->pengali = $v_li['pengali'];
            //     $m_sod->jumlah = $v_li['jumlah'];
            //     $m_sod->harga = $v_li['harga'];
            //     $m_sod->save();
            // }
        }
    }
}
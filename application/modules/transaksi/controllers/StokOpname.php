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
        // $sql = "
        //     select 
        //         i.kode,
        //         i.nama,
        //         gi.nama as nama_group,
        //         s.harga,
        //         s.jumlah
        //     from item i
        //     right join
        //         group_item gi
        //         on
        //             i.group_kode = gi.kode
        //     left join
        //         (
        //             select s.gudang_kode, s.item_kode, sum(s.jumlah) as jumlah, sh.harga from stok s
        //             right join
        //                 (
        //                     select top 1 * from stok_tanggal where gudang_kode = '".$gudang_kode."' and tanggal <= GETDATE() order by tanggal desc
        //                 ) st
        //                 on
        //                     s.id_header = st.id
        //             left join
        //                 stok_harga sh
        //                 on
        //                     sh.id_header = st.id and
        //                     sh.item_kode = s.item_kode
        //             group by
        //                 s.gudang_kode, 
        //                 s.item_kode,
        //                 sh.harga
        //         ) s
        //         on
        //             i.kode = s.item_kode
        //     ".$sql_group_item."
        //     order by
        //         i.nama asc
        // ";
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
                    select st.id, st.gudang_kode, s.item_kode, sum(s.jumlah) as jumlah from stok s
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = 'GDG-PUSAT' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            s.id_header = st.id
                    group by
                        st.id,
                        st.gudang_kode, 
                        s.item_kode
                ) s
                on
                    i.kode = s.item_kode
            left join
                (
                    select st.id, st.gudang_kode, sh.item_kode, sh.harga from stok_harga sh
                    right join
                        (
                            select top 1 * from stok_tanggal where gudang_kode = 'GDG-PUSAT' and tanggal <= GETDATE() order by tanggal desc
                        ) st
                        on
                            sh.id_header = st.id
                    group by
                        st.id, 
                        st.gudang_kode, 
                        sh.item_kode, 
                        sh.harga
                ) sh
                on
                    sh.item_kode = i.kode
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
            select 
                so.id, 
                so.kode_stok_opname, 
                so.tanggal, 
                g.nama 
            from stok_opname so
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
        // $content['item'] = $this->getItem();
        // $content['gudang'] = $this->getGudang();
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
        $kode = 'SO23070005';

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
                so.kode_stok_opname = '".$kode."'
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

        cetak_r( $sql, 1 );

        // $d_conf = $m_conf->hydrateRaw($sql);
    }

    public function injekStokOpname()
    {
        $data = array(
            array('BRG2302001', 'KG', 0, 320000.00),
            array('BRG2302002', 'KG', 0, 110000.00),
            array('BRG2302003', 'KG', 0, 30000.00),
            array('BRG2302004', 'KG', 0, 85000.00),
            array('BRG2302006', 'LTR', 0, 12000.00),
            array('BRG2302007', 'KG', 0, 35000.00),
            array('BRG2302008', 'KG', 0, 45000.00),
            array('BRG2302010', 'KG', 0, 260000.00),
            array('BRG2302011', 'CAN', 0, 265000.00),
            array('BRG2302012', 'KG', 0, 265000.00),
            array('BRG2302013', 'LTR', 0, 156000.00),
            array('BRG2302014', 'CAN', 0, 52500.00),
            array('BRG2302015', 'KG', 0, 85000.00),
            array('BRG2302016', 'KG', 0, 66000.00),
            array('BRG2302017', 'KG', 0, 31428.57),
            array('BRG2302018', 'KG', 0, 35000.00),
            array('BRG2302019', 'ML', 0, 2500.00),
            array('BRG2302021', 'BTL', 0, 42000.00),
            array('BRG2302022', 'KG', 0, 14000.00),
            array('BRG2302024', 'KG', 0, 56000.00),
            array('BRG2302025', 'KG', 0, 20000.00),
            array('BRG2302026', 'KG', 0, 15000.00),
            array('BRG2302028', 'KG', 0, 312000.00),
            array('BRG2302029', 'PACK', 0, 38500.00),
            array('BRG2302030', 'KG', 0, 110000.00),
            array('BRG2302031', 'EKOR', 0, 775000.00),
            array('BRG2302032', 'KG', 0, 28000.00),
            array('BRG2302033', 'KG', 0, 14500.00),
            array('BRG2302546', 'PCS', 0, 1600.00),
            array('BRG2302037', 'KG', 0, 65000.00),
            array('BRG2302038', 'KG', 0, 32000.00),
            array('BRG2302039', 'KG', 0, 27500.00),
            array('BRG2302041', 'KG', 0, 32000.00),
            array('BRG2302042', 'KG', 0, 39000.00),
            array('BRG2302043', 'KG', 0, 16000.00),
            array('BRG2302044', 'KG', 0, 39000.00),
            array('BRG2302045', 'KG', 0, 37000.00),
            array('BRG2302046', 'PACK', 0, 42727.00),
            array('BRG2302550', 'KG', 0, 10000.00),
            array('BRG2302048', 'EKOR', 0, 75000.00),
            array('BRG2302049', 'EKOR', 0, 130000.00),
            array('BRG2302050', 'CAN', 0, 41000.00),
            array('BRG2302051', 'KG', 0, 47500.00),
            array('BRG2302052', 'PACK', 0, 14550.00),
            array('BRG2302055', 'PACK', 0, 91700.00),
            array('BRG2302056', 'CAN', 0, 14250.00),
            array('BRG2302057', 'KG', 0, 81800.00),
            array('BRG2302058', 'KG', 0, 66900.00),
            array('BRG2302059', 'KG', 0, 67000.00),
            array('BRG2302060', 'KG', 0, 53500.00),
            array('BRG2302061', 'KG', 0, 10000.00),
            array('BRG2302062', 'KG', 0, 15000.00),
            array('BRG2302063', 'KG', 0, 14000.00),
            array('BRG2302064', 'KG', 0, 35000.00),
            array('BRG2302065', 'KG', 0, 11500.00),
            array('BRG2302066', 'KG', 0, 13200.00),
            array('BRG2302067', 'KG', 0, 16000.00),
            array('BRG2302068', 'BTL', 0, 15250.00),
            array('BRG2302070', 'KG', 0, 24000.00),
            array('BRG2302069', 'KG', 0, 22000.00),
            array('BRG2302071', 'KG', 0, 19500.00),
            array('BRG2302072', 'PACK', 0, 128750.00),
            array('BRG2302073', 'KG', 0, 280000.00),
            array('BRG2302074', 'PACK', 0, 9000.00),
            array('BRG2302076', 'KG', 0, 198000.00),
            array('BRG2302077', 'KG', 0, 25000.00),
            array('BRG2302078', 'KG', 0, 146000.00),
            array('BRG2302080', 'KG', 0, 13500.00),
            array('BRG2302081', 'CAN', 0, 19000.00),
            array('BRG2302083', 'KG', 0, 47500.00),
            array('BRG2302084', 'KG', 0, 28000.00),
            array('BRG2302086', 'KG', 0, 20000.00),
            array('BRG2302092', 'EKOR', 0, 28000.00),
            array('BRG2302094', 'KG', 0, 186500.00),
            array('BRG2302095', 'KG', 0, 27500.00),
            array('BRG2302096', 'KG', 0, 30500.00),
            array('BRG2302097', 'KG', 0, 24500.00),
            array('BRG2302099', 'KG', 0, 38000.00),
            array('BRG2302100', 'KG', 0, 20000.00),
            array('BRG2302101', 'KG', 0, 37000.00),
            array('BRG2302102', 'PCS', 0, 8000.00),
            array('BRG2302103', 'BTL', 0, 95000.00),
            array('BRG2302105', 'KG', 0, 46000.00),
            array('BRG2302106', 'KG', 0, 35000.00),
            array('BRG2302107', 'PACK', 0, 4500.00),
            array('BRG2302109', 'PACK', 0, 88000.00),
            array('BRG2302111', 'KG', 0, 49000.00),
            array('BRG2302112', 'KG', 0, 62000.00),
            array('BRG2302113', 'KG', 0, 72100.00),
            array('BRG2302114', 'PACK', 0, 14750.00),
            array('BRG2302115', 'KG', 0, 53400.00),
            array('BRG2302558', 'BTL', 0, 96500.00),
            array('BRG2302116', 'BTL', 0, 17182.00),
            array('BRG2302117', 'PACK', 0, 47000.00),
            array('BRG2302118', 'PACK', 0, 57500.00),
            array('BRG2302119', 'BTL', 0, 18750.00),
            array('BRG2302120', 'PACK', 0, 17500.00),
            array('BRG2302121', 'KG', 0, 80000.00),
            array('BRG2302123', 'CAN', 0, 1540909.00),
            array('BRG2302563', 'CAN', 0, 3901.02),
            array('BRG2302564', 'CAN', 0, 3916.43),
            array('BRG2302126', 'KG', 0, 86000.00),
            array('BRG2302128', 'PACK', 0, 75000.00),
            array('BRG2302130', 'PACK', 0, 22450.00),
            array('BRG2302132', 'LTR', 0, 86205.00),
            array('BRG2302133', 'CAN', 0, 19000.00),
            array('BRG2302135', 'BTL', 0, 87000.00),
            array('BRG2302137', 'BTL', 0, 68000.00),
            array('BRG2302140', 'KG', 0, 49000.00),
            array('BRG2302141', 'KG', 0, 75000.00),
            array('BRG2302142', 'EKOR', 0, 55000.00),
            array('BRG2302144', 'KG', 0, 75000.00),
            array('BRG2302145', 'KG', 0, 95000.00),
            array('BRG2302146', 'KG', 0, 85000.00),
            array('BRG2302147', 'KG', 0, 220000.00),
            array('BRG2302148', 'KG', 0, 145000.00),
            array('BRG2302149', 'KG', 0, 120000.00),
            array('BRG2302151', 'KG', 0, 85000.00),
            array('BRG2302152', 'KG', 0, 85000.00),
            array('BRG2302153', 'KG', 0, 75000.00),
            array('BRG2302154', 'CAN', 0, 24000.00),
            array('BRG2302155', 'PACK', 0, 62500.00),
            array('BRG2302156', 'PACK', 0, 23750.00),
            array('BRG2302157', 'KG', 0, 105000.00),
            array('BRG2302158', 'KG', 0, 481000.00),
            array('BRG2302159', 'KG', 0, 25000.00),
            array('BRG2302160', 'IKAT', 0, 10000.00),
            array('BRG2302161', 'KG', 0, 25000.00),
            array('BRG2302162', 'PACK', 0, 20000.00),
            array('BRG2302163', 'IKAT', 0, 3000.00),
            array('BRG2302164', 'KG', 0, 15000.00),
            array('BRG2302165', 'KG', 0, 100000.00),
            array('BRG2302166', 'KG', 0, 37500.00),
            array('BRG2302167', 'KG', 0, 17500.00),
            array('BRG2302168', 'KG', 0, 8500.00),
            array('BRG2302169', 'IKAT', 0, 2000.00),
            array('BRG2302170', 'KG', 0, 13000.00),
            array('BRG2302171', 'IKAT', 0, 10500.00),
            array('BRG2302172', 'IKAT', 0, 3000.00),
            array('BRG2302173', 'KG', 0, 8000.00),
            array('BRG2302174', 'KG', 0, 10000.00),
            array('BRG2302175', 'IKAT', 0, 12000.00),
            array('BRG2302176', 'KG', 0, 25000.00),
            array('BRG2302177', 'KG', 0, 12500.00),
            array('BRG2302565', 'PCS', 0, 13000.00),
            array('BRG2302178', 'PAIL', 0, 156250.00),
            array('BRG2302592', 'PACK', 0, 31450.00),
            array('BRG2302182', 'KG', 0, 8000.00),
            array('BRG2302566', 'BTL', 0, 4000.00),
            array('BRG2302183', 'PACK', 0, 11500.00),
            array('BRG2302184', 'PACK', 0, 33500.00),
            array('BRG2302187', 'PACK', 0, 118967.00),
            array('BRG2302547', 'PACK', 0, 22000.00),
            array('BRG2302190', 'KG', 0, 10000.00),
            array('BRG2302593', 'PACK', 0, 23912.50),
            array('BRG2302567', 'BTL', 0, 22525.00),
            array('BRG2302197', 'KG', 0, 40000.00),
            array('BRG2302198', 'KG', 0, 26000.00),
            array('BRG2302200', 'CAN', 0, 33000.00),
            array('BRG2302202', 'KG', 0, 12500.00),
            array('BRG2302203', 'PACK', 0, 26000.00),
            array('BRG2302204', 'KG', 0, 90000.00),
            array('BRG2302205', 'KG', 0, 140000.00),
            array('BRG2302207', 'KG', 0, 50000.00),
            array('BRG2302208', 'KG', 0, 47000.00),
            array('BRG2302209', 'KG', 0, 36000.00),
            array('BRG2302210', 'KG', 0, 35000.00),
            array('BRG2302212', 'KG', 0, 50000.00),
            array('BRG2302213', 'KG', 0, 125000.00),
            array('BRG2302214', 'KG', 0, 265000.00),
            array('BRG2302215', 'KG', 0, 55000.00),
            array('BRG2302216', 'KG', 0, 19000.00),
            array('BRG2302217', 'KG', 0, 7000.00),
            array('BRG2302218', 'KG', 0, 16000.00),
            array('BRG2302219', 'PACK', 0, 1450.00),
            array('BRG2302220', 'KG', 0, 14000.00),
            array('BRG2302222', 'KG', 0, 37000.00),
            array('BRG2302223', 'PACK', 0, 16000.00),
            array('BRG2302224', 'KG', 0, 285000.00),
            array('BRG2302225', 'KG', 0, 55000.00),
            array('BRG2302227', 'PACK', 0, 13750.00),
            array('BRG2302229', 'KG', 0, 40000.00),
            array('BRG2302557', 'KG', 0, 35000.00),
            array('BRG2302232', 'KG', 0, 65000.00),
            array('BRG2302236', 'KG', 0, 10000.00),
            array('BRG2302238', 'KG', 0, 19000.00),
            array('BRG2302239', 'KG', 0, 35000.00),
            array('BRG2302241', 'KG', 0, 65000.00),
            array('BRG2302242', 'KG', 0, 30000.00),
            array('BRG2302243', 'EKOR', 0, 1487550.00),
            array('BRG2302244', 'KG', 0, 8000.00),
            array('BRG2302245', 'BTL', 0, 18000.00),
            array('BRG2302246', 'KG', 0, 240000.00),
            array('BRG2302247', 'PACK', 0, 37500.00),
            array('BRG2302250', 'BTL', 0, 8500.00),
            array('BRG2302251', 'BTL', 0, 24000.00),
            array('BRG2302253', 'BTL', 0, 15250.00),
            array('BRG2302254', 'KG', 0, 19500.00),
            array('BRG2302255', 'KG', 0, 12500.00),
            array('BRG2302259', 'KG', 0, 12500.00),
            array('BRG2302260', 'KG', 0, 46500.00),
            array('BRG2302261', 'IKAT', 0, 3500.00),
            array('BRG2302262', 'KG', 0, 15000.00),
            array('BRG2302264', 'KG', 0, 32500.00),
            array('BRG2302265', 'KG', 0, 11666.94),
            array('BRG2302266', 'BTL', 0, 22000.00),
            array('BRG2302267', 'KG', 0, 13500.00),
            array('BRG2302268', 'KG', 0, 10000.00),
            array('BRG2302269', 'KG', 0, 10000.00),
            array('BRG2302270', 'KG', 0, 35000.00),
            array('BRG2302271', 'KG', 0, 23000.00),
            array('BRG2302272', 'KG', 0, 160000.00),
            array('BRG2302273', 'KG', 0, 55000.00),
            array('BRG2302274', 'KG', 0, 22000.00),
            array('BRG2302275', 'KG', 0, 10000.00),
            array('BRG2302276', 'KG', 0, 8000.00),
            array('BRG2302277', 'KG', 0, 6000.00),
            array('BRG2302278', 'KG', 0, 9000.00),
            array('BRG2302279', 'KG', 0, 12000.00),
            array('BRG2302281', 'BTL', 0, 12000.00),
            array('BRG2302282', 'KG', 0, 77000.00),
            array('BRG2302283', 'KG', 0, 92000.00),
            array('BRG2302285', 'KG', 0, 35000.00),
            array('BRG2302287', 'PACK', 0, 24500.00),
            array('BRG2302289', 'KG', 0, 22500.00),
            array('BRG2302290', 'KG', 0, 12000.00),
            array('BRG2302292', 'KG', 0, 224000.00),
            array('BRG2302294', 'PACK', 0, 71309.00),
            array('BRG2302295', 'KG', 0, 6500.00),
            array('BRG2302296', 'KG', 0, 35000.00),
            array('BRG2302297', 'KG', 0, 30000.00),
            array('BRG2302298', 'KG', 0, 25000.00),
            array('BRG2302302', 'KG', 0, 24000.00),
            array('BRG2302303', 'KG', 0, 12000.00),
            array('BRG2302304', 'KG', 0, 34000.00),
            array('BRG2302589', 'PACK', 0, 27900.00),
            array('BRG2302306', 'KG', 0, 8000.00),
            array('BRG2302307', 'KG', 0, 85000.00),
            array('BRG2302308', 'KG', 0, 25000.00),
            array('BRG2302309', 'KG', 0, 7000.00),
            array('BRG2302311', 'KG', 0, 45000.00),
            array('BRG2302559', 'BTL', 0, 22000.00),
            array('BRG2302312', 'KG', 0, 6000.00),
            array('BRG2302313', 'PCS', 0, 1100.00),
            array('BRG2302314', 'KG', 0, 30000.00),
            array('BRG2302315', 'KG', 0, 95000.00),
            array('BRG2302316', 'KG', 0, 55000.00),
            array('BRG2302317', 'KG', 0, 7500.00),
            array('BRG2302319', 'PCS', 0, 1800.00),
            array('BRG2302320', 'PACK', 0, 88288.00),
            array('BRG2302321', 'PACK', 0, 81818.00),
            array('BRG2302323', 'BTL', 0, 31250.00),
            array('BRG2302325', 'KG', 0, 30000.00),
            array('BRG2302326', 'KG', 0, 18500.00),
            array('BRG2302327', 'KG', 0, 5000.00),
            array('BRG2302328', 'BTL', 0, 115000.00),
            array('BRG2302331', 'BTL', 0, 131000.00),
            array('BRG2302333', 'KG', 0, 13500.00),
            array('BRG2302334', 'KG', 0, 110000.00),
            array('BRG2302336', 'BTL', 0, 4000.00),
            array('BRG2302338', 'KG', 0, 13500.00),
            array('BRG2302548', 'KG', 0, 32000.00),
            array('BRG2302340', 'KG', 0, 80000.00),
            array('BRG2302549', 'PACK', 0, 35000.00),
            array('BRG2302345', 'BTL', 0, 35000.00),
            array('BRG2302346', 'KG', 0, 108162.90),
            array('BRG2302348', 'CAN', 0, 14575.00),
            array('BRG2302349', 'BTL', 0, 31000.00),
            array('BRG2302352', 'PCS', 0, 5000.00),
            array('BRG2302353', 'KG', 0, 55000.00),
            array('BRG2302354', 'KG', 0, 11000.00),
            array('BRG2302356', 'KG', 0, 120000.00),
            array('BRG2302362', 'KG', 0, 52500.00),
            array('BRG2302363', 'KG', 0, 60000.00),
            array('BRG2302364', 'KG', 0, 74500.00),
            array('BRG2302365', 'BTL', 0, 20750.00),
            array('BRG2302367', 'KG', 0, 75000.00),
            array('BRG2302368', 'KG', 0, 110000.00),
            array('BRG2302370', 'KG', 0, 12000.00),
            array('BRG2302371', 'KG', 0, 20000.00),
            array('BRG2302372', 'KG', 0, 35000.00),
            array('BRG2302555', 'BTL', 0, 42500.00),
            array('BRG2302376', 'GRAM', 0, 1545.00),
            array('BRG2302377', 'SISIR', 0, 31500.00),
            array('BRG2302378', 'SISIR', 0, 30000.00),
            array('BRG2302379', 'SISIR', 0, 15000.00),
            array('BRG2302380', 'SSR', 0, 85000.00),
            array('BRG2302381', 'TANDON', 0, 85000.00),
            array('BRG2302382', 'SISIR', 0, 45000.00),
            array('BRG2302383', 'PCS', 0, 5000.00),
            array('BRG2302384', 'CAN', 0, 165000.00),
            array('BRG2302385', 'PACK', 0, 22400.00),
            array('BRG2302386', 'PACK', 0, 31982.00),
            array('BRG2302574', 'BTL', 0, 5433.33),
            array('BRG2302387', 'KG', 0, 95000.00),
            array('BRG2302556', 'BTL', 0, 450000.00),
            array('BRG2302390', 'LOAF', 0, 42000.00),
            array('BRG2302394', 'KG', 0, 13000.00),
            array('BRG2302397', 'BTL', 0, 27500.00),
            array('BRG2302585', 'PACK', 0, 29100.00),
            array('BRG2302401', 'BTL', 0, 49500.00),
            array('BRG2302405', 'BTL', 0, 19000.00),
            array('BRG2302409', 'GLN', 0, 48750.00),
            array('BRG2302411', 'BTL', 0, 52500.00),
            array('BRG2302412', 'BTL', 0, 55000.00),
            array('BRG2302413', 'BTL', 0, 60000.00),
            array('BRG2302414', 'KG', 0, 27000.00),
            array('BRG2302415', 'KG', 0, 10500.00),
            array('BRG2302416', 'KG', 0, 10000.00),
            array('BRG2302417', 'KG', 0, 10000.00),
            array('BRG2302418', 'KG', 0, 26000.00),
            array('BRG2302419', 'KG', 0, 172500.00),
            array('BRG2302420', 'KG', 0, 12500.00),
            array('BRG2302421', 'KG', 0, 35000.00),
            array('BRG2302423', 'KG', 0, 16000.00),
            array('BRG2302422', 'KG', 0, 95000.00),
            array('BRG2302424', 'KG', 0, 25000.00),
            array('BRG2302426', 'KG', 0, 9000.00),
            array('BRG2302427', 'KG', 0, 7000.00),
            array('BRG2302428', 'KG', 0, 10000.00),
            array('BRG2302430', 'KG', 0, 600000.00),
            array('BRG2302431', 'KG', 0, 2500000.00),
            array('BRG2302433', 'KG', 0, 175000.00),
            array('BRG2302432', 'KG', 0, 125000.00),
            array('BRG2302434', 'KG', 0, 19000.00),
            array('BRG2302435', 'BTL', 0, 68500.00),
            array('BRG2302436', 'PACK', 0, 92700.00),
            array('BRG2302439', 'KG', 0, 12500.00),
            array('BRG2302442', 'KG', 0, 12500.00),
            array('BRG2302446', 'KG', 0, 160000.00),
            array('BRG2302447', 'KG', 0, 87000.00),
            array('BRG2302448', 'KG', 0, 35000.00),
            array('BRG2302449', 'KG', 0, 17000.00),
            array('BRG2302450', 'BTL', 0, 22000.00),
            array('BRG2302452', 'PACK', 0, 62000.00),
            array('BRG2302399', 'PCS', 0, 3500.00),
            array('BRG2302460', 'PCS', 0, 3500.00),
            array('BRG2302461', 'PCS', 0, 4800.00),
            array('BRG2302462', 'KG', 0, 145000.00),
            array('BRG2302463', 'POT', 0, 34000.00),
            array('BRG2302464', 'POT', 0, 23000.00),
            array('BRG2302465', 'KG', 0, 20000.00),
            array('BRG2302554', 'BTL', 0, 32500.00),
            array('BRG2302466', 'CAN', 0, 51000.00),
            array('BRG2302467', 'KG', 0, 8500.00),
            array('BRG2302468', 'KG', 0, 20000.00),
            array('BRG2302470', 'PCS', 0, 4000.00),
            array('BRG2302471', 'PCS', 0, 4000.00),
            array('BRG2302473', 'PCS', 0, 15000.00),
            array('BRG2302474', 'PCS', 0, 600.00),
            array('BRG2302475', 'PAX', 0, 4500.00),
            array('BRG2302476', 'KG', 0, 290000.00),
            array('BRG2302478', 'KG', 0, 28000.00),
            array('BRG2302481', 'KG', 0, 28000.00),
            array('BRG2302482', 'KG', 0, 61273.00),
            array('BRG2302487', 'KG', 0, 8800.00),
            array('BRG2302488', 'PACK', 0, 25000.00),
            array('BRG2302551', 'BTL', 0, 45000.00),
            array('BRG2302491', 'KG', 0, 5000.00),
            array('BRG2302492', 'KG', 0, 7500.00),
            array('BRG2302493', 'KG', 0, 12000.00),
            array('BRG2302494', 'KG', 0, 14863.64),
            array('BRG2302495', 'KG', 0, 14500.00),
            array('BRG2302496', 'CAN', 0, 285000.00),
            array('BRG2302497', 'KG', 0, 13500.00),
            array('BRG2302498', 'KG', 0, 13000.00),
            array('BRG2302499', 'KG', 0, 45000.00),
            array('BRG2302500', 'CAN', 0, 105000.00),
            array('BRG2302501', 'KG', 0, 9333.33),
            array('BRG2302503', 'KG', 0, 125000.00),
            array('BRG2302504', 'KG', 0, 243000.00),
            array('BRG2302505', 'PACK', 0, 32000.00),
            array('BRG2302506', 'KG', 0, 25000.00),
            array('BRG2302507', 'KG', 0, 20000.00),
            array('BRG2302510', 'KG', 0, 110000.00),
            array('BRG2302511', 'KG', 0, 90000.00),
            array('BRG2302512', 'KG', 0, 60000.00),
            array('BRG2302513', 'KG', 0, 130000.00),
            array('BRG2302514', 'KG', 0, 130000.00),
            array('BRG2302515', 'KG', 0, 145000.00),
            array('BRG2302517', 'KG', 0, 110000.00),
            array('BRG2302518', 'KG', 0, 65000.00),
            array('BRG2302519', 'KG', 0, 138000.00),
            array('BRG2302520', 'KG', 0, 115000.00),
            array('BRG2302521', 'KG', 0, 162000.00),
            array('BRG2302522', 'KG', 0, 175000.00),
            array('BRG2302523', 'KG', 0, 150000.00),
            array('BRG2302526', 'KG', 0, 76000.00),
            array('BRG2302527', 'KG', 0, 115000.00),
            array('BRG2302528', 'KG', 0, 162500.00),
            array('BRG2302529', 'KG', 0, 65000.00),
            array('BRG2302530', 'KG', 0, 85000.00),
            array('BRG2302531', 'KG', 0, 80000.00),
            array('BRG2302532', 'KG', 0, 65000.00),
            array('BRG2302533', 'PACK', 0, 67000.00),
            array('BRG2302534', 'KG', 0, 178000.00),
            array('BRG2302535', 'KG', 0, 64000.00),
            array('BRG2302536', 'KG', 0, 45000.00),
            array('BRG2302537', 'KG', 0, 130000.00),
            array('BRG2302538', 'KG', 0, 250000.00),
            array('BRG2302539', 'KG', 0, 15000.00),
            array('BRG2302553', 'BTL', 0, 109033.00),
            array('BRG2302540', 'LTR', 0, 82205.00),
            array('BRG2302541', 'BTL', 0, 41850.00),
            array('BRG2302543', 'KG', 0, 10000.00),
            array('BRG2302544', 'KG', 0, 17500.00),
            array('BRG2302577', 'BTL', 0, 4197.67),
            array('BRG2302578', 'BTL', 0, 4197.67),
            array('BRG2302579', 'BTL', 0, 4197.67),
            array('BRG2302545', 'KG', 0, 40000.00)
        );
        
        $kode = 'SO23070005';

        // $m_so = new \Model\Storage\StokOpname_model();
        // $d_so = $m_so->where('kode_stok_opname', $kode)->first();

        // foreach ($data as $k_li => $v_li) {
        //     $m_sod = new \Model\Storage\StokOpnameDet_model();
        //     $d_sod = $m_sod->where('id_header', $d_so->id)->where('item_kode', $v_li[0])->first();

        //     $m_is = new \Model\Storage\ItemSatuan_model();
        //     $d_is = $m_is->where('item_kode', $v_li[0])->where('satuan', 'like', $v_li[1])->first();

        //     $pengali = 0;
        //     if ( $d_is ) {
        //         $pengali = $d_is->pengali;
        //     } else {
        //         cetak_r( $v_li[0].' -> '.$v_li[1] );
        //     }

        //     if ( $d_sod ) {
        //         $m_sod->where('id_header', $d_so->id)->where('item_kode', $v_li[0])->delete();
        //     }

        //     $m_sod = new \Model\Storage\StokOpnameDet_model();
        //     $m_sod->id_header = $d_so->id;
        //     $m_sod->item_kode = $v_li[0];
        //     $m_sod->satuan = $v_li[1];
        //     $m_sod->pengali = $pengali;
        //     $m_sod->jumlah = $v_li[2];
        //     $m_sod->harga = $v_li[3];
        //     $m_sod->save();
        // }

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
                so.kode_stok_opname = '".$kode."'
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
    }
}
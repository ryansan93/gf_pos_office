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
            array('BRG2302001', 'GRAM', 0, 320000.00),
            array('BRG2302002', 'GRAM', 0, 110000.00),
            array('BRG2302003', 'GRAM', 0, 30000.00),
            array('BRG2302004', 'GRAM', 0, 85000.00),
            array('BRG2302006', 'ML', 0, 12000.00),
            array('BRG2302007', 'GRAM', 0, 35000.00),
            array('BRG2302008', 'GRAM', 0, 45000.00),
            array('BRG2302010', 'GRAM', 0, 260000.00),
            array('BRG2302011', 'GRAM', 0, 265000.00),
            array('BRG2302012', 'GRAM', 0, 265000.00),
            array('BRG2302013', 'ML', 0, 156000.00),
            array('BRG2302014', 'GRAM', 0, 52500.00),
            array('BRG2302015', 'GRAM', 0, 85000.00),
            array('BRG2302016', 'GRAM', 0, 66000.00),
            array('BRG2302017', 'GRAM', 0, 31428.57),
            array('BRG2302018', 'GRAM', 0, 35000.00),
            array('BRG2302019', 'ML', 0, 2500.00),
            array('BRG2302021', 'ML', 0, 42000.00),
            array('BRG2302022', 'GRAM', 0, 14000.00),
            array('BRG2302024', 'GRAM', 0, 56000.00),
            array('BRG2302025', 'GRAM', 0, 20000.00),
            array('BRG2302026', 'GRAM', 0, 15000.00),
            array('BRG2302028', 'GRAM', 0, 312000.00),
            array('BRG2302029', 'GRAM', 0, 38500.00),
            array('BRG2302030', 'GRAM', 0, 110000.00),
            array('BRG2302031', 'EKOR', 0, 775000.00),
            array('BRG2302032', 'GRAM', 0, 28000.00),
            array('BRG2302033', 'GRAM', 0, 14500.00),
            array('BRG2302037', 'GRAM', 0, 65000.00),
            array('BRG2302038', 'GRAM', 0, 32000.00),
            array('BRG2302039', 'GRAM', 0, 27500.00),
            array('BRG2302042', 'GRAM', 0, 39000.00),
            array('BRG2302041', 'GRAM', 0, 32000.00),
            array('BRG2302043', 'GRAM', 0, 16000.00),
            array('BRG2302045', 'GRAM', 0, 37000.00),
            array('BRG2302044', 'GRAM', 0, 39000.00),
            array('BRG2302046', 'GRAM', 0, 42727.00),
            array('BRG2302550', 'GRAM', 0, 10000.00),
            array('BRG2302048', 'EKOR', 0, 75000.00),
            array('BRG2302049', 'GRAM', 0, 130000.00),
            array('BRG2302050', 'GRAM', 0, 41000.00),
            array('BRG2302051', 'GRAM', 0, 47500.00),
            array('BRG2302052', 'GRAM', 0, 14550.00),
            array('BRG2302055', 'GRAM', 0, 91700.00),
            array('BRG2302056', 'GRAM', 0, 14250.00),
            array('BRG2302057', 'GRAM', 0, 81800.00),
            array('BRG2302058', 'GRAM', 0, 66900.00),
            array('BRG2302059', 'GRAM', 0, 67000.00),
            array('BRG2302060', 'GRAM', 0, 53500.00),
            array('BRG2302061', 'GRAM', 0, 10000.00),
            array('BRG2302062', 'GRAM', 0, 15000.00),
            array('BRG2302063', 'GRAM', 0, 14000.00),
            array('BRG2302064', 'GRAM', 0, 35000.00),
            array('BRG2302065', 'GRAM', 0, 11500.00),
            array('BRG2302067', 'GRAM', 0, 16000.00),
            array('BRG2302068', 'ML', 0, 15250.00),
            array('BRG2302070', 'GRAM', 0, 24000.00),
            array('BRG2302069', 'GRAM', 0, 22000.00),
            array('BRG2302071', 'GRAM', 0, 19500.00),
            array('BRG2302072', 'GRAM', 0, 128750.00),
            array('BRG2302066', 'GRAM', 0, 13200.00),
            array('BRG2302073', 'GRAM', 0, 280000.00),
            array('BRG2302074', 'GRAM', 0, 9000.00),
            array('BRG2302076', 'GRAM', 0, 198000.00),
            array('BRG2302077', 'GRAM', 0, 25000.00),
            array('BRG2302078', 'GRAM', 0, 146000.00),
            array('BRG2302080', 'GRAM', 0, 13500.00),
            array('BRG2302081', 'GRAM', 0, 19000.00),
            array('BRG2302083', 'GRAM', 0, 47500.00),
            array('BRG2302084', 'GRAM', 0, 28000.00),
            array('BRG2302086', 'GRAM', 0, 20000.00),
            array('BRG2302092', 'EKOR', 0, 28000.00),
            array('BRG2302094', 'GRAM', 0, 186500.00),
            array('BRG2302095', 'GRAM', 0, 27500.00),
            array('BRG2302546', 'PCS', 0, 1600.00),
            array('BRG2302096', 'GRAM', 0, 30500.00),
            array('BRG2302097', 'GRAM', 0, 24500.00),
            array('BRG2302100', 'GRAM', 0, 20000.00),
            array('BRG2302099', 'GRAM', 0, 38000.00),
            array('BRG2302101', 'GRAM', 0, 37000.00),
            array('BRG2302102', 'PCS', 0, 8000.00),
            array('BRG2302103', 'ML', 0, 95000.00),
            array('BRG2302106', 'GRAM', 0, 35000.00),
            array('BRG2302105', 'GRAM', 0, 46000.00),
            array('BRG2302107', 'GRAM', 0, 4500.00),
            array('BRG2302109', 'GRAM', 0, 88000.00),
            array('BRG2302111', 'GRAM', 0, 49000.00),
            array('BRG2302112', 'GRAM', 0, 62000.00),
            array('BRG2302113', 'GRAM', 0, 72100.00),
            array('BRG2302114', 'GRAM', 0, 14750.00),
            array('BRG2302115', 'GRAM', 0, 53400.00),
            array('BRG2302116', 'GRAM', 0, 17182.00),
            array('BRG2302117', 'GRAM', 0, 47000.00),
            array('BRG2302118', 'GRAM', 0, 57500.00),
            array('BRG2302119', 'GRAM', 0, 18750.00),
            array('BRG2302120', 'GRAM', 0, 17500.00),
            array('BRG2302121', 'GRAM', 0, 80000.00),
            array('BRG2302123', 'GRAM', 0, 1540909.00),
            array('BRG2302563', 'CAN', 0, 3901.02),
            array('BRG2302564', 'CAN', 0, 3916.43),
            array('BRG2302126', 'GRAM', 0, 86000.00),
            array('BRG2302128', 'GRAM', 0, 75000.00),
            array('BRG2302130', 'GRAM', 0, 22450.00),
            array('BRG2302132', 'ML', 0, 86205.00),
            array('BRG2302133', 'GRAM', 0, 19000.00),
            array('BRG2302135', 'ML', 0, 87000.00),
            array('BRG2302137', 'GRAM', 0, 68000.00),
            array('BRG2302558', 'ML', 0, 96500.00),
            array('BRG2302140', 'GRAM', 0, 49000.00),
            array('BRG2302141', 'EKOR', 0, 75000.00),
            array('BRG2302142', 'EKOR', 0, 55000.00),
            array('BRG2302144', 'EKOR', 0, 75000.00),
            array('BRG2302145', 'GRAM', 0, 95000.00),
            array('BRG2302146', 'GRAM', 0, 85000.00),
            array('BRG2302147', 'GRAM', 0, 220000.00),
            array('BRG2302148', 'GRAM', 0, 145000.00),
            array('BRG2302149', 'GRAM', 0, 120000.00),
            array('BRG2302151', 'GRAM', 0, 85000.00),
            array('BRG2302152', 'GRAM', 0, 85000.00),
            array('BRG2302153', 'GRAM', 0, 75000.00),
            array('BRG2302154', 'GRAM', 0, 24000.00),
            array('BRG2302155', 'GRAM', 0, 62500.00),
            array('BRG2302156', 'GRAM', 0, 23750.00),
            array('BRG2302157', 'GRAM', 0, 105000.00),
            array('BRG2302158', 'GRAM', 0, 481000.00),
            array('BRG2302159', 'GRAM', 0, 25000.00),
            array('BRG2302160', 'IKAT', 0, 10000.00),
            array('BRG2302161', 'GRAM', 0, 25000.00),
            array('BRG2302162', 'PACK', 0, 20000.00),
            array('BRG2302163', 'IKAT', 0, 3000.00),
            array('BRG2302164', 'GRAM', 0, 15000.00),
            array('BRG2302165', 'GRAM', 0, 100000.00),
            array('BRG2302166', 'KG', 0, 37500.00),
            array('BRG2302167', 'KG', 0, 17500.00),
            array('BRG2302168', 'KG', 0, 8500.00),
            array('BRG2302169', 'IKAT', 0, 2000.00),
            array('BRG2302170', 'GRAM', 0, 13000.00),
            array('BRG2302171', 'GRAM', 0, 10500.00),
            array('BRG2302172', 'GRAM', 0, 3000.00),
            array('BRG2302173', 'GRAM', 0, 8000.00),
            array('BRG2302174', 'GRAM', 0, 10000.00),
            array('BRG2302175', 'GRAM', 0, 12000.00),
            array('BRG2302176', 'GRAM', 0, 25000.00),
            array('BRG2302177', 'GRAM', 0, 12500.00),
            array('BRG2302565', 'PCS', 0, 13000.00),
            array('BRG2302178', 'GRAM', 0, 156250.00),
            array('BRG2302592', 'PACK', 0, 31450.00),
            array('BRG2302182', 'GRAM', 0, 8000.00),
            array('BRG2302566', 'BTL', 0, 4000.00),
            array('BRG2302183', 'GRAM', 0, 11500.00),
            array('BRG2302184', 'GRAM', 0, 33500.00),
            array('BRG2302187', 'PACK', 0, 118967.00),
            array('BRG2302547', 'GRAM', 0, 22000.00),
            array('BRG2302190', 'PCS', 0, 10000.00),
            array('BRG2302593', 'PACK', 0, 23912.50),
            array('BRG2302567', 'BTL', 0, 22525.00),
            array('BRG2302197', 'GRAM', 0, 40000.00),
            array('BRG2302198', 'GRAM', 0, 26000.00),
            array('BRG2302200', 'ML', 0, 33000.00),
            array('BRG2302202', 'GRAM', 0, 12500.00),
            array('BRG2302203', 'PACK', 0, 26000.00),
            array('BRG2302204', 'GRAM', 0, 90000.00),
            array('BRG2302205', 'GRAM', 0, 140000.00),
            array('BRG2302208', 'GRAM', 0, 47000.00),
            array('BRG2302207', 'GRAM', 0, 50000.00),
            array('BRG2302209', 'GRAM', 0, 36000.00),
            array('BRG2302210', 'GRAM', 0, 35000.00),
            array('BRG2302212', 'GRAM', 0, 50000.00),
            array('BRG2302213', 'GRAM', 0, 125000.00),
            array('BRG2302214', 'GRAM', 0, 265000.00),
            array('BRG2302215', 'GRAM', 0, 55000.00),
            array('BRG2302216', 'GRAM', 0, 19000.00),
            array('BRG2302217', 'GRAM', 0, 7000.00),
            array('BRG2302218', 'GRAM', 0, 16000.00),
            array('BRG2302219', 'GRAM', 0, 1450.00),
            array('BRG2302220', 'GRAM', 0, 14000.00),
            array('BRG2302222', 'GRAM', 0, 37000.00),
            array('BRG2302223', 'GRAM', 0, 16000.00),
            array('BRG2302224', 'GRAM', 0, 285000.00),
            array('BRG2302225', 'GRAM', 0, 55000.00),
            array('BRG2302227', 'GRAM', 0, 13750.00),
            array('BRG2302229', 'GRAM', 0, 40000.00),
            array('BRG2302232', 'GRAM', 0, 65000.00),
            array('BRG2302557', 'GRAM', 0, 35000.00),
            array('BRG2302236', 'GRAM', 0, 10000.00),
            array('BRG2302238', 'GRAM', 0, 19000.00),
            array('BRG2302239', 'KG', 0, 35000.00),
            array('BRG2302241', 'GRAM', 0, 65000.00),
            array('BRG2302242', 'GRAM', 0, 30000.00),
            array('BRG2302243', 'EKOR', 0, 1487550.00),
            array('BRG2302244', 'GRAM', 0, 8000.00),
            array('BRG2302245', 'GRAM', 0, 18000.00),
            array('BRG2302246', 'GRAM', 0, 240000.00),
            array('BRG2302247', 'GRAM', 0, 37500.00),
            array('BRG2302250', 'ML', 0, 8500.00),
            array('BRG2302251', 'ML', 0, 24000.00),
            array('BRG2302253', 'ML', 0, 15250.00),
            array('BRG2302254', 'GRAM', 0, 19500.00),
            array('BRG2302255', 'GRAM', 0, 12500.00),
            array('BRG2302259', 'KG', 0, 12500.00),
            array('BRG2302260', 'G', 0, 46500.00),
            array('BRG2302261', 'ikat', 0, 3500.00),
            array('BRG2302262', 'GRAM', 0, 15000.00),
            array('BRG2302264', 'GRAM', 0, 32500.00),
            array('BRG2302265', 'KG', 0, 11666.94),
            array('BRG2302266', 'GRAM', 0, 22000.00),
            array('BRG2302268', 'GRAM', 0, 10000.00),
            array('BRG2302269', 'GRAM', 0, 10000.00),
            array('BRG2302267', 'GRAM', 0, 13500.00),
            array('BRG2302270', 'GRAM', 0, 35000.00),
            array('BRG2302271', 'GRAM', 0, 23000.00),
            array('BRG2302272', 'GRAM', 0, 160000.00),
            array('BRG2302273', 'GRAM', 0, 55000.00),
            array('BRG2302274', 'GRAM', 0, 22000.00),
            array('BRG2302275', 'GRAM', 0, 10000.00),
            array('BRG2302276', 'GRAM', 0, 8000.00),
            array('BRG2302277', 'GRAM', 0, 6000.00),
            array('BRG2302278', 'GRAM', 0, 9000.00),
            array('BRG2302279', 'GRAM', 0, 12000.00),
            array('BRG2302281', 'GRAM', 0, 12000.00),
            array('BRG2302282', 'GRAM', 0, 77000.00),
            array('BRG2302283', 'GRAM', 0, 92000.00),
            array('BRG2302285', 'GRAM', 0, 35000.00),
            array('BRG2302287', 'GRAM', 0, 24500.00),
            array('BRG2302289', 'GRAM', 0, 22500.00),
            array('BRG2302290', 'GRAM', 0, 12000.00),
            array('BRG2302292', 'GRAM', 0, 224000.00),
            array('BRG2302294', 'GRAM', 0, 71309.00),
            array('BRG2302296', 'GRAM', 0, 35000.00),
            array('BRG2302297', 'GRAM', 0, 30000.00),
            array('BRG2302295', 'GRAM', 0, 6500.00),
            array('BRG2302298', 'GRAM', 0, 25000.00),
            array('BRG2302302', 'GRAM', 0, 24000.00),
            array('BRG2302303', 'GRAM', 0, 12000.00),
            array('BRG2302304', 'GRAM', 0, 34000.00),
            array('BRG2302589', 'PACK', 0, 27900.00),
            array('BRG2302306', 'GRAM', 0, 8000.00),
            array('BRG2302307', 'GRAM', 0, 85000.00),
            array('BRG2302308', 'GRAM', 0, 25000.00),
            array('BRG2302309', 'GRAM', 0, 7000.00),
            array('BRG2302311', 'GRAM', 0, 45000.00),
            array('BRG2302312', 'GRAM', 0, 6000.00),
            array('BRG2302313', 'PCS', 0, 1100.00),
            array('BRG2302314', 'GRAM', 0, 30000.00),
            array('BRG2302315', 'GRAM', 0, 95000.00),
            array('BRG2302316', 'GRAM', 0, 55000.00),
            array('BRG2302317', 'GRAM', 0, 7500.00),
            array('BRG2302319', 'PCS', 0, 1800.00),
            array('BRG2302320', 'GRAM', 0, 88288.00),
            array('BRG2302321', 'GRAM', 0, 81818.00),
            array('BRG2302559', 'GRAM', 0, 22000.00),
            array('BRG2302323', 'ML', 0, 31250.00),
            array('BRG2302325', 'GRAM', 0, 30000.00),
            array('BRG2302326', 'GRAM', 0, 18500.00),
            array('BRG2302327', 'GRAM', 0, 5000.00),
            array('BRG2302328', 'ML', 0, 115000.00),
            array('BRG2302331', 'GRAM', 0, 131000.00),
            array('BRG2302333', 'GRAM', 0, 13500.00),
            array('BRG2302334', 'GRAM', 0, 110000.00),
            array('BRG2302336', 'GRAM', 0, 4000.00),
            array('BRG2302338', 'GRAM', 0, 13500.00),
            array('BRG2302340', 'GRAM', 0, 80000.00),
            array('BRG2302345', 'ML', 0, 35000.00),
            array('BRG2302346', 'GRAM', 0, 108162.90),
            array('BRG2302348', 'ML', 0, 14575.00),
            array('BRG2302349', 'ML', 0, 31000.00),
            array('BRG2302548', 'GRAM', 0, 32000.00),
            array('BRG2302352', 'PCS', 0, 5000.00),
            array('BRG2302353', 'GRAM', 0, 55000.00),
            array('BRG2302354', 'GRAM', 0, 11000.00),
            array('BRG2302356', 'GRAM', 0, 120000.00),
            array('BRG2302362', 'GRAM', 0, 52500.00),
            array('BRG2302363', 'GRAM', 0, 60000.00),
            array('BRG2302364', 'GRAM', 0, 74500.00),
            array('BRG2302365', 'GRAM', 0, 20750.00),
            array('BRG2302367', 'GRAM', 0, 75000.00),
            array('BRG2302368', 'GRAM', 0, 110000.00),
            array('BRG2302370', 'GRAM', 0, 12000.00),
            array('BRG2302372', 'GRAM', 0, 35000.00),
            array('BRG2302371', 'GRAM', 0, 20000.00),
            array('BRG2302376', 'GRAM', 0, 1545.00),
            array('BRG2302377', 'SISIR', 0, 31500.00),
            array('BRG2302378', 'SISIR', 0, 30000.00),
            array('BRG2302379', 'SISIR', 0, 15000.00),
            array('BRG2302380', 'SSR', 0, 85000.00),
            array('BRG2302381', 'TANDON', 0, 85000.00),
            array('BRG2302382', 'SISIR', 0, 45000.00),
            array('BRG2302383', 'PCS', 0, 5000.00),
            array('BRG2302384', 'GRAM', 0, 165000.00),
            array('BRG2302385', 'GRAM', 0, 22400.00),
            array('BRG2302386', 'GRAM', 0, 31982.00),
            array('BRG2302574', 'BTL', 0, 5433.33),
            array('BRG2302555', 'GRAM', 0, 42500.00),
            array('BRG2302387', 'GRAM', 0, 95000.00),
            array('BRG2302390', 'SLICE', 0, 42000.00),
            array('BRG2302556', 'ML', 0, 450000.00),
            array('BRG2302394', 'GRAM', 0, 13000.00),
            array('BRG2302397', 'GRAM', 0, 27500.00),
            array('BRG2302585', 'PACK', 0, 29100.00),
            array('BRG2302401', 'GRAM', 0, 49500.00),
            array('BRG2302405', 'ML', 0, 19000.00),
            array('BRG2302409', 'ML', 0, 48750.00),
            array('BRG2302411', 'ML', 0, 52500.00),
            array('BRG2302412', 'ML', 0, 55000.00),
            array('BRG2302413', 'ML', 0, 60000.00),
            array('BRG2302414', 'GRAM', 0, 27000.00),
            array('BRG2302415', 'GRAM', 0, 10500.00),
            array('BRG2302416', 'GRAM', 0, 10000.00),
            array('BRG2302417', 'GRAM', 0, 10000.00),
            array('BRG2302418', 'GRAM', 0, 26000.00),
            array('BRG2302419', 'GRAM', 0, 172500.00),
            array('BRG2302420', 'GRAM', 0, 12500.00),
            array('BRG2302421', 'GRAM', 0, 35000.00),
            array('BRG2302423', 'GRAM', 0, 16000.00),
            array('BRG2302422', 'GRAM', 0, 95000.00),
            array('BRG2302424', 'GRAM', 0, 25000.00),
            array('BRG2302426', 'GRAM', 0, 9000.00),
            array('BRG2302428', 'GRAM', 0, 10000.00),
            array('BRG2302427', 'GRAM', 0, 7000.00),
            array('BRG2302429', 'PCS', 0, 0.00),
            array('BRG2302430', 'GRAM', 0, 600000.00),
            array('BRG2302431', 'GRAM', 0, 2500000.00),
            array('BRG2302432', 'GRAM', 0, 125000.00),
            array('BRG2302433', 'GRAM', 0, 175000.00),
            array('BRG2302434', 'GRAM', 0, 19000.00),
            array('BRG2302435', 'ML', 0, 68500.00),
            array('BRG2302436', 'GRAM', 0, 92700.00),
            array('BRG2302439', 'GRAM', 0, 12500.00),
            array('BRG2302442', 'GRAM', 0, 12500.00),
            array('BRG2302446', 'GRAM', 0, 160000.00),
            array('BRG2302447', 'GRAM', 0, 87000.00),
            array('BRG2302448', 'GRAM', 0, 35000.00),
            array('BRG2302449', 'GRAM', 0, 17000.00),
            array('BRG2302450', 'GRAM', 0, 22000.00),
            array('BRG2302452', 'GRAM', 0, 62000.00),
            array('BRG2302399', 'PCS', 0, 3500.00),
            array('BRG2302460', 'PCS', 0, 3500.00),
            array('BRG2302461', 'PCS', 0, 4800.00),
            array('BRG2302462', 'GRAM', 0, 145000.00),
            array('BRG2302464', 'GRAM', 0, 23000.00),
            array('BRG2302463', 'GRAM', 0, 34000.00),
            array('BRG2302465', 'GRAM', 0, 20000.00),
            array('BRG2302466', 'GRAM', 0, 51000.00),
            array('BRG2302467', 'GRAM', 0, 8500.00),
            array('BRG2302468', 'GRAM', 0, 20000.00),
            array('BRG2302471', 'PCS', 0, 4000.00),
            array('BRG2302470', 'PCS', 0, 4000.00),
            array('BRG2302473', 'PCS', 0, 15000.00),
            array('BRG2302474', 'PCS', 0, 600.00),
            array('BRG2302475', 'PAX', 0, 4500.00),
            array('BRG2302476', 'GRAM', 0, 290000.00),
            array('BRG2302478', 'GRAM', 0, 28000.00),
            array('BRG2302481', 'GRAM', 0, 28000.00),
            array('BRG2302482', 'GRAM', 0, 61273.00),
            array('BRG2302487', 'GRAM', 0, 8800.00),
            array('BRG2302488', 'GRAM', 0, 25000.00),
            array('BRG2302551', 'ML', 0, 45000.00),
            array('BRG2302491', 'GRAM', 0, 5000.00),
            array('BRG2302492', 'GRAM', 0, 7500.00),
            array('BRG2302493', 'GRAM', 0, 12000.00),
            array('BRG2302494', 'GRAM', 0, 14863.64),
            array('BRG2302495', 'GRAM', 0, 14500.00),
            array('BRG2302496', 'GRAM', 0, 285000.00),
            array('BRG2302497', 'GRAM', 0, 13500.00),
            array('BRG2302498', 'GRAM', 0, 13000.00),
            array('BRG2302499', 'GRAM', 0, 45000.00),
            array('BRG2302500', 'GRAM', 0, 105000.00),
            array('BRG2302501', 'GRAM', 0, 9333.33),
            array('BRG2302503', 'GRAM', 0, 125000.00),
            array('BRG2302504', 'GRAM', 0, 243000.00),
            array('BRG2302505', 'GRAM', 0, 32000.00),
            array('BRG2302506', 'GRAM', 0, 25000.00),
            array('BRG2302507', 'GRAM', 0, 20000.00),
            array('BRG2302554', 'ML', 0, 32500.00),
            array('BRG2302510', 'GRAM', 0, 110000.00),
            array('BRG2302511', 'GRAM', 0, 90000.00),
            array('BRG2302512', 'GRAM', 0, 60000.00),
            array('BRG2302513', 'GRAM', 0, 130000.00),
            array('BRG2302514', 'GRAM', 0, 130000.00),
            array('BRG2302515', 'GRAM', 0, 145000.00),
            array('BRG2302517', 'GRAM', 0, 110000.00),
            array('BRG2302518', 'GRAM', 0, 65000.00),
            array('BRG2302519', 'GRAM', 0, 138000.00),
            array('BRG2302520', 'GRAM', 0, 115000.00),
            array('BRG2302521', 'GRAM', 0, 162000.00),
            array('BRG2302522', 'GRAM', 0, 175000.00),
            array('BRG2302523', 'GRAM', 0, 150000.00),
            array('BRG2302526', 'GRAM', 0, 76000.00),
            array('BRG2302527', 'GRAM', 0, 115000.00),
            array('BRG2302528', 'GRAM', 0, 162500.00),
            array('BRG2302529', 'GRAM', 0, 65000.00),
            array('BRG2302530', 'GRAM', 0, 85000.00),
            array('BRG2302531', 'GRAM', 0, 80000.00),
            array('BRG2302532', 'GRAM', 0, 65000.00),
            array('BRG2302533', 'GRAM', 0, 67000.00),
            array('BRG2302534', 'GRAM', 0, 178000.00),
            array('BRG2302535', 'GRAM', 0, 64000.00),
            array('BRG2302536', 'GRAM', 0, 45000.00),
            array('BRG2302537', 'GRAM', 0, 130000.00),
            array('BRG2302538', 'GRAM', 0, 250000.00),
            array('BRG2302539', 'GRAM', 0, 15000.00),
            array('BRG2302540', 'GRAM', 0, 82205.00),
            array('BRG2302541', 'ML', 0, 41850.00),
            array('BRG2302544', 'GRAM', 0, 17500.00),
            array('BRG2302543', 'GRAM', 0, 10000.00),
            array('BRG2302553', 'GRAM', 0, 109033.00),
            array('BRG2302577', 'BTL', 0, 4197.67),
            array('BRG2302578', 'BTL', 0, 4197.67),
            array('BRG2302579', 'BTL', 0, 4197.67),
            array('BRG2302545', 'GRAM', 0, 40000.00),
            array('BRG2302549', 'GRAM', 0, 35000.00)
        );
        
        $kode = 'SO23070005';

        $m_so = new \Model\Storage\StokOpname_model();
        $d_so = $m_so->where('kode_stok_opname', $kode)->first();

        foreach ($data as $k_li => $v_li) {
            $m_sod = new \Model\Storage\StokOpnameDet_model();
            $d_sod = $m_sod->where('id_header', $d_so->id)->where('item_kode', $v_li[0])->first();

            $m_is = new \Model\Storage\ItemSatuan_model();
            $d_is = $m_is->where('item_kode', $v_li[0])->where('satuan', 'like', $v_li['1'])->first();

            $pengali = 0;
            if ( $d_is ) {
                $pengali = $d_is->pengali;
            }

            if ( $d_sod ) {
                $m_sod->where('id_header', $d_so->id)->where('item_kode', $v_li[0])->delete();
            }

            $m_sod = new \Model\Storage\StokOpnameDet_model();
            $m_sod->id_header = $d_so->id;
            $m_sod->item_kode = $v_li['0'];
            $m_sod->satuan = $v_li['1'];
            $m_sod->pengali = $v_li['pengali'];
            $m_sod->jumlah = $v_li['2'];
            $m_sod->harga = $v_li['3'];
            $m_sod->save();

        }

        // $m_conf = new \Model\Storage\Conf();

        // $tgl_transaksi = null;
        // $gudang = null;
        // $barang = null;

        // $sql_tgl_dan_gudang = "
        //     select so.* from stok_opname so
        //     where
        //         so.kode_stok_opname = '".$kode."'
        // ";
        // $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
        // if ( $d_tgl_dan_gudang->count() > 0 ) {
        //     $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
        //     $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
        //     $gudang = $d_tgl_dan_gudang['gudang_kode'];
        // }

        // $sql_barang = "
        //     select so.tanggal, sod.item_kode from stok_opname_det sod
        //     right join
        //         stok_opname so
        //         on
        //             so.id = sod.id_header
        //     where
        //         so.kode_stok_opname = '".$kode."' and
        //         sod.jumlah > 0
        //     group by
        //         so.tanggal,
        //         sod.item_kode
        // ";
        // $d_barang = $m_conf->hydrateRaw( $sql_barang );
        // if ( $d_barang->count() > 0 ) {
        //     $d_barang = $d_barang->toArray();

        //     foreach ($d_barang as $key => $value) {
        //         $barang[] = $value['item_kode'];
        //     }
        // }

        // $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

        // $d_conf = $m_conf->hydrateRaw($sql);
    }
}
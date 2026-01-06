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
        $so_id = (isset($params['so_id']) && !empty($params['so_id'])) ? $params['so_id'] : 0;

        $sql_group_item = null;
        if ( !empty($group_item) ) {
            $sql_group_item = "and gi.kode in ('".implode("', '", $group_item)."')";
        }

        $sql_so_item = "
            left join
                (
                    select * from stok_opname_det sod
                    where
                        sod.id_header = ".$so_id."
                ) sod
                on
                    i.kode = sod.item_kode
        ";

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                i.kode,
                i.nama,
                gi.nama as nama_group,
                CASE
                    WHEN ( ".$so_id." > 0 ) THEN
                        sod.harga
                    ELSE
                        sh.harga
                END as harga,
                CASE
                    WHEN ( ".$so_id." > 0 ) THEN
                        sod.jumlah
                    ELSE
                        s.jumlah
                END as jumlah,
                CASE
                    WHEN ( ".$so_id." > 0 ) THEN
                        sod.satuan
                    ELSE
                        ''
                END as d_satuan,
                CASE
                    WHEN ( ".$so_id." > 0 ) THEN
                        sod.pengali
                    ELSE
                        0
                END as d_pengali
            from item i
            left join
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
            ".$sql_so_item."
            where
                i.kode is not null and
                i.nama is not null
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

    public function editForm($id)
    {
        $m_so = new \Model\Storage\StokOpname_model();
        $d_so = $m_so->where('id', $id)->first();

        $data = null;
        if ( $d_so ) {
            $data = $d_so->toArray();
        }

        $content['group_item'] = $this->getGroupItem();
        $content['gudang'] = $this->getGudang();
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

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
                $m_sod->satuan_old = isset($v_li['satuan_old']) ? $v_li['satuan_old'] : null;
                $m_sod->pengali_old = (isset($v_li['pengali_old']) && $v_li['pengali_old'] > 0) ? $v_li['pengali_old'] : 0;
                $m_sod->jumlah_old = (isset($v_li['jumlah_old']) && $v_li['jumlah_old'] > 0) ? $v_li['jumlah_old'] : 0;
                $m_sod->harga_old = (isset($v_li['harga_old']) && $v_li['harga_old'] > 0) ? $v_li['harga_old'] : 0;
                $m_sod->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_so, $deskripsi_log );

            $tanggal = $params['tanggal'];

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'kode' => $kode_stok_opname,
                'tanggal' => $tanggal,
                'kode_gudang' => $params['gudang_kode'],
                'delete' => 0
            );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_so = new \Model\Storage\StokOpname_model();
            $d_so_old = $m_so->where('id', $params['id'])->first();

            $kode_gudang = $d_so_old->gudang_kode;
            if ( $kode_gudang != $params['gudang_kode'] ) {
                $kode_gudang = $d_so_old->gudang_kode.','.$kode_gudang;
            }

            $m_so->where('id', $params['id'])->update(
                array(
                    'tanggal' => $params['tanggal'],
                    'gudang_kode' => $params['gudang_kode']
                )
            );

            $m_sod = new \Model\Storage\StokOpnameDet_model();
            $m_sod->where('id_header', $params['id'])->delete();

            foreach ($params['list_item'] as $k_li => $v_li) {
                $m_sod = new \Model\Storage\StokOpnameDet_model();
                $m_sod->id_header = $params['id'];
                $m_sod->item_kode = $v_li['item_kode'];
                $m_sod->satuan = $v_li['satuan'];
                $m_sod->pengali = $v_li['pengali'];
                $m_sod->jumlah = $v_li['jumlah'];
                $m_sod->harga = $v_li['harga'];
                $m_sod->satuan_old = isset($v_li['satuan_old']) ? $v_li['satuan_old'] : null;
                $m_sod->pengali_old = (isset($v_li['pengali_old']) && $v_li['pengali_old'] > 0) ? $v_li['pengali_old'] : 0;
                $m_sod->jumlah_old = (isset($v_li['jumlah_old']) && $v_li['jumlah_old'] > 0) ? $v_li['jumlah_old'] : 0;
                $m_sod->harga_old = (isset($v_li['harga_old']) && $v_li['harga_old'] > 0) ? $v_li['harga_old'] : 0;
                $m_sod->save();
            }

            $d_so = $m_so->where('id', $params['id'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_so, $deskripsi_log );

            $tanggal = $params['tanggal'];
            if ( $d_so_old->tanggal < $tanggal ) {
                $tanggal = $d_so_old->tanggal;
            }

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'kode' => $d_so->kode_stok_opname,
                'tanggal' => $tanggal,
                'kode_gudang' => $kode_gudang,
                'delete' => 0
            );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $m_so = new \Model\Storage\StokOpname_model();
            $d_so = $m_so->where('id', $params['id'])->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_so, $deskripsi_log );

            $tanggal = $d_so->tanggal;
            $kode_gudang = $d_so->gudang_kode;

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'kode' => $d_so->kode_stok_opname,
                'tanggal' => $tanggal,
                'kode_gudang' => $kode_gudang,
                'delete' => 1
            );
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
            $tgl_transaksi = $params['tanggal'];
            $delete = (isset($params['delete']) && !empty($params['delete'])) ?: 0;
            $gudang = $params['kode_gudang'];

            $m_conf = new \Model\Storage\Conf();

            $barang = null;

            $sql_tgl_dan_gudang = "
                select so.* from stok_opname so
                where
                    so.kode_stok_opname = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                // $gudang = $d_tgl_dan_gudang['gudang_kode'];
            }

            $sql_barang = "
                select so.tanggal, sod.item_kode from stok_opname_det sod
                right join
                    stok_opname so
                    on
                        so.id = sod.id_header
                where
                    so.kode_stok_opname = '".$kode."' and
                    (sod.jumlah <> sod.jumlah_old or (sod.jumlah * sod.harga) <> (sod.jumlah_old * sod.harga_old)) and
                    (sod.jumlah > 0 or sod.harga > 0)
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

            if ( $delete == 1 ) {
                $m_so = new \Model\Storage\StokOpname_model();
                $d_so = $m_so->where('kode_stok_opname', $kode)->first();

                $m_sod = new \Model\Storage\StokOpnameDet_model();
                $m_sod->where('id_header', $d_so->id)->delete();

                $m_so->where('id', $d_so->id)->delete();
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
        $kode = 'SO23080029';

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
                (sod.jumlah <> sod.jumlah_old or (sod.jumlah * sod.harga) <> (sod.jumlah_old * sod.harga_old)) and
                (sod.jumlah > 0 or sod.harga > 0)
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

    public function cekStokOpname() {
        $arr = array(
            array('2026-01-01', 'GDG THE RA', 'BRG2302002', 'KG', 110000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302410', 'PACK', 48000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302014', 'can', 49670),
            array('2026-01-01', 'GDG THE RA', 'BRG2302020', 'BTL', 39000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302139', 'KG', 50000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302140', 'KG', 50000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302418', 'KG', 35000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302143', 'KG', 38000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302034', 'CAN', 65000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302546', 'PCS', 1600),
            array('2026-01-01', 'GDG THE RA', 'BRG2302036', 'BTL', 17500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302047', 'BTL', 15000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302038', 'KG', 27000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302039', 'KG', 34500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302040', 'PACK', 26000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302043', 'KG', 14500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302045', 'KG', 38000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302053', 'KG', 97014),
            array('2026-01-01', 'GDG THE RA', 'BRG2302061', 'KG', 15000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302066', 'KG', 15440),
            array('2026-01-01', 'GDG THE RA', 'BRG2302084', 'KG', 52500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302086', 'KG', 34500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302096', 'KG', 48000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302099', 'KG', 49000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302101', 'KG', 60000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302402', 'BTL', 16363),
            array('2026-01-01', 'GDG THE RA', 'BRG2302136', 'KG', 75000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302165', 'KG', 125000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302178', 'PAIL', 158900),
            array('2026-01-01', 'GDG THE RA', 'BRG2302180', 'KG', 85000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302185', 'PACK', 18000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302188', 'LTR', 17371),
            array('2026-01-01', 'GDG THE RA', 'BRG2302196', 'KG', 15750),
            array('2026-01-01', 'GDG THE RA', 'BRG2302193', 'KG', 52473),
            array('2026-01-01', 'GDG THE RA', 'BRG2302412', 'BTL', 55000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302454', 'CAN', 11459),
            array('2026-01-01', 'GDG THE RA', 'BRG2302230', 'KG', 18000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302239', 'KG', 42500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302252', 'GLN', 150930),
            array('2026-01-01', 'GDG THE RA', 'BRG2302240', 'KG', 75000),
            array('2026-01-01', 'GDG THE RA', 'BRG2308004', 'PACK', 35000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302257', 'PACK', 16900),
            array('2026-01-01', 'GDG THE RA', 'BRG2302288', 'KG', 95000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302267', 'KG', 15000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302291', 'PACK', 31800),
            array('2026-01-01', 'GDG THE RA', 'BRG2302293', 'PACK', 27550),
            array('2026-01-01', 'GDG THE RA', 'BRG2302295', 'KG', 8000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302299', 'KG', 34000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302300', 'PACK', 28500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302570', 'LTR', 91170),
            array('2026-01-01', 'GDG THE RA', 'BRG2302329', 'PACK', 31000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302082', 'KG', 31000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302324', 'BTL', 80000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302341', 'LTR', 21857),
            array('2026-01-01', 'GDG THE RA', 'BRG2302342', 'BTL', 27000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302343', 'BTL', 70000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302346', 'KG', 31000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302350', 'BTL', 31000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302358', 'BTL', 17000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302362', 'KG', 48000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302364', 'KG', 58000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302366', 'KG', 320000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302378', 'SISIR', 40000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302388', 'BTL', 30000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302389', 'BTL', 18738),
            array('2026-01-01', 'GDG THE RA', 'BRG2302395', 'KG', 165000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302392', 'PACK', 60000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302446', 'KG', 190000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302404', 'PACK', 6500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302400', 'BTL', 31500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302415', 'KG', 14000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302416', 'KG', 8000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302417', 'KG', 14000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302423', 'KG', 45000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302436', 'PACK', 96900),
            array('2026-01-01', 'GDG THE RA', 'BRG2302427', 'KG', 7500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302429', 'PCS', 2800),
            array('2026-01-01', 'GDG THE RA', 'BRG2302441', 'PACK', 16900),
            array('2026-01-01', 'GDG THE RA', 'BRG2302453', 'KG', 58000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302459', 'PCS', 5500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302486', 'KG', 16000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302467', 'KG', 10000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302472', 'KG', 28300),
            array('2026-01-01', 'GDG THE RA', 'BRG2302477', 'KG', 140000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302480', 'KG', 12500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302485', 'KG', 14500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302484', 'KG', 23000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302496', 'CAN', 279900),
            array('2026-01-01', 'GDG THE RA', 'BRG2302508', 'CAN', 19500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302495', 'KG', 9500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302516', 'KG', 130000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302515', 'KG', 158000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302543', 'KG', 17500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302545', 'KG', 30000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302002', 'KG', 110000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302004', 'KG', 73000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302410', 'PACK', 48000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302020', 'BTL', 7800),
            array('2026-01-01', 'GDG-GTR', 'BRG2302414', 'KG', 35000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302139', 'KG', 50000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302140', 'KG', 50000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302144', 'EKOR', 75000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302143', 'KG', 38000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302034', 'CAN', 65000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302546', 'PCS', 1600),
            array('2026-01-01', 'GDG-GTR', 'BRG2302036', 'BTL', 17500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302550', 'KG', 20000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302048', 'EKOR', 75000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302038', 'KG', 27000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302039', 'KG', 34500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302040', 'PACK', 26000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302041', 'KG', 49000),
            array('2026-01-01', 'GDG-GTR', 'BRG2307002', 'KG', 40000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302043', 'KG', 14500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302044', 'KG', 35000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302045', 'KG', 38000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302050', 'CAN', 49000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302053', 'KG', 97014),
            array('2026-01-01', 'GDG-GTR', 'BRG2302062', 'KG', 24000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302066', 'KG', 15440),
            array('2026-01-01', 'GDG-GTR', 'BRG2302075', 'PACK', 21000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302074', 'PACK', 10000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302088', 'PACK', 18750),
            array('2026-01-01', 'GDG-GTR', 'BRG2302089', 'PACK', 19000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302032', 'KG', 39500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302084', 'KG', 52500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302086', 'KG', 34500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302093', 'PCS', 2522),
            array('2026-01-01', 'GDG-GTR', 'BRG2302096', 'KG', 48000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302099', 'KG', 49000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302101', 'KG', 60000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302102', 'PCS', 8000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302402', 'BTL', 16363),
            array('2026-01-01', 'GDG-GTR', 'BRG2302110', 'PACK', 74200),
            array('2026-01-01', 'GDG-GTR', 'BRG2302136', 'KG', 75000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302152', 'KG', 85000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302161', 'KG', 35000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302165', 'KG', 125000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302172', 'IKAT', 4000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302178', 'PAIL', 158900),
            array('2026-01-01', 'GDG-GTR', 'BRG2302212', 'KG', 65273),
            array('2026-01-01', 'GDG-GTR', 'BRG2302179', 'KG', 170000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302180', 'KG', 85000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302547', 'PACK', 37000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302185', 'PACK', 18000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302188', 'LTR', 17371),
            array('2026-01-01', 'GDG-GTR', 'BRG2302191', 'PACK', 4500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302196', 'KG', 15750),
            array('2026-01-01', 'GDG-GTR', 'BRG2302195', 'KG', 22000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302197', 'KG', 47000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302412', 'BTL', 55000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302211', 'KG', 80000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302454', 'CAN', 11459),
            array('2026-01-01', 'GDG-GTR', 'BRG2302218', 'KG', 27500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302221', 'CAN', 14000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302228', 'CAN', 16000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302226', 'KG', 250000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302232', 'KG', 130000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302233', 'BTL', 22000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302230', 'KG', 18000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302557', 'KG', 65000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302236', 'KG', 12000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302239', 'KG', 42500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302104', 'PACK', 54000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302252', 'GLN', 150930),
            array('2026-01-01', 'GDG-GTR', 'BRG2302240', 'KG', 75000),
            array('2026-01-01', 'GDG-GTR', 'BRG2308004', 'PACK', 35000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302263', 'KG', 40000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302257', 'PACK', 16900),
            array('2026-01-01', 'GDG-GTR', 'BRG2302288', 'KG', 95000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302286', 'KG', 98000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302267', 'KG', 15000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302281', 'BTL', 22000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302282', 'KG', 82000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302291', 'PACK', 31800),
            array('2026-01-01', 'GDG-GTR', 'BRG2302293', 'PACK', 27550),
            array('2026-01-01', 'GDG-GTR', 'BRG2302295', 'KG', 8000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302299', 'KG', 34000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302300', 'PACK', 28500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302301', 'PACK', 8000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302303', 'KG', 20000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302305', 'KG', 17000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302304', 'KG', 35000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302309', 'KG', 13000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302317', 'KG', 10000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302319', 'PCS', 1800),
            array('2026-01-01', 'GDG-GTR', 'BRG2302570', 'LTR', 91170),
            array('2026-01-01', 'GDG-GTR', 'BRG2302326', 'KG', 16000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302329', 'PACK', 31000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302332', 'BTL', 11000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302082', 'KG', 31000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302324', 'BTL', 80000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302548', 'KG', 32000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302341', 'LTR', 21857),
            array('2026-01-01', 'GDG-GTR', 'BRG2302343', 'BTL', 70000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302346', 'KG', 31000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302350', 'BTL', 31000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302351', 'PACK', 3500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302360', 'KG', 140000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302362', 'KG', 48000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302364', 'KG', 58000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302378', 'SISIR', 40000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302384', 'CAN', 170000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302388', 'BTL', 30000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302389', 'BTL', 18738),
            array('2026-01-01', 'GDG-GTR', 'BRG2302395', 'KG', 165000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302436', 'PACK', 97610),
            array('2026-01-01', 'GDG-GTR', 'BRG2302404', 'PACK', 6500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302400', 'BTL', 31500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302415', 'KG', 14000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302416', 'KG', 8000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302417', 'KG', 14000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302424', 'KG', 40000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302423', 'KG', 45000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302426', 'KG', 12500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302444', 'PCS', 1975),
            array('2026-01-01', 'GDG-GTR', 'BRG2302429', 'PCS', 2800),
            array('2026-01-01', 'GDG-GTR', 'BRG2302437', 'KG', 29000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302438', 'PACK', 34000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302441', 'PACK', 16900),
            array('2026-01-01', 'GDG-GTR', 'BRG2302461', 'PCS', 5200),
            array('2026-01-01', 'GDG-GTR', 'BRG2302459', 'PCS', 5500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302460', 'PCS', 3500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302486', 'KG', 16000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302467', 'KG', 10000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302468', 'KG', 25000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302470', 'PCS', 4500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302472', 'KG', 28300),
            array('2026-01-01', 'GDG-GTR', 'BRG2302477', 'KG', 140000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302479', 'KG', 14500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302488', 'PACK', 27000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302480', 'KG', 12500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302484', 'KG', 23000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302497', 'KG', 16000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302498', 'KG', 12000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302496', 'CAN', 279900),
            array('2026-01-01', 'GDG-GTR', 'BRG2302508', 'CAN', 19500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302495', 'KG', 9500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302498', 'KG', 12000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302516', 'KG', 130000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302553', 'KG', 112612),
            array('2026-01-01', 'GDG-GTR', 'BRG2302543', 'KG', 17500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302560', 'BTL', 30875),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302561', 'CAN', 20345),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302562', 'BTL', 1356),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302563', 'CAN', 4738),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302568', 'BTL', 41750),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302569', 'BTL', 6249),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302571', 'BTL', 4832),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302572', 'BTL', 6792),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302573', 'BTL', 5250),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302575', 'BTL', 2625),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302576', 'CAN', 4737),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302117', 'PACK', 57000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302122', 'KG', 0),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302120', 'PACK', 18500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302124', 'KG', 300566),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302565', 'pcs', 14000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302188', 'LTR', 17372),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302195', 'KG', 22000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302196', 'KG', 15750),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302454', 'CAN', 11459),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302455', 'CAN', 11459),
            array('2026-01-01', 'GDG.THE RA', 'BRG2307004', 'KG', 28500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302230', 'KG', 18000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302231', 'KG', 18500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302220', 'KG', 13000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302234', 'KG', 32000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302237', 'KG', 42500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302310', 'CAN', 26000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302061', 'KG', 15000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302442', 'PACK', 12000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302426', 'KG', 12000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302425', 'KG', 15500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302458', 'BTL', 26500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302218', 'KG', 27500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302018', 'KG', 30000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302560', 'BTL', 30875),
            array('2026-01-01', 'GDG.GTR', 'BRG2302561', 'CAN', 20345),
            array('2026-01-01', 'GDG.GTR', 'BRG2302562', 'BTL', 1356),
            array('2026-01-01', 'GDG.GTR', 'BRG2302563', 'CAN', 4738),
            array('2026-01-01', 'GDG.GTR', 'BRG2302568', 'BTL', 41750),
            array('2026-01-01', 'GDG.GTR', 'BRG2302569', 'BTL', 0),
            array('2026-01-01', 'GDG.GTR', 'BRG2302571', 'BTL', 4832),
            array('2026-01-01', 'GDG.GTR', 'BRG2302572', 'BTL', 6792),
            array('2026-01-01', 'GDG.GTR', 'BRG2302573', 'BTL', 5250),
            array('2026-01-01', 'GDG.GTR', 'BRG2302575', 'BTL', 2625),
            array('2026-01-01', 'GDG.GTR', 'BRG2302576', 'CAN', 4737),
            array('2026-01-01', 'GDG.GTR', 'BRG2302117', 'PACK', 57000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302122', 'KG', 0),
            array('2026-01-01', 'GDG.GTR', 'BRG2302120', 'PACK', 18500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302124', 'KG', 300566),
            array('2026-01-01', 'GDG.GTR', 'BRG2302565', 'pcs', 14000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302188', 'LTR', 17372),
            array('2026-01-01', 'GDG.GTR', 'BRG2302195', 'KG', 22000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302196', 'KG', 15750),
            array('2026-01-01', 'GDG.GTR', 'BRG2302454', 'CAN', 11459),
            array('2026-01-01', 'GDG.GTR', 'BRG2302455', 'CAN', 11459),
            array('2026-01-01', 'GDG.GTR', 'BRG2307004', 'KG', 28500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302230', 'KG', 18000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302231', 'KG', 18500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302220', 'KG', 13000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302234', 'KG', 32000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302237', 'KG', 42500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302310', 'CAN', 26000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302061', 'KG', 15000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302442', 'PACK', 12000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302426', 'KG', 12000),
            array('2026-01-01', 'GDG.GTR', 'BRG2302425', 'KG', 15500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302458', 'BTL', 26500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302218', 'KG', 27500),
            array('2026-01-01', 'GDG.GTR', 'BRG2302018', 'KG', 30000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302042', 'KG', 0.2, 55000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302131', 'LTR', 0.5, 60000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302134', 'BTL', 0.3, 15000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302186', 'KG', 1.3, 45400),
            array('2026-01-01', 'GDG THE RA', 'BRG2302187', 'PACK', 0.75, 182400),
            array('2026-01-01', 'GDG THE RA', 'BRG2302191', 'PACK', 1.5, 4500),
            array('2026-01-01', 'GDG THE RA', 'BRG2504003', 'PACK', 1, 6000),
            array('2026-01-01', 'GDG THE RA', 'BRG2510005', 'BTL', 1, 89150),
            array('2026-01-01', 'GDG THE RA', 'BRG2302249', 'GLN', 0.25, 77164),
            array('2026-01-01', 'GDG THE RA', 'BRG2302256', 'PACK', 2, 24400),
            array('2026-01-01', 'GDG THE RA', 'BRG2302284', 'BTL', 0.25, 125000),
            array('2026-01-01', 'GDG THE RA', 'BRG2505002', 'PACK', 2, 17500),
            array('2026-01-01', 'GDG THE RA', 'BRG2502001', 'KG', 2, 180000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302335', 'PACK', 1, 14500),
            array('2026-01-01', 'GDG THE RA', 'BRG2302339', 'PACK', 12, 3600),
            array('2026-01-01', 'GDG THE RA', 'BRG2302344', 'BTL', 0.6, 110000),
            array('2026-01-01', 'GDG THE RA', 'BRG2504002', 'BTL', 1, 110000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302352', 'PCS', 1.25, 6500),
            array('2026-01-01', 'GDG THE RA', 'BRG2310006', 'BTL', 1.5, 225000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302386', 'PACK', 1, 35500),
            array('2026-01-01', 'GDG THE RA', 'BRG2310010', 'KG', 1, 55000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302393', 'GLN', 0.4, 255000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302403', 'GLN', 0.25, 136168),
            array('2026-01-01', 'GDG THE RA', 'BRG2302406', 'CAN', 0.15, 110000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302407', 'GLN', 0.3, 105135),
            array('2026-01-01', 'GDG THE RA', 'BRG2302408', 'PACK', 1.5, 16799),
            array('2026-01-01', 'GDG THE RA', 'BRG2302476', 'KG', 3.6, 210000),
            array('2026-01-01', 'GDG THE RA', 'BRG2302493', 'KG', 1.5, 10000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302005', 'PACK', 0.5, 52000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302042', 'KG', 2, 55000),
            array('2026-01-01', 'GDG-GTR', 'BRG2412001', 'PAX', 1, 46950),
            array('2026-01-01', 'GDG-GTR', 'BRG2302131', 'LTR', 2, 60000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302134', 'BTL', 1.5, 15000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302322', 'BTL', 1.25, 46950),
            array('2026-01-01', 'GDG-GTR', 'BRG2302186', 'KG', 2.5, 45400),
            array('2026-01-01', 'GDG-GTR', 'BRG2302187', 'KG', 2, 182400),
            array('2026-01-01', 'GDG-GTR', 'BRG2502005', 'KG', 2.5, 62700),
            array('2026-01-01', 'GDG-GTR', 'BRG2302455', 'CAN', 1, 11459),
            array('2026-01-01', 'GDG-GTR', 'BRG2407004', 'KG', 1.5, 85000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302249', 'GLN', 2, 77164),
            array('2026-01-01', 'GDG-GTR', 'BRG2302256', 'PACK', 2, 24400),
            array('2026-01-01', 'GDG-GTR', 'BRG2302284', 'BTL', 0.8, 125000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302335', 'PACK', 3, 14500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302339', 'PACK', 22, 3600),
            array('2026-01-01', 'GDG-GTR', 'BRG2302344', 'BTL', 1.2, 110000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302386', 'PACK', 0.5, 35500),
            array('2026-01-01', 'GDG-GTR', 'BRG2302393', 'GLN', 0.7, 255000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302408', 'PACK', 3, 16799),
            array('2026-01-01', 'GDG-GTR', 'BRG2302403', 'GLN', 0.7, 136168),
            array('2026-01-01', 'GDG-GTR', 'BRG2302406', 'CAN', 1, 110000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302407', 'GLN', 1.3, 105135),
            array('2026-01-01', 'GDG-GTR', 'BRG2302456', 'PACK', 2.5, 15000),
            array('2026-01-01', 'GDG-GTR', 'BRG2505001', 'BTL', 0.1, 26000),
            array('2026-01-01', 'GDG-GTR', 'BRG2302522', 'KG', 1.5, 175000),
            array('2026-01-01', 'GDG.THE RA', 'BRG2512003', 'CAN', 47, 5500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2409004', 'KG', 0, 270560),
            array('2026-01-01', 'GDG.THE RA', 'BRG2311003', 'BTL', 0, 23625),
            array('2026-01-01', 'GDG.THE RA', 'BRG2512004', 'can', 0, 7069),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302469', 'PCS', 21, 8113),
            array('2026-01-01', 'GDG.THE RA', 'BRG2309001', 'PCS', 312, 285),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302570', 'BTL', 0.5, 182340),
            array('2026-01-01', 'GDG.THE RA', 'BRG2302352', 'PCS', 2, 6500),
            array('2026-01-01', 'GDG.THE RA', 'BRG2402001', 'BARREL', 0, 1733000),
            array('2026-01-01', 'GDG.GTR', 'BRG2512003', 'CAN', 0, 5500),
            array('2026-01-01', 'GDG.GTR', 'BRG2409004', 'KG', 5, 270560),
            array('2026-01-01', 'GDG.GTR', 'BRG2311003', 'BTL', 17, 23625),
            array('2026-01-01', 'GDG.GTR', 'BRG2512004', 'can', 18, 7069),
            array('2026-01-01', 'GDG.GTR', 'BRG2302469', 'PCS', 44, 8113),
            array('2026-01-01', 'GDG.GTR', 'BRG2309001', 'PCS', 313, 285),
            array('2026-01-01', 'GDG.GTR', 'BRG2302570', 'BTL', 2.5, 182340),
            array('2026-01-01', 'GDG.GTR', 'BRG2302352', 'PCS', 0, 6500),
            array('2026-01-01', 'GDG.GTR', 'BRG2402001', 'BARREL', 2, 1733000),
        );

        $err = 0;
        $ket = null;
        foreach ($arr as $key => $value) {
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select i.* from item i
                where
                    i.kode = '".$value['2']."'
            ";
            $d_brg = $m_conf->hydrateRaw( $sql );

            if ( $d_brg->count() > 0 ) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select is.* from item_satuan is
                    where
                        is.item_kode = '".$value['2']."' and
                        is.satuan like '".$value['3']."'
                ";
                $d_is = $m_conf->hydrateRaw( $sql );

                if ( $d_brg->count() > 0 ) {
                } else {
                    $err = 1;
                    if ( empty($ket) ) {
                        $ket = 'SATUAN '.$value['3'].' PADA KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                    } else {
                        $ket .= 'SATUAN '.$value['3'].' PADA KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                    }
                }
            } else {
                $err = 1;
                if ( empty($ket) ) {
                    $ket = 'KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                } else {
                    $ket .= 'KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                }
            }
        }

        if ( $err == 1 ) {
            cetak_r( $ket );
        } else {
            cetak_r( 'DATA LENGKAP' );
        }
    }
}
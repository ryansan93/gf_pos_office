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

    // public function tes()
    // {
    //     $kode = 'SO23080029';

    //     $m_conf = new \Model\Storage\Conf();

    //     $tgl_transaksi = null;
    //     $gudang = null;
    //     $barang = null;

    //     $sql_tgl_dan_gudang = "
    //         select so.* from stok_opname so
    //         where
    //             so.kode_stok_opname = '".$kode."'
    //     ";
    //     $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
    //     if ( $d_tgl_dan_gudang->count() > 0 ) {
    //         $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
    //         $tgl_transaksi = $d_tgl_dan_gudang['tanggal'];
    //         $gudang = $d_tgl_dan_gudang['gudang_kode'];
    //     }

    //     $sql_barang = "
    //         select so.tanggal, sod.item_kode from stok_opname_det sod
    //         right join
    //             stok_opname so
    //             on
    //                 so.id = sod.id_header
    //         where
    //             so.kode_stok_opname = '".$kode."' and
    //             (sod.jumlah <> sod.jumlah_old or (sod.jumlah * sod.harga) <> (sod.jumlah_old * sod.harga_old)) and
    //             (sod.jumlah > 0 or sod.harga > 0)
    //         group by
    //             so.tanggal,
    //             sod.item_kode
    //     ";
    //     $d_barang = $m_conf->hydrateRaw( $sql_barang );
    //     if ( $d_barang->count() > 0 ) {
    //         $d_barang = $d_barang->toArray();

    //         foreach ($d_barang as $key => $value) {
    //             $barang[] = $value['item_kode'];
    //         }
    //     }

    //     $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

    //     cetak_r( $sql, 1 );

    //     // $d_conf = $m_conf->hydrateRaw($sql);
    // }

    public function cekStokOpname() {
        $arr = array(
            array('2026-05-01', 'GDG.GTR', 'BRG2302018', 'KG', 6, 30.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302061', 'KG', 0, 15.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302120', 'PACK', 3, 37.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302122', 'KG', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302124', 'KG', 6, 300.57), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302127', 'KG', 11, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302188', 'LTR', 12, 17.37), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302195', 'KG', 3, 22.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302196', 'KG', 12, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302218', 'KG', 2, 27.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302220', 'KG', 2, 13.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302230', 'KG', 3, 18.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302231', 'KG', 20, 18.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302234', 'KG', 3, 32.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302237', 'KG', 3, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302310', 'CAN', 4, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302352', 'PCS', 0, 6500.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302425', 'KG', 15, 15.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302426', 'KG', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302442', 'PACK', 0, 48.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302454', 'CAN', 11, 29.38), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302455', 'CAN', 13, 29.38), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302458', 'BTL', 3, 57.61), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302469', 'PCS', 47, 540.87), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302497', 'KG', 1.5, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302560', 'BTL', 75, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302561', 'CAN', 89, 20345.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302563', 'CAN', 82, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302565', 'PCS', 60, 14000.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302568', 'BTL', 35, 41750.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302569', 'BTL', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302570', 'BTL', 1, 409.74), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302571', 'BTL', 88, 4832.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302572', 'BTL', 88, 6792.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302573', 'BTL', 146, 5250.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302575', 'BTL', 20, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302576', 'CAN', 29, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2307004', 'KG', 4, 28.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2309001', 'PCS', 500, 285.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2311003', 'BTL', 15, 23625.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2402001', 'BARREL', 1, 1733000.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2409004', 'KG', 7, 270.56), 
            array('2026-05-01', 'GDG.GTR', 'BRG2510007', 'KG', 0.5, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512003', 'CAN', 0, 5500.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512004', 'CAN', 33, 7069.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512005', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512006', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512007', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2601003', 'BTL', 358, 0.00),

            array('2026-05-01', 'GDG.THE RA', 'BRG2302018', 'KG', 1.00, 30.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302061', 'KG', 3.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302120', 'PACK', 1.00, 37.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302122', 'KG', 0.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302124', 'KG', 1.00, 300.57),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302127', 'KG', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302188', 'LTR', 5.00, 17.37),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302195', 'KG', 0.00, 22.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302196', 'KG', 7.00, 15.75),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302218', 'KG', 1.00, 27.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302220', 'KG', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302230', 'KG', 2.00, 18.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302231', 'KG', 10.00, 18.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302234', 'KG', 0.00, 32.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302237', 'KG', 0.00, 42.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302310', 'CAN', 2.00, 26000.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302352', 'PCS', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302425', 'KG', 10.00, 15.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302426', 'KG', 0.00, 12.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302442', 'PACK', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302454', 'CAN', 4.00, 29.38),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302455', 'CAN', 4.00, 29.38),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302458', 'BTL', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302469', 'PCS', 36.00, 540.87),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302560', 'BTL', 39.00, 30875.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302561', 'CAN', 24.00, 20345.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302563', 'CAN', 34.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302565', 'PCS', 0.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302568', 'BTL', 0.00, 41750.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302569', 'BTL', 34.00, 6249.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302570', 'BTL', 1.00, 159.55),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302571', 'BTL', 19.00, 4832.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302572', 'BTL', 25.00, 6792.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302573', 'BTL', 29.00, 5250.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302575', 'BTL', 17.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302576', 'CAN', 32.00, 4737.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2307004', 'KG', 4.00, 28.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2309001', 'PCS', 125.00, 285.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2311003', 'BTL', 17.00, 23625.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2402001', 'BARREL', 0.00, 1733000.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2409004', 'KG', 0.00, 270.56),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512003', 'CAN', 22.00, 5500.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512004', 'CAN', 0.00, 7069.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512005', 'PCS', 50.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512006', 'PCS', 71.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512007', 'PCS', 111.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2601003', 'BTL', 90.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2603001', 'GRAM', 2.00, 0),

            array('2026-05-01', 'GDG-GTR', 'BRG2302002', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302004', 'KG', 10.00, 74.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302005', 'PACK', 0.75, 52000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302020', 'BTL', 2.50, 12.58),
            array('2026-05-01', 'GDG-GTR', 'BRG2302032', 'KG', 1.50, 39.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302034', 'CAN', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302036', 'BTL', 1.50, 875.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302038', 'KG', 3.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302039', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302040', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302041', 'KG', 1.50, 49.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302042', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302043', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302044', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302045', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302048', 'EKOR', 8.00, 75000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302050', 'CAN', 1.00, 122.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302053', 'KG', 1.50, 97.01),
            array('2026-05-01', 'GDG-GTR', 'BRG2302054', 'KG', 0.70, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302062', 'KG', 0.50, 24.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302066', 'KG', 18.00, 15.44),
            array('2026-05-01', 'GDG-GTR', 'BRG2302074', 'PACK', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302075', 'PACK', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302082', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302084', 'KG', 0.80, 52.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302086', 'KG', 2.50, 34.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302088', 'PACK', 8.00, 101.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302089', 'PACK', 6.00, 76.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302093', 'PCS', 12.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302096', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302099', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302101', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302102', 'PCS', 10.00, 8000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302107', 'PACK', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302110', 'PACK', 0.50, 74.20),
            array('2026-05-01', 'GDG-GTR', 'BRG2302120', 'PACK', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302131', 'LTR', 1.80, 60.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302134', 'BTL', 1.30, 25.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302136', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302139', 'KG', 10.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302140', 'KG', 6.00, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302143', 'KG', 22.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302144', 'EKOR', 2.00, 75000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302152', 'KG', 2.50, 85.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302161', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302172', 'IKAT', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302178', 'PAIL', 1.20, 158.90),
            array('2026-05-01', 'GDG-GTR', 'BRG2302179', 'KG', 1.30, 170.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302180', 'KG', 1.50, 85.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302185', 'PACK', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302186', 'KG', 2.50, 45.40),
            array('2026-05-01', 'GDG-GTR', 'BRG2302187', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302188', 'LTR', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302191', 'PACK', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302195', 'KG', 2.30, 22.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302196', 'KG', 11.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302197', 'KG', 3.50, 47.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302211', 'KG', 3.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302218', 'KG', 0.90, 27.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302221', 'CAN', 9.00, 32.94),
            array('2026-05-01', 'GDG-GTR', 'BRG2302224', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302226', 'KG', 0.90, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302228', 'CAN', 6.00, 37.65),
            array('2026-05-01', 'GDG-GTR', 'BRG2302230', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302232', 'KG', 0.50, 130.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302233', 'BTL', 0.25, 275.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302236', 'KG', 1.30, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302237', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302240', 'KG', 6.50, 75.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302247', 'PACK', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302249', 'GLN', 1.50, 13.54),
            array('2026-05-01', 'GDG-GTR', 'BRG2302252', 'GLN', 1.80, 26.48),
            array('2026-05-01', 'GDG-GTR', 'BRG2302256', 'PACK', 2.00, 162.67),
            array('2026-05-01', 'GDG-GTR', 'BRG2302257', 'PACK', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302263', 'KG', 0.80, 40.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302267', 'KG', 5.00, 15.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302271', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302281', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302282', 'KG', 6.00, 82.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302284', 'BTL', 1.20, 78.13),
            array('2026-05-01', 'GDG-GTR', 'BRG2302286', 'KG', 1.00, 98.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302288', 'KG', 1.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302291', 'PACK', 4.00, 83.68),
            array('2026-05-01', 'GDG-GTR', 'BRG2302293', 'PACK', 3.00, 55.10),
            array('2026-05-01', 'GDG-GTR', 'BRG2302295', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302299', 'KG', 4.00, 34.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302300', 'PACK', 1.50, 57.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302301', 'PACK', 3.00, 32.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302303', 'KG', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302305', 'KG', 4.00, 17.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302309', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302317', 'KG', 1.00, 10.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302319', 'PCS', 6.00, 1800.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302324', 'BTL', 0.80, 100.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302326', 'KG', 1.30, 16.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302329', 'PACK', 1.80, 31.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302332', 'BTL', 0.50, 97.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302335', 'PACK', 4.00, 223.08),
            array('2026-05-01', 'GDG-GTR', 'BRG2302339', 'PACK', 38.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302341', 'LTR', 26.00, 21.86),
            array('2026-05-01', 'GDG-GTR', 'BRG2302343', 'BTL', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302344', 'BTL', 1.20, 61.11),
            array('2026-05-01', 'GDG-GTR', 'BRG2302346', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302350', 'BTL', 3.00, 182.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302351', 'PACK', 3.00, 35.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302352', 'PCS', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302360', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302362', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302364', 'KG', 1.50, 58.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302378', 'SISIR', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302386', 'PACK', 1.00, 71.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302388', 'BTL', 3.50, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302389', 'BTL', 0.50, 669.21),
            array('2026-05-01', 'GDG-GTR', 'BRG2302390', 'LOAF', 5.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302393', 'GLN', 0.70, 51.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302395', 'KG', 2.50, 165.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302400', 'BTL', 6.00, 52.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302402', 'BTL', 0.70, 26.18),
            array('2026-05-01', 'GDG-GTR', 'BRG2302403', 'GLN', 1.60, 23.89),
            array('2026-05-01', 'GDG-GTR', 'BRG2302404', 'PACK', 4.00, 36.93),
            array('2026-05-01', 'GDG-GTR', 'BRG2302406', 'CAN', 0.80, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302407', 'GLN', 1.80, 18.44),
            array('2026-05-01', 'GDG-GTR', 'BRG2302408', 'PACK', 3.00, 95.45),
            array('2026-05-01', 'GDG-GTR', 'BRG2302410', 'PACK', 3.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302412', 'BTL', 1.00, 220.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302414', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302415', 'KG', 1.20, 14.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302416', 'KG', 3.00, 8.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302417', 'KG', 3.50, 14.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302423', 'KG', 1.20, 45.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302424', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302425', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302426', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302428', 'KG', 1.20, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302429', 'PCS', 450.00, 2800.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302436', 'PACK', 0.50, 97.61),
            array('2026-05-01', 'GDG-GTR', 'BRG2302437', 'KG', 0.50, 29.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302438', 'PACK', 4.00, 123.19),
            array('2026-05-01', 'GDG-GTR', 'BRG2302441', 'PACK', 2.00, 33.80),
            array('2026-05-01', 'GDG-GTR', 'BRG2302444', 'PCS', 12.00, 1975.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302454', 'CAN', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302455', 'CAN', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302456', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302459', 'PCS', 22.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302460', 'PCS', 26.00, 3500.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302467', 'KG', 1.60, 10.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302468', 'KG', 0.50, 25.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302470', 'PCS', 12.00, 4500.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302472', 'KG', 18.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302475', 'PAX', 7.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302477', 'KG', 12.00, 140.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302479', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302480', 'KG', 8.00, 12.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302484', 'KG', 3.50, 23.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302486', 'KG', 6.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302488', 'PACK', 4.00, 54.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302495', 'KG', 4.00, 9.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302496', 'CAN', 0.50, 186.60),
            array('2026-05-01', 'GDG-GTR', 'BRG2302497', 'KG', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302498', 'KG', 0.25, 12.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302508', 'CAN', 2.50, 105.41),
            array('2026-05-01', 'GDG-GTR', 'BRG2302516', 'KG', 4.50, 130.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302522', 'KG', 1.50, 175.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302542', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302543', 'KG', 5.00, 17.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302546', 'PCS', 750.00, 1600.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302547', 'PACK', 1.00, 74.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302548', 'KG', 2.50, 32.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302550', 'KG', 0.50, 20.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302553', 'KG', 0.25, 112.61),
            array('2026-05-01', 'GDG-GTR', 'BRG2302557', 'KG', 0.15, 65.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302570', 'BTL', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2307002', 'KG', 1.00, 40.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2308004', 'PACK', 4.00, 35000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2403002', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2406001', 'PCS', 12.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2408002', 'GLN', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2408010', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2409001', 'BTL', 1.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2412001', 'PAX', 1.00, 46950.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2502005', 'KG', 1.80, 62.70),
            array('2026-05-01', 'GDG-GTR', 'BRG2502007', 'KG', 1.20, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2505001', 'BTL', 0.10, 26000.00),

            array('2026-05-01', 'GDG THE RA', 'BRG2302002', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302014', 'CAN', 2.00, 248.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302020', 'BTL', 0.25, 62.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302034', 'CAN', 0.20, 130.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302036', 'BTL', 1.00, 875.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302037', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302038', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302039', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302040', 'PACK', 1.50, 104.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302042', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302043', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302045', 'KG', 1.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302047', 'BTL', 1.75, 1875.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302053', 'KG', 0.70, 97.01),
            array('2026-05-01', 'GDG THE RA', 'BRG2302054', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302061', 'KG', 0.50, 15.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302066', 'KG', 12.00, 15.44),
            array('2026-05-01', 'GDG THE RA', 'BRG2302074', 'PACK', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302082', 'KG', 1.60, 31000.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302084', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302086', 'KG', 1.00, 34.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302088', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302096', 'KG', 0.15, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302097', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302099', 'KG', 0.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302101', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302127', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302131', 'LTR', 1.25, 60.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302134', 'BTL', 0.50, 25.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302136', 'KG', 1.50, 75.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302139', 'KG', 6.00, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302140', 'KG', 3.00, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302143', 'KG', 9.00, 38.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302178', 'PAIL', 0.80, 158.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302180', 'KG', 0.40, 85.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302185', 'PACK', 2.50, 36.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302186', 'KG', 1.80, 45.40),
            array('2026-05-01', 'GDG THE RA', 'BRG2302187', 'PACK', 1.20, 80.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302188', 'LTR', 1.50, 17.37),
            array('2026-05-01', 'GDG THE RA', 'BRG2302191', 'PACK', 2.00, 9.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302193', 'KG', 0.20, 52.47),
            array('2026-05-01', 'GDG THE RA', 'BRG2302196', 'KG', 4.50, 15.75),
            array('2026-05-01', 'GDG THE RA', 'BRG2302221', 'CAN', 3.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302224', 'KG', 0.15, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302226', 'KG', 0.10, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302230', 'KG', 0.70, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302237', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302240', 'KG', 2.50, 75.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302249', 'GLN', 0.25, 13.54),
            array('2026-05-01', 'GDG THE RA', 'BRG2302252', 'GLN', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302256', 'PACK', 1.50, 162.67),
            array('2026-05-01', 'GDG THE RA', 'BRG2302257', 'PACK', 3.00, 105.63),
            array('2026-05-01', 'GDG THE RA', 'BRG2302267', 'KG', 2.50, 15.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302288', 'KG', 0.60, 95.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302291', 'PACK', 3.00, 83.68),
            array('2026-05-01', 'GDG THE RA', 'BRG2302293', 'PACK', 1.50, 55.10),
            array('2026-05-01', 'GDG THE RA', 'BRG2302295', 'KG', 2.50, 8.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302299', 'KG', 1.50, 34.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302300', 'PACK', 1.00, 57.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302324', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302329', 'PACK', 0.80, 31.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302335', 'PACK', 3.00, 223.08),
            array('2026-05-01', 'GDG THE RA', 'BRG2302339', 'PACK', 8.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302341', 'LTR', 14.00, 21.86),
            array('2026-05-01', 'GDG THE RA', 'BRG2302342', 'BTL', 0.70, 45.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302343', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302344', 'BTL', 0.60, 61.11),
            array('2026-05-01', 'GDG THE RA', 'BRG2302346', 'KG', 1.80, 31.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302350', 'BTL', 1.50, 182.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302352', 'PCS', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302358', 'BTL', 2.00, 680.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302362', 'KG', 1.50, 48.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302364', 'KG', 1.20, 58.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302366', 'KG', 0.70, 320.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302378', 'SISIR', 0.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302386', 'PACK', 1.00, 71.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302388', 'BTL', 0.60, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302389', 'BTL', 1.00, 669.21),
            array('2026-05-01', 'GDG THE RA', 'BRG2302390', 'LOAF', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302392', 'PACK', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302393', 'GLN', 0.50, 51.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302395', 'KG', 0.50, 165.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302400', 'BTL', 1.00, 52.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302402', 'BTL', 1.00, 26.18),
            array('2026-05-01', 'GDG THE RA', 'BRG2302403', 'GLN', 0.70, 23.89),
            array('2026-05-01', 'GDG THE RA', 'BRG2302404', 'PACK', 1.80, 36.93),
            array('2026-05-01', 'GDG THE RA', 'BRG2302406', 'CAN', 0.20, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302407', 'GLN', 0.80, 18.44),
            array('2026-05-01', 'GDG THE RA', 'BRG2302408', 'PACK', 1.50, 95.45),
            array('2026-05-01', 'GDG THE RA', 'BRG2302410', 'PACK', 1.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302412', 'BTL', 0.25, 220.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302414', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302415', 'KG', 0.70, 14.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302416', 'KG', 1.00, 8.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302417', 'KG', 1.25, 14.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302418', 'KG', 2.00, 35.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302423', 'KG', 1.00, 45.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302424', 'KG', 0.10, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302427', 'KG', 1.80, 7.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302429', 'PCS', 15.00, 2800.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302433', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302436', 'PACK', 0.15, 96.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302441', 'PACK', 3.00, 33.80),
            array('2026-05-01', 'GDG THE RA', 'BRG2302453', 'KG', 0.40, 58.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302454', 'CAN', 1.50, 29.38),
            array('2026-05-01', 'GDG THE RA', 'BRG2302459', 'PCS', 8.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302460', 'PCS', 4.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302467', 'KG', 0.10, 10.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302472', 'KG', 4.50, 28.30),
            array('2026-05-01', 'GDG THE RA', 'BRG2302476', 'KG', 3.40, 210.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302477', 'KG', 3.00, 140.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302480', 'KG', 5.50, 12.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302484', 'KG', 1.00, 23.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302485', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302486', 'KG', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302493', 'KG', 0.60, 10.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302495', 'KG', 1.50, 9.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302496', 'CAN', 0.50, 186.60),
            array('2026-05-01', 'GDG THE RA', 'BRG2302497', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302508', 'CAN', 2.00, 105.41),
            array('2026-05-01', 'GDG THE RA', 'BRG2302515', 'KG', 0.60, 158.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302516', 'KG', 0.50, 130.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302543', 'KG', 2.50, 17.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302545', 'KG', 0.50, 30.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302546', 'PCS', 15.00, 1600.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302570', 'BTL', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2308004', 'PACK', 2.00, 35000.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2310006', 'BTL', 1.80, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2310010', 'KG', 1.30, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2406001', 'PCS', 3.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2502001', 'KG', 1.25, 180.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2505002', 'PAX', 3.00, 17500.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2506002', 'BTL', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2510005', 'BTL', 0.40, 89150.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2603010', 'PACK', 3.00, 0.00),
        );

        $err = 0;
        $ket = null;
        foreach ($arr as $key => $value) {
            // cetak_r( $value );
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
                    select _is.* from item_satuan _is
                    where
                        _is.item_kode = '".$value['2']."' and
                        _is.satuan like '".$value['3']."'
                ";
                $d_is = $m_conf->hydrateRaw( $sql );

                if ( $d_is->count() > 0 ) {
                } else {
                    $err = 1;
                    if ( empty($ket) ) {
                        $ket = 'SATUAN '.$value['3'].' PADA KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                    } else {
                        $ket .= '<br>SATUAN '.$value['3'].' PADA KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                    }
                }
            } else {
                $err = 1;
                if ( empty($ket) ) {
                    $ket = 'KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                } else {
                    $ket .= '<br>KODE BRG '.$value['2'].' TIDAK DITEMUKAN';
                }
            }
        }

        if ( $err == 1 ) {
            cetak_r( $ket );
        } else {
            cetak_r( 'DATA LENGKAP' );
        }
    }

    public function injekStokOpname() {
        $arr = array(
            array('2026-05-01', 'GDG.GTR', 'BRG2302018', 'KG', 6, 30.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302061', 'KG', 0, 15.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302120', 'PACK', 3, 37.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302122', 'KG', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302124', 'KG', 6, 300.57), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302127', 'KG', 11, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302188', 'LTR', 12, 17.37), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302195', 'KG', 3, 22.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302196', 'KG', 12, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302218', 'KG', 2, 27.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302220', 'KG', 2, 13.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302230', 'KG', 3, 18.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302231', 'KG', 20, 18.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302234', 'KG', 3, 32.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302237', 'KG', 3, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302310', 'CAN', 4, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302352', 'PCS', 0, 6500.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302425', 'KG', 15, 15.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302426', 'KG', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302442', 'PACK', 0, 48.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302454', 'CAN', 11, 29.38), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302455', 'CAN', 13, 29.38), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302458', 'BTL', 3, 57.61), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302469', 'PCS', 47, 540.87), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302497', 'KG', 1.5, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302560', 'BTL', 75, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302561', 'CAN', 89, 20345.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302563', 'CAN', 82, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302565', 'PCS', 60, 14000.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302568', 'BTL', 35, 41750.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302569', 'BTL', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302570', 'BTL', 1, 409.74), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302571', 'BTL', 88, 4832.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302572', 'BTL', 88, 6792.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302573', 'BTL', 146, 5250.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302575', 'BTL', 20, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2302576', 'CAN', 29, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2307004', 'KG', 4, 28.50), 
            array('2026-05-01', 'GDG.GTR', 'BRG2309001', 'PCS', 500, 285.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2311003', 'BTL', 15, 23625.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2402001', 'BARREL', 1, 1733000.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2409004', 'KG', 7, 270.56), 
            array('2026-05-01', 'GDG.GTR', 'BRG2510007', 'KG', 0.5, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512003', 'CAN', 0, 5500.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512004', 'CAN', 33, 7069.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512005', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512006', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2512007', 'PCS', 0, 0.00), 
            array('2026-05-01', 'GDG.GTR', 'BRG2601003', 'BTL', 358, 0.00),

            array('2026-05-01', 'GDG.THE RA', 'BRG2302018', 'KG', 1.00, 30.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302061', 'KG', 3.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302120', 'PACK', 1.00, 37.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302122', 'KG', 0.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302124', 'KG', 1.00, 300.57),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302127', 'KG', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302188', 'LTR', 5.00, 17.37),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302195', 'KG', 0.00, 22.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302196', 'KG', 7.00, 15.75),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302218', 'KG', 1.00, 27.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302220', 'KG', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302230', 'KG', 2.00, 18.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302231', 'KG', 10.00, 18.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302234', 'KG', 0.00, 32.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302237', 'KG', 0.00, 42.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302310', 'CAN', 2.00, 26000.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302352', 'PCS', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302425', 'KG', 10.00, 15.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302426', 'KG', 0.00, 12.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302442', 'PACK', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302454', 'CAN', 4.00, 29.38),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302455', 'CAN', 4.00, 29.38),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302458', 'BTL', 1.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302469', 'PCS', 36.00, 540.87),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302560', 'BTL', 39.00, 30875.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302561', 'CAN', 24.00, 20345.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302563', 'CAN', 34.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302565', 'PCS', 0.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302568', 'BTL', 0.00, 41750.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302569', 'BTL', 34.00, 6249.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302570', 'BTL', 1.00, 159.55),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302571', 'BTL', 19.00, 4832.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302572', 'BTL', 25.00, 6792.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302573', 'BTL', 29.00, 5250.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302575', 'BTL', 17.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2302576', 'CAN', 32.00, 4737.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2307004', 'KG', 4.00, 28.50),
            array('2026-05-01', 'GDG.THE RA', 'BRG2309001', 'PCS', 125.00, 285.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2311003', 'BTL', 17.00, 23625.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2402001', 'BARREL', 0.00, 1733000.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2409004', 'KG', 0.00, 270.56),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512003', 'CAN', 22.00, 5500.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512004', 'CAN', 0.00, 7069.00),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512005', 'PCS', 50.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512006', 'PCS', 71.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2512007', 'PCS', 111.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2601003', 'BTL', 90.00, 0),
            array('2026-05-01', 'GDG.THE RA', 'BRG2603001', 'GRAM', 2.00, 0),

            array('2026-05-01', 'GDG-GTR', 'BRG2302002', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302004', 'KG', 10.00, 74.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302005', 'PACK', 0.75, 52000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302020', 'BTL', 2.50, 12.58),
            array('2026-05-01', 'GDG-GTR', 'BRG2302032', 'KG', 1.50, 39.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302034', 'CAN', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302036', 'BTL', 1.50, 875.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302038', 'KG', 3.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302039', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302040', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302041', 'KG', 1.50, 49.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302042', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302043', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302044', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302045', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302048', 'EKOR', 8.00, 75000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302050', 'CAN', 1.00, 122.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302053', 'KG', 1.50, 97.01),
            array('2026-05-01', 'GDG-GTR', 'BRG2302054', 'KG', 0.70, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302062', 'KG', 0.50, 24.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302066', 'KG', 18.00, 15.44),
            array('2026-05-01', 'GDG-GTR', 'BRG2302074', 'PACK', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302075', 'PACK', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302082', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302084', 'KG', 0.80, 52.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302086', 'KG', 2.50, 34.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302088', 'PACK', 8.00, 101.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302089', 'PACK', 6.00, 76.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302093', 'PCS', 12.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302096', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302099', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302101', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302102', 'PCS', 10.00, 8000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302107', 'PACK', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302110', 'PACK', 0.50, 74.20),
            array('2026-05-01', 'GDG-GTR', 'BRG2302120', 'PACK', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302131', 'LTR', 1.80, 60.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302134', 'BTL', 1.30, 25.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302136', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302139', 'KG', 10.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302140', 'KG', 6.00, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302143', 'KG', 22.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302144', 'EKOR', 2.00, 75000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302152', 'KG', 2.50, 85.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302161', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302172', 'IKAT', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302178', 'PAIL', 1.20, 158.90),
            array('2026-05-01', 'GDG-GTR', 'BRG2302179', 'KG', 1.30, 170.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302180', 'KG', 1.50, 85.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302185', 'PACK', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302186', 'KG', 2.50, 45.40),
            array('2026-05-01', 'GDG-GTR', 'BRG2302187', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302188', 'LTR', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302191', 'PACK', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302195', 'KG', 2.30, 22.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302196', 'KG', 11.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302197', 'KG', 3.50, 47.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302211', 'KG', 3.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302218', 'KG', 0.90, 27.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302221', 'CAN', 9.00, 32.94),
            array('2026-05-01', 'GDG-GTR', 'BRG2302224', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302226', 'KG', 0.90, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302228', 'CAN', 6.00, 37.65),
            array('2026-05-01', 'GDG-GTR', 'BRG2302230', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302232', 'KG', 0.50, 130.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302233', 'BTL', 0.25, 275.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302236', 'KG', 1.30, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302237', 'KG', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302240', 'KG', 6.50, 75.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302247', 'PACK', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302249', 'GLN', 1.50, 13.54),
            array('2026-05-01', 'GDG-GTR', 'BRG2302252', 'GLN', 1.80, 26.48),
            array('2026-05-01', 'GDG-GTR', 'BRG2302256', 'PACK', 2.00, 162.67),
            array('2026-05-01', 'GDG-GTR', 'BRG2302257', 'PACK', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302263', 'KG', 0.80, 40.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302267', 'KG', 5.00, 15.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302271', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302281', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302282', 'KG', 6.00, 82.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302284', 'BTL', 1.20, 78.13),
            array('2026-05-01', 'GDG-GTR', 'BRG2302286', 'KG', 1.00, 98.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302288', 'KG', 1.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302291', 'PACK', 4.00, 83.68),
            array('2026-05-01', 'GDG-GTR', 'BRG2302293', 'PACK', 3.00, 55.10),
            array('2026-05-01', 'GDG-GTR', 'BRG2302295', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302299', 'KG', 4.00, 34.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302300', 'PACK', 1.50, 57.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302301', 'PACK', 3.00, 32.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302303', 'KG', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302305', 'KG', 4.00, 17.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302309', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302317', 'KG', 1.00, 10.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302319', 'PCS', 6.00, 1800.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302324', 'BTL', 0.80, 100.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302326', 'KG', 1.30, 16.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302329', 'PACK', 1.80, 31.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302332', 'BTL', 0.50, 97.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302335', 'PACK', 4.00, 223.08),
            array('2026-05-01', 'GDG-GTR', 'BRG2302339', 'PACK', 38.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302341', 'LTR', 26.00, 21.86),
            array('2026-05-01', 'GDG-GTR', 'BRG2302343', 'BTL', 3.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302344', 'BTL', 1.20, 61.11),
            array('2026-05-01', 'GDG-GTR', 'BRG2302346', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302350', 'BTL', 3.00, 182.35),
            array('2026-05-01', 'GDG-GTR', 'BRG2302351', 'PACK', 3.00, 35.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302352', 'PCS', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302360', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302362', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302364', 'KG', 1.50, 58.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302378', 'SISIR', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302386', 'PACK', 1.00, 71.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302388', 'BTL', 3.50, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302389', 'BTL', 0.50, 669.21),
            array('2026-05-01', 'GDG-GTR', 'BRG2302390', 'LOAF', 5.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302393', 'GLN', 0.70, 51.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302395', 'KG', 2.50, 165.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302400', 'BTL', 6.00, 52.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302402', 'BTL', 0.70, 26.18),
            array('2026-05-01', 'GDG-GTR', 'BRG2302403', 'GLN', 1.60, 23.89),
            array('2026-05-01', 'GDG-GTR', 'BRG2302404', 'PACK', 4.00, 36.93),
            array('2026-05-01', 'GDG-GTR', 'BRG2302406', 'CAN', 0.80, 50.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302407', 'GLN', 1.80, 18.44),
            array('2026-05-01', 'GDG-GTR', 'BRG2302408', 'PACK', 3.00, 95.45),
            array('2026-05-01', 'GDG-GTR', 'BRG2302410', 'PACK', 3.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302412', 'BTL', 1.00, 220.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302414', 'KG', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302415', 'KG', 1.20, 14.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302416', 'KG', 3.00, 8.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302417', 'KG', 3.50, 14.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302423', 'KG', 1.20, 45.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302424', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302425', 'KG', 4.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302426', 'KG', 0.80, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302428', 'KG', 1.20, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302429', 'PCS', 450.00, 2800.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302436', 'PACK', 0.50, 97.61),
            array('2026-05-01', 'GDG-GTR', 'BRG2302437', 'KG', 0.50, 29.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302438', 'PACK', 4.00, 123.19),
            array('2026-05-01', 'GDG-GTR', 'BRG2302441', 'PACK', 2.00, 33.80),
            array('2026-05-01', 'GDG-GTR', 'BRG2302444', 'PCS', 12.00, 1975.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302454', 'CAN', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302455', 'CAN', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302456', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302459', 'PCS', 22.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302460', 'PCS', 26.00, 3500.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302467', 'KG', 1.60, 10.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302468', 'KG', 0.50, 25.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302470', 'PCS', 12.00, 4500.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302472', 'KG', 18.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302475', 'PAX', 7.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302477', 'KG', 12.00, 140.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302479', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302480', 'KG', 8.00, 12.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302484', 'KG', 3.50, 23.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302486', 'KG', 6.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302488', 'PACK', 4.00, 54.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302495', 'KG', 4.00, 9.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302496', 'CAN', 0.50, 186.60),
            array('2026-05-01', 'GDG-GTR', 'BRG2302497', 'KG', 4.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302498', 'KG', 0.25, 12.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302508', 'CAN', 2.50, 105.41),
            array('2026-05-01', 'GDG-GTR', 'BRG2302516', 'KG', 4.50, 130.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302522', 'KG', 1.50, 175.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302542', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302543', 'KG', 5.00, 17.50),
            array('2026-05-01', 'GDG-GTR', 'BRG2302546', 'PCS', 750.00, 1600.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302547', 'PACK', 1.00, 74.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302548', 'KG', 2.50, 32.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302550', 'KG', 0.50, 20.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302553', 'KG', 0.25, 112.61),
            array('2026-05-01', 'GDG-GTR', 'BRG2302557', 'KG', 0.15, 65.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2302570', 'BTL', 1.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2307002', 'KG', 1.00, 40.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2308004', 'PACK', 4.00, 35000.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2403002', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2406001', 'PCS', 12.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2408002', 'GLN', 1.00, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2408010', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2409001', 'BTL', 1.60, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2412001', 'PAX', 1.00, 46950.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2502005', 'KG', 1.80, 62.70),
            array('2026-05-01', 'GDG-GTR', 'BRG2502007', 'KG', 1.20, 0.00),
            array('2026-05-01', 'GDG-GTR', 'BRG2505001', 'BTL', 0.10, 26000.00),

            array('2026-05-01', 'GDG THE RA', 'BRG2302002', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302014', 'CAN', 2.00, 248.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302020', 'BTL', 0.25, 62.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302034', 'CAN', 0.20, 130.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302036', 'BTL', 1.00, 875.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302037', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302038', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302039', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302040', 'PACK', 1.50, 104.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302042', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302043', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302045', 'KG', 1.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302047', 'BTL', 1.75, 1875.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302053', 'KG', 0.70, 97.01),
            array('2026-05-01', 'GDG THE RA', 'BRG2302054', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302061', 'KG', 0.50, 15.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302066', 'KG', 12.00, 15.44),
            array('2026-05-01', 'GDG THE RA', 'BRG2302074', 'PACK', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302082', 'KG', 1.60, 31000.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302084', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302086', 'KG', 1.00, 34.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302088', 'PACK', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302096', 'KG', 0.15, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302097', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302099', 'KG', 0.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302101', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302127', 'KG', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302131', 'LTR', 1.25, 60.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302134', 'BTL', 0.50, 25.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302136', 'KG', 1.50, 75.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302139', 'KG', 6.00, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302140', 'KG', 3.00, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302143', 'KG', 9.00, 38.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302178', 'PAIL', 0.80, 158.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302180', 'KG', 0.40, 85.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302185', 'PACK', 2.50, 36.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302186', 'KG', 1.80, 45.40),
            array('2026-05-01', 'GDG THE RA', 'BRG2302187', 'PACK', 1.20, 80.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302188', 'LTR', 1.50, 17.37),
            array('2026-05-01', 'GDG THE RA', 'BRG2302191', 'PACK', 2.00, 9.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302193', 'KG', 0.20, 52.47),
            array('2026-05-01', 'GDG THE RA', 'BRG2302196', 'KG', 4.50, 15.75),
            array('2026-05-01', 'GDG THE RA', 'BRG2302221', 'CAN', 3.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302224', 'KG', 0.15, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302226', 'KG', 0.10, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302230', 'KG', 0.70, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302237', 'KG', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302240', 'KG', 2.50, 75.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302249', 'GLN', 0.25, 13.54),
            array('2026-05-01', 'GDG THE RA', 'BRG2302252', 'GLN', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302256', 'PACK', 1.50, 162.67),
            array('2026-05-01', 'GDG THE RA', 'BRG2302257', 'PACK', 3.00, 105.63),
            array('2026-05-01', 'GDG THE RA', 'BRG2302267', 'KG', 2.50, 15.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302288', 'KG', 0.60, 95.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302291', 'PACK', 3.00, 83.68),
            array('2026-05-01', 'GDG THE RA', 'BRG2302293', 'PACK', 1.50, 55.10),
            array('2026-05-01', 'GDG THE RA', 'BRG2302295', 'KG', 2.50, 8.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302299', 'KG', 1.50, 34.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302300', 'PACK', 1.00, 57.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302324', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302329', 'PACK', 0.80, 31.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302335', 'PACK', 3.00, 223.08),
            array('2026-05-01', 'GDG THE RA', 'BRG2302339', 'PACK', 8.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302341', 'LTR', 14.00, 21.86),
            array('2026-05-01', 'GDG THE RA', 'BRG2302342', 'BTL', 0.70, 45.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302343', 'BTL', 0.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302344', 'BTL', 0.60, 61.11),
            array('2026-05-01', 'GDG THE RA', 'BRG2302346', 'KG', 1.80, 31.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302350', 'BTL', 1.50, 182.35),
            array('2026-05-01', 'GDG THE RA', 'BRG2302352', 'PCS', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302358', 'BTL', 2.00, 680.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302362', 'KG', 1.50, 48.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302364', 'KG', 1.20, 58.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302366', 'KG', 0.70, 320.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302378', 'SISIR', 0.75, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302386', 'PACK', 1.00, 71.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302388', 'BTL', 0.60, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302389', 'BTL', 1.00, 669.21),
            array('2026-05-01', 'GDG THE RA', 'BRG2302390', 'LOAF', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302392', 'PACK', 0.25, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302393', 'GLN', 0.50, 51.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302395', 'KG', 0.50, 165.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302400', 'BTL', 1.00, 52.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302402', 'BTL', 1.00, 26.18),
            array('2026-05-01', 'GDG THE RA', 'BRG2302403', 'GLN', 0.70, 23.89),
            array('2026-05-01', 'GDG THE RA', 'BRG2302404', 'PACK', 1.80, 36.93),
            array('2026-05-01', 'GDG THE RA', 'BRG2302406', 'CAN', 0.20, 50.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302407', 'GLN', 0.80, 18.44),
            array('2026-05-01', 'GDG THE RA', 'BRG2302408', 'PACK', 1.50, 95.45),
            array('2026-05-01', 'GDG THE RA', 'BRG2302410', 'PACK', 1.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302412', 'BTL', 0.25, 220.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302414', 'KG', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302415', 'KG', 0.70, 14.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302416', 'KG', 1.00, 8.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302417', 'KG', 1.25, 14.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302418', 'KG', 2.00, 35.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302423', 'KG', 1.00, 45.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302424', 'KG', 0.10, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302427', 'KG', 1.80, 7.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302429', 'PCS', 15.00, 2800.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302433', 'KG', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302436', 'PACK', 0.15, 96.90),
            array('2026-05-01', 'GDG THE RA', 'BRG2302441', 'PACK', 3.00, 33.80),
            array('2026-05-01', 'GDG THE RA', 'BRG2302453', 'KG', 0.40, 58.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302454', 'CAN', 1.50, 29.38),
            array('2026-05-01', 'GDG THE RA', 'BRG2302459', 'PCS', 8.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302460', 'PCS', 4.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302467', 'KG', 0.10, 10.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302472', 'KG', 4.50, 28.30),
            array('2026-05-01', 'GDG THE RA', 'BRG2302476', 'KG', 3.40, 210.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302477', 'KG', 3.00, 140.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302480', 'KG', 5.50, 12.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302484', 'KG', 1.00, 23.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302485', 'KG', 2.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302486', 'KG', 1.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302493', 'KG', 0.60, 10.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302495', 'KG', 1.50, 9.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302496', 'CAN', 0.50, 186.60),
            array('2026-05-01', 'GDG THE RA', 'BRG2302497', 'KG', 2.50, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302508', 'CAN', 2.00, 105.41),
            array('2026-05-01', 'GDG THE RA', 'BRG2302515', 'KG', 0.60, 158.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302516', 'KG', 0.50, 130.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302543', 'KG', 2.50, 17.50),
            array('2026-05-01', 'GDG THE RA', 'BRG2302545', 'KG', 0.50, 30.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302546', 'PCS', 15.00, 1600.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2302570', 'BTL', 0.60, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2308004', 'PACK', 2.00, 35000.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2310006', 'BTL', 1.80, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2310010', 'KG', 1.30, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2406001', 'PCS', 3.00, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2502001', 'KG', 1.25, 180.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2505002', 'PAX', 3.00, 17500.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2506002', 'BTL', 0.20, 0.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2510005', 'BTL', 0.40, 89150.00),
            array('2026-05-01', 'GDG THE RA', 'BRG2603010', 'PACK', 3.00, 0.00),
        );

        $err = 0;
        $ket = null;
        foreach ($arr as $key => $value) {
            $m_so = new \Model\Storage\StokOpname_model();
            $sql = "
                select * from stok_opname so
                where
                    so.tanggal = '".$value['0']."' and
                    so.gudang_kode = '".$value['1']."'
            ";
            $d_so = $m_so->hydrateRaw( $sql );

            $id_so = null;
            if ( $d_so->count() > 0 ) {
                $id_so = $d_so->toArray()[0]['id'];
            } else {
                $kode_stok_opname = $m_so->getNextIdRibuan();
    
                $m_so->tanggal = $value['0'];
                $m_so->gudang_kode = $value['1'];
                $m_so->kode_stok_opname = $kode_stok_opname;
                $m_so->save();

                $id_so = $m_so->id;
            }

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select _is.* from item_satuan _is
                where
                    _is.item_kode = '".$value['2']."' and
                    _is.satuan like '".$value['3']."'
            ";
            $d_is = $m_conf->hydrateRaw( $sql );

            $pengali = null;
            if ( $d_is->count() > 0 ) {
                $pengali = $d_is->toArray()[0]['pengali'];
            }

            $m_sod = new \Model\Storage\StokOpnameDet_model();
            $m_sod->id_header = $id_so;
            $m_sod->item_kode = $value['2'];
            $m_sod->satuan = $value['3'];
            $m_sod->pengali = $pengali;
            $m_sod->jumlah = $value['4'];
            $m_sod->harga = ($value['5'] * $pengali);
            $m_sod->save();
        }
    }
}
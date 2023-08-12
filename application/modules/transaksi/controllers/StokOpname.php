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
                $m_sod->harga_old = (isset($v_li['harga_old']) && isset($v_li['harga_old']) > 0) ? $v_li['harga_old'] : 0;
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
                $m_sod->harga_old = (isset($v_li['harga_old']) && isset($v_li['harga_old']) > 0) ? $v_li['harga_old'] : 0;
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
}
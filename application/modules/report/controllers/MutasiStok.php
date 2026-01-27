<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MutasiStok extends Public_Controller {

    private $pathView = 'report/mutasi_stok/';
    private $url;
    private $hakAkses;
    private $_mappingDataReport;

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
    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    'assets/report/mutasi_stok/js/mutasi-stok.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/mutasi_stok/css/mutasi-stok.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['gudang'] = $this->getGudang();
            $content['item'] = $this->getItem();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Mutasi Stok';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGudang()
    {
        $m_gdg = new \Model\Storage\Gudang_model();
        $d_gdg = $m_gdg->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gdg->count() > 0 ) {
            $data = $d_gdg->toArray();
        }

        return $data;
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['group'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $tgl_stok_opname = $this->config->item('tgl_stok_opname');

            $start_date = ($params['start_date'] >= $tgl_stok_opname) ? $params['start_date'] : $tgl_stok_opname;
            $end_date = $params['end_date'];
            $gudang = $params['gudang'];
            $item = $params['item'];

            $m_stokt = new \Model\Storage\StokTanggal_model();
            $d_stokt = $m_stokt->whereBetween('tanggal', [$start_date, $end_date])->where('gudang_kode', $gudang)->with(['gudang'])->orderBy('tanggal', 'asc')->get();

            $data = null;
            if ( $d_stokt->count() > 0 ) {
                $data = $d_stokt->toArray();
            }

            $this->_mappingDataReport = $this->mappingDataReport( null, $item, $gudang, $start_date, $end_date );

            $content_report['data'] = $this->_mappingDataReport;
            $html_report = $this->load->view($this->pathView . 'list', $content_report, TRUE);

            $list_html = array(
                'list_report' => $html_report
            );

            $this->result['status'] = 1;
            $this->result['content'] = $list_html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function mappingDataReport($_data, $_item, $_gudang, $start_date, $end_date)
    {
        $sql_item = null;
        if ( !in_array('all', $_item) ) {
            $sql_item = "and s.item_kode in ('".implode("', '", $_item)."')";
        }

        $sql_item_sh = null;
        if ( !in_array('all', $_item) ) {
            $sql_item_sh = "where item_kode in ('".implode("', '", $_item)."')";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                data.*,
                g.nama as nama_gudang,
                i.nama as nama_barang,
                isatuan.satuan,
                sh.harga
            from
            (
                /* Saldo Awal */
                select
                    d_masuk.*
                    ,sh.harga
                    ,isnull(strans.jumlah, 0) as jml_pakai
                    ,d_masuk.sisa_stok + isnull(strans.jumlah, 0) as debet
                    ,0 as kredit
                    ,'0' as jenis
                from
                (
                    select
                        stid.id,
                        d_masuk.gudang_kode,
                        d_masuk.tanggal as tanggal,
                        d_masuk.kode_trans as kode_trans,
                        d_masuk.item_kode,
                        d_masuk.sisa_stok
                    from
                    (
                        select
                            d_masuk.tgl_st,
                            d_masuk.gudang_kode,
                            d_masuk.tanggal,
                            d_masuk.kode_trans,
                            d_masuk.item_kode,
                            sum(d_masuk.sisa_stok) as sisa_stok
                        from
                        (
                            select
                                min(st.tanggal) as tgl_st,
                                st.gudang_kode,
                                '".$start_date."' as tanggal,
                                'Saldo Awal' as kode_trans,
                                s.item_kode,
                                min(s.sisa_stok) as sisa_stok
                            from stok_tanggal st
                            left join
                                stok s
                                on
                                    st.id = s.id_header
                            where
                                st.tanggal between '".$start_date."' and '".$end_date."' and
                                s.tanggal < '".$start_date."' and
                                st.gudang_kode = '".$_gudang."'
                                ".$sql_item."
                            group by
                                st.gudang_kode,
                                s.tanggal,
                                s.item_kode
                        ) d_masuk
                        group by
                            d_masuk.tgl_st,
                            d_masuk.gudang_kode,
                            d_masuk.tanggal,
                            d_masuk.kode_trans,
                            d_masuk.item_kode
                    ) d_masuk
                    left join
                        stok_tanggal stid
                        on
                            d_masuk.tgl_st = stid.tanggal and
                            d_masuk.gudang_kode = stid.gudang_kode
                ) d_masuk
                left join
                    (
                        select 
                            s.kode_trans, s.item_kode, s.gudang_kode, sum(strans.jumlah) as jumlah
                            -- strans.*
                        from stok_tanggal st
                        left join
                            stok s
                            on
                                st.id = s.id_header
                        left join
                            stok_trans strans
                            on
                                s.id = strans.id_header
                        where
                            st.tanggal between '".$start_date."' and '".$end_date."' and
                            st.gudang_kode = '".$_gudang."'
                            ".$sql_item."
                        group by
                            s.kode_trans, s.item_kode, s.gudang_kode
                    ) strans
                    on
                        d_masuk.kode_trans = strans.kode_trans and
                        d_masuk.item_kode = strans.item_kode and
                        d_masuk.gudang_kode = strans.gudang_kode
                left join
                    (
                        select
                        *
                        from stok_harga
                        ".$sql_item_sh."
                    ) sh
                    on
                        sh.id_header = d_masuk.id and
                        sh.item_kode = d_masuk.item_kode

                /* End - Saldo Awal */

                union all

                /* Data Masuk */
                select
                    d_masuk.*
                    ,isnull(strans.jumlah, 0) as jml_pakai
                    ,d_masuk.sisa_stok + isnull(strans.jumlah, 0) as debet
                    ,0 as kredit
                    ,'1' as jenis
                from
                (
                    select
                        stid.id,
                        d_masuk.gudang_kode,
                        d_masuk.tanggal,
                        d_masuk.kode_trans,
                        d_masuk.item_kode,
                        d_masuk.sisa_stok,
                        d_masuk.harga
                    from
                    (
                        select
                            min(st.tanggal) as tgl_st,
                            st.gudang_kode,
                            s.tanggal,
                            s.kode_trans,
                            s.item_kode,
                            min(s.sisa_stok) as sisa_stok,
                            s.harga_beli as harga
                            -- ,isnull(sum(strans.jumlah), 0) as jml_pakai
                        from stok_tanggal st
                        left join
                            stok s
                            on
                                st.id = s.id_header
                        where                        
                            s.tanggal between '".$start_date."' and '".$end_date."' and
                            st.tanggal between '".$start_date."' and '".$end_date."' and
                            st.gudang_kode = '".$_gudang."'
                            ".$sql_item."
                        group by
                            st.gudang_kode,
                            s.tanggal,
                            s.kode_trans,
                            s.item_kode,
                            s.harga_beli
                    ) d_masuk
                    left join
                        stok_tanggal stid
                        on
                            d_masuk.tgl_st = stid.tanggal and
                            d_masuk.gudang_kode = stid.gudang_kode
                ) d_masuk
                left join
                    (
                        select 
                            s.kode_trans, s.item_kode, s.gudang_kode, sum(strans.jumlah) as jumlah
                            -- strans.*
                        from stok_tanggal st
                        left join
                            stok s
                            on
                                st.id = s.id_header
                        left join
                            stok_trans strans
                            on
                                s.id = strans.id_header
                        where
                            st.tanggal between '".$start_date."' and '".$end_date."' and
                            st.gudang_kode = '".$_gudang."'
                            ".$sql_item."
                        group by
                            s.kode_trans, s.item_kode, s.gudang_kode
                    ) strans
                    on
                        d_masuk.kode_trans = strans.kode_trans and
                        d_masuk.item_kode = strans.item_kode and
                        d_masuk.gudang_kode = strans.gudang_kode
                /* END - Data Masuk */

                union all

                /* Data Keluar */
                select 
                    st.id,
                    s.gudang_kode, 
                    st.tanggal, 
                    strans.kode_trans, 
                    s.item_kode,
                    0 as sisa_stok,
                    sh.harga,
                    0 as jml_pakai,
                    0 as debet,
                    strans.jumlah as kredit,
                    '2' as jenis
                    -- strans.*
                from stok_tanggal st
                left join
                    stok s
                    on
                        st.id = s.id_header
                left join
                    stok_trans strans
                    on
                        s.id = strans.id_header
                left join
                    (
                        select
                        *
                        from stok_harga
                        ".$sql_item_sh."
                    ) sh
                    on
                        sh.id_header = st.id and
                        sh.item_kode = s.item_kode
                where
                    st.tanggal between '".$start_date."' and '".$end_date."' and
                    st.gudang_kode = '".$_gudang."'
                    and strans.jumlah > 0
                    ".$sql_item."
                /* END - Data Keluar */
            ) data
            left join
                (
                    select
                    *
                    from stok_harga
                    ".$sql_item_sh."
                ) sh
                on
                    sh.id_header = data.id and
                    sh.item_kode = data.item_kode
            left join
                gudang g
                on
                    g.kode_gudang = data.gudang_kode
            left join
                item i
                on
                    i.kode = data.item_kode
            left join
                (
                    select is1.* from item_satuan is1
                    right join
                        (
                            select max(id) as id, item_kode from item_satuan where pengali = 1 group by item_kode
                        ) is2
                        on
                            is1.id = is2.id

                ) isatuan
                on
                    i.kode = isatuan.item_kode
        ";
        // cetak_r( $sql, 1 );
        $d_conf = $m_conf->hydrateRaw( $sql );
        if ( $d_conf->count() > 0 ) {
            $d_conf = $d_conf->toArray();

            foreach ($d_conf as $key => $value) {
                if ( !isset($data[ $value['gudang_kode'] ]) ) {
                    $data[ $value['gudang_kode'] ]['kode'] = $value['gudang_kode'];
                    $data[ $value['gudang_kode'] ]['nama'] = $value['nama_gudang'];
                }

                $key_item = $value['nama_barang'].' | '.$value['item_kode'];
                if ( !isset($data[ $value['gudang_kode'] ]['detail'][ $key_item ]) ) {
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['kode'] = $value['item_kode'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['nama'] = $value['nama_barang'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['satuan'] = $value['satuan'];
                }

                if ( $value['jenis'] == '1' ) {
                    $key_masuk = str_replace('-', '', substr($value['tanggal'], 0, 10)).'-'.$value['kode_trans']; 
                    if ( !isset($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]) ) {
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['kode'] = $value['kode_trans'];
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['tgl_trans'] = substr($value['tanggal'], 0, 10);
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['masuk'] = $value['debet'];
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['keluar'] = $value['kredit'];
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['harga'] = $value['harga'];
                        $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['nilai'] = ($value['debet'] * $value['harga']);
                    }
                }

                if ( $value['jenis'] == '2' ) {
                    $key_keluar = str_replace('-', '', substr($value['tanggal'], 0, 10)).'-'.$value['kode_trans']; 
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['kode'] = $value['kode_trans'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['tgl_trans'] = substr($value['tanggal'], 0, 10);
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['masuk'] = $value['debet'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['keluar'] = $value['kredit'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['harga'] = $value['harga'];
                    $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar'][ $key_keluar ]['nilai'] = ($value['kredit'] * $value['harga']);
                }

                if ( isset($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk']) && !empty($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk']) ) {
                    ksort( $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['masuk']);
                }

                if ( isset($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar']) && !empty($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar']) ) {
                    ksort( $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($value['tanggal'], 0, 10) ]['keluar']);
                }

                if ( isset($data[ $value['gudang_kode'] ]['detail']) ) {
                    ksort( $data[ $value['gudang_kode'] ]['detail'] );
                }
                
                if ( isset($data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail']) ) {
                    ksort( $data[ $value['gudang_kode'] ]['detail'][ $key_item ]['detail'] );
                }
            }
        }

        /*
        cetak_r($d_conf, 1);

        $kode_item = array();
        if ( !empty( $_item ) ) {
            foreach ($_item as $k_item => $v_item) {
                if ( stristr($v_item, 'all') !== FALSE ) {
                    $m_item = new \Model\Storage\Item_model();
                    $d_item = $m_item->orderBy('nama', 'asc')->get()->toArray();

                    foreach ($d_item as $k_item => $v_item) {
                        $kode_item[] = trim($v_item['kode']);
                    }

                    break;
                } else {
                    $m_item = new \Model\Storage\Item_model();
                    $d_item = $m_item->where('kode', $v_item)->orderBy('nama', 'asc')->get()->toArray();

                    foreach ($d_item as $k_item => $v_item) {
                        $kode_item[] = trim($v_item['kode']);
                    }
                }
            }
        }

        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $data[ $v_data['gudang_kode'] ]['kode'] = $v_data['gudang_kode'];
                $data[ $v_data['gudang_kode'] ]['nama'] = $v_data['gudang']['nama'];

                $_kode_item = "'".implode("', '", $kode_item)."'";

                $id_stok_tanggal = $v_data['id'];
                $kode_gudang = $v_data['gudang_kode'];

                $conf = new \Model\Storage\Conf();
                $sql = "
                    select 
                        s.id_header,
                        s.item_kode,
                        s.tgl_trans,
                        s.tanggal,
                        s.kode_trans,
                        s.harga_beli,
                        sum(s.sisa_stok) as sisa_stok,
                        i.nama as nama,
                        isatuan.satuan
                    from stok s
                    right join
                        item i
                        on
                            s.item_kode = i.kode
                    right join
                        (
                            select is1.* from item_satuan is1
                            right join
                                (
                                    select max(id) as id, item_kode from item_satuan where pengali = 1 group by item_kode
                                ) is2
                                on
                                    is1.id = is2.id

                        ) isatuan
                        on
                            i.kode = isatuan.item_kode
                    where
                        s.id_header = $id_stok_tanggal and
                        s.gudang_kode = '$kode_gudang' and
                        s.item_kode in ($_kode_item)
                    group by
                        s.id_header,
                        s.item_kode,
                        s.tgl_trans,
                        s.tanggal,
                        s.kode_trans,
                        s.harga_beli,
                        i.nama,
                        isatuan.satuan
                ";

                $d_conf_stok = $conf->hydrateRaw($sql);

                if ( $d_conf_stok->count() > 0 ) {
                    $d_conf_stok = $d_conf_stok->toArray();

                    // cetak_r( $d_conf_stok );

                    foreach ($d_conf_stok as $k_det => $v_det) {
                        $id_header = $v_det['id_header'];
                        $item_kode = $v_det['item_kode'];
                        $tanggal = substr($v_det['tanggal'], 0, 10);
                        $kode_trans = $v_det['kode_trans'];

                        $conf = new \Model\Storage\Conf();
                        $sql = "
                            select * from stok_harga sh
                            where
                                sh.id_header = $id_header and
                                sh.item_kode = '$item_kode'
                        ";
                        $d_harga = $conf->hydrateRaw($sql);

                        $harga_beli = $v_det['harga_beli'];
                        $harga_rata = 0;
                        if ( $d_harga->count() > 0 ) {
                            $d_harga = $d_harga->toArray();

                            $harga_rata = $d_harga[0]['harga'];
                        }

                        $conf = new \Model\Storage\Conf();
                        $sql = "
                            select st.* from stok_trans st
                            left join
                                stok s
                                on
                                    st.id_header = s.id
                            where
                                s.id_header = $id_header and
                                s.item_kode = '$item_kode' and
                                s.tanggal = '$tanggal' and
                                s.kode_trans = '$kode_trans'
                        ";

                        $d_conf_stokt = $conf->hydrateRaw($sql);
                        $data_stokt = null;
                        if ( $d_conf_stokt->count() > 0 ) {
                            $data_stokt = $d_conf_stokt->toArray();
                        }

                        $key_item = $v_det['nama'].' | '.$v_det['item_kode'];

                        $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['kode'] = $v_det['item_kode'];
                        $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['nama'] = $v_det['nama'];
                        $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['satuan'] = $v_det['satuan'];

                        // $key_masuk = str_replace('-', '', substr($v_det['tanggal'], 0, 10)).'-'.$v_det['kode_trans'].'-'.$harga_beli; 
                        $key_masuk = str_replace('-', '', substr($v_det['tanggal'], 0, 10)).'-'.$v_det['kode_trans']; 

                        if ( !isset($data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]) ) {
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['kode'] = $v_det['kode_trans'];
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['tgl_trans'] = substr($v_det['tanggal'], 0, 10);
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['masuk'] = $v_det['sisa_stok'];
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['keluar'] = 0;
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['stok_akhir'] = $v_det['sisa_stok'];
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['harga'] = $harga_rata;
                            $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['nilai'] = ($v_det['sisa_stok'] * $harga_rata);

                            // if ( !empty($data_stokt) ) {
                            //     foreach ($data_stokt as $k => $v) {
                            //         $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['masuk'] += $v['jumlah'];
                            //         $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['stok_akhir'] += $v['jumlah'];
                            //         $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['nilai'] += ($v['jumlah'] * $harga_rata);
                            //     }
                            // }
                        }

                        if ( !empty($data_stokt) ) {
                            foreach ($data_stokt as $k_detd => $v_detd) {                                
                                $tanggal = null;
                                $m_conf = new \Model\Storage\Conf();

                                $tbl_name = $v_detd['tbl_name'];
                                $column_name = null;
                                $kode_trans_nama = null;

                                if ( $tbl_name == 'closing_order' ) { 
                                    if ( is_numeric($v_detd['kode_trans']) ) {
                                        $tbl_name = 'waste_menu';
                                        $column_name = 'id';

                                        $sql = "
                                            select pesanan_kode from waste_menu_item where id_header = ".$v_detd['kode_trans']." group by pesanan_kode
                                        ";

                                        $d_conf = $m_conf->hydrateRaw($sql);
                                        if ( $d_conf->count() > 0 ) {
                                            $d_conf = $d_conf->toArray();

                                            $kode_trans_nama = 'WM ('.$d_conf[0]['pesanan_kode'].')';
                                        } else {
                                            $kode_trans_nama = 'WM';
                                        }
                                    } else {
                                        $tbl_name = 'jual';
                                        $column_name = 'kode_faktur'; 
                                        $kode_trans_nama = $v_detd['kode_trans'];
                                    }
                                } else if ( $tbl_name == 'jual' ) { 
                                    $column_name = 'kode_faktur'; 
                                    $kode_trans_nama = $v_detd['kode_trans'];
                                } else if ( $tbl_name == 'waste_menu' ) {
                                    $column_name = 'id';

                                    $sql = "
                                        select pesanan_kode from waste_menu_item where id_header = ".$v_detd['kode_trans']." group by pesanan_kode
                                    ";

                                    $d_conf = $m_conf->hydrateRaw($sql);
                                    if ( $d_conf->count() > 0 ) {
                                        $d_conf = $d_conf->toArray();

                                        $kode_trans_nama = 'WM ('.$d_conf[0]['pesanan_kode'].')';
                                    } else {
                                        $kode_trans_nama = 'WM';
                                    }
                                } else {
                                    $column_name = 'kode_'.$tbl_name;
                                    $kode_trans_nama = $v_detd['kode_trans'];
                                }

                                $kode_trans = $v_detd['kode_trans'];

                                $sql = "
                                    select * from $tbl_name where $column_name = '$kode_trans'
                                ";

                                $d_conf = $m_conf->hydrateRaw($sql);
                                if ( $d_conf->count() > 0 ) {
                                    $d_trans = $d_conf->toArray();

                                    foreach ($d_trans as $k_trans => $v_trans) {
                                        $column_name = null;

                                        if ( $tbl_name == 'closing_order' ) { 
                                            if ( is_numeric($v_detd['kode_trans']) ) {
                                                $column_name = 'tanggal';
                                            } else {
                                                $column_name = 'tgl_trans'; 
                                            }
                                        } else if ( $tbl_name == 'jual' ) { 
                                            $column_name = 'tgl_trans'; 
                                        } else if ( $tbl_name == 'waste_menu' ) {
                                            $column_name = 'tanggal';
                                        } else {
                                            $column_name = 'tgl_'.$tbl_name;
                                        }

                                        $tanggal = $v_trans[$column_name];
                                    }
                                }

                                $key_keluar = str_replace('-', '', substr($tanggal, 0, 10)).'-'.$v_detd['id'];

                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['kode'] = $kode_trans_nama;
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['tgl_trans'] = substr($tanggal, 0, 10);
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['masuk'] = 0;
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['keluar'] = $v_detd['jumlah'];
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['stok_akhir'] = $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk'][ $key_masuk ]['masuk'] - $v_detd['jumlah'];
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['harga'] = $harga_rata;
                                $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($tanggal, 0, 10) ]['keluar'][ $key_keluar ]['nilai'] = ($v_detd['jumlah'] * $harga_rata);
                            }

                            if ( isset($data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk']) && !empty($data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk']) ) {
                                ksort( $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['masuk']);
                            }

                            if ( isset($data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['keluar']) && !empty($data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['keluar']) ) {
                                ksort( $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'][ substr($v_det['tanggal'], 0, 10) ]['keluar']);
                            }
                        }

                        ksort( $data[ $v_data['gudang_kode'] ]['detail'] );
                        ksort( $data[ $v_data['gudang_kode'] ]['detail'][ $key_item ]['detail'] );
                    }
                }
            }
        }
        */

        return $data;
    }

    public function excryptParamsExportExcel()
    {
        $params = $this->input->post('params');

        try {
            $paramsEncrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = array('data' => $paramsEncrypt);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportExcel($_params)
    {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $tgl_stok_opname = $this->config->item('tgl_stok_opname');

        $start_date = ($_data_params['start_date'] >= $tgl_stok_opname) ? $_data_params['start_date'] : $tgl_stok_opname;
        $end_date = $_data_params['end_date'];
        $gudang = $_data_params['gudang'];
        $item = $_data_params['item'];

        $m_stokt = new \Model\Storage\StokTanggal_model();
        $d_stokt = $m_stokt->whereBetween('tanggal', [$start_date, $end_date])->where('gudang_kode', $gudang)->with(['gudang'])->orderBy('tanggal', 'asc')->get();

        $data = null;
        if ( $d_stokt->count() > 0 ) {
            $data = $d_stokt->toArray();
        }

        $this->_mappingDataReport = $this->mappingDataReport( $data, $item, $gudang );

        $gudang_nama = $this->_mappingDataReport[ $gudang ]['nama'];

        $content['nama_gudang'] = $gudang_nama;
        $content['start_date'] = $start_date;
        $content['end_date'] = $end_date;
        $content['data'] = $this->_mappingDataReport;
        $res_view_html = $this->load->view('report/mutasi_stok/export_excel', $content, true);

        $filename = 'export-mutasi-stok-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }

    public function cekBarang()
    {
        $data_barang_excel = array(
            array('AYAM DADA 1A', 'PCS'),
            array('AYAM SAYAP', 'PCS'),
            array('AYAM PAHA BAWAH', 'PCS'),
            array('AYAM DADA 4A', 'PCS'),
            array('AYAM PAHA ATAS', 'PCS'),
            array('PATTY', 'PCS'),
            array('CRISPY CHICKEN STEAK', 'PCS'),
            array('GRILL CHICKEN STEAK', 'PCS'),
            array('CHICKEN SKIN', 'GRAM'),
            array('TEPUNG SEGITIGA BIRU 4KG', 'PCS'),
            array('TEPUNG MAIZENA GORENG 300G', 'PCS'),
            array('TEPUNG RAHASIA', 'PCS'),
            array('MINYAK GORENG FORTUNE 1LT', 'L'),
            array('BERAS PULEN ONDE 5KG', 'KG'),
            array('GULA LOKAL 1KG', 'PCS'),
            array('TEH SARIWANGI', 'PACK'),
            array('SAOS TOMAT SACHET', 'PACK'),
            array('SAOS SAMBAL SACHET', 'PACK'),
            array('SAMBAL BAWANG LVL 1', 'PCS'),
            array('SAMBAL BAWANG LVL 2', 'PCS'),
            array('SAMBAL BAWANG LVL 3', 'PCS'),
            array('SAMBAL BAWANG LVL 4', 'PCS'),
            array('SAMBAL IJO', 'PCS'),
            array('SAMBAL MATAH ', 'PCS'),
            array('SAUS TOMAT 1KG ', 'KG'),
            array('SAUS SAMBAL 1KG', 'KG'),
            array('SAUS MAYONAISE', 'GRAM'),
            array('SAUS KEJU TOPPING', 'GRAM'),
            array('SAUS KEJU KULIT', 'GRAM'),
            array('SAUS BLACKPAPER', 'PCS'),
            array('KEJU MOZARELLA', 'PCS'),
            array('SUSU ULTRA UHT', 'PCS'),
            array('TEPUNG MAIZENA STEAK 1KG', 'GRAM'),
            array('TISU HANDTOWEL', 'PACK'),
            array('PAPER BAG ALA CARTE', 'PACK'),
            array('KERTAS KENTANG', 'PACK'),
            array('MARGARIN', 'PCS'),
            array('ROTI BURGER', 'PCS'),
            array('FRENCH FRIES', 'GRAM'),
            array('SARUNG TANGAN', 'PACK'),
            array('LUNCH BOX M', 'PCS'),
            array('SEALER', 'PCS'),
            array('KERTAS NASI', 'PACK'),
            array('STIKER', 'PCS'),
            array('CUP TEH', 'PACK'),
            array('LUNCH BOX L', 'PCS'),
            array('TEMPAT SAMBAL DINE IN', 'PACK'),
            array('PLASTIK 1 KG', 'PACK'),
            array('CUP SAMBAL 35', 'PACK'),
            array('CUP SAMBAL 25', 'PACK'),
            array('SEDOTAN', 'PACK'),
            array('KRESEK 24', 'PACK'),
            array('KRESEK 28', 'PACK'),
            array('KRESEK 35', 'PACK'),
            array('TISU TOILET', 'PACK'),
            array('KRESEK SAMPAH', 'PCS'),
            array('STELLA REFIL SPRAY', 'PCS'),
            array('SABUN CUCI TANGAN', 'PCS'),
            array('SABUN TOILET', 'PCS'),
            array('CLING', 'PCS'),
            array('SUNLIGHT', 'PCS'),
            array('VIXAL', 'PCS'),
            array('KAPUR BARUS', 'PCS'),
            array('DETERGEN', 'PCS'),
            array('PEMBERSIH TOILET DUDUK', 'PCS'),
            array('PEMBERSIH WASTAFEL', 'PCS'),
            array('SUPER PEL', 'PCS'),
            array('REFIL HANDSANITIZER', 'PCS'),
            array('KRESEK 10', 'PACK'),
            array('BIG COLA', 'PCS'),
            array('KENTANG', 'KG'),
            array('BUNCIS', 'KG'),
            array('SELADA', 'KG'),
            array('TIMUN', 'KG'),
            array('WORTEL', 'KG'),
            array('TOMAT MERAH', 'KG'),
            array('BAWANG PUTIH', 'KG'),
            array('GARAM 250gr', 'PCS'),
            array('BOX BENTO/KOTAK STEAK', 'PCS'),
            array('SASA MICIN 250 gr', 'PCS'),
            array('OBAT MINYAK', 'GRAM'),
        );

        $data_tidak_ditemukan = null;
        foreach ($data_barang_excel as $key => $value) {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('nama', 'like', '%'.$value[0].'%')->orderBy('nama', 'asc')->first();

            if ( !$d_item ) {
                $m_item = new \Model\Storage\Item_model();

                $kode = $m_item->getNextId();

                $m_item->kode = $kode;
                $m_item->nama = $value[0];
                $m_item->brand = NULL;
                $m_item->satuan = strtoupper($value[1]);
                $m_item->group_kode = NULL;
                $m_item->keterangan = NULL;
                $m_item->save();
            } else {
                $m_item = new \Model\Storage\Item_model();

                $kode = $m_item->getNextId();

                $m_item->where('kode', $kode)->update(
                    array(
                        'satuan' => strtoupper($value[1])
                    )
                );
            }
        }
    }

    public function insertStokOpname()
    {
        $data_barang_excel = array(
            array('AYAM DADA 1A', 144.00, 0.00),
            array('AYAM SAYAP', 85.00, 0.00),
            array('AYAM PAHA BAWAH', 80.00, 0.00),
            array('AYAM DADA 4A', 66.00, 0.00),
            array('AYAM PAHA ATAS', 30.00, 0.00),
            array('PATTY', 6.00, 0.00),
            array('CRISPY CHICKEN STEAK', 6.00, 0.00),
            array('GRILL CHICKEN STEAK', 23.00, 0.00),
            array('CHICKEN SKIN', 719.00, 0.00),
            array('TEPUNG SEGITIGA BIRU 4KG', 4.00, 40640.00),
            array('TEPUNG MAIZENA GORENG 300G', 5.00, 3780.00),
            array('TEPUNG RAHASIA', 4.00, 0.00),
            array('MINYAK GORENG FORTUNE 1LT', 13.00, 12800.00),
            array('BERAS PULEN ONDE 5KG', 28.00, 51000.00),
            array('GULA LOKAL 1KG', 10.00, 12350.00),
            array('TEH SARIWANGI', 6.00, 5120.00),
            array('SAOS TOMAT SACHET', 4.00, 5300.00),
            array('SAOS SAMBAL SACHET', 4.00, 6250.00),
            array('SAMBAL BAWANG LVL 1', 4.00, 0.00),
            array('SAMBAL BAWANG LVL 2', 4.00, 0.00),
            array('SAMBAL BAWANG LVL 3', 4.00, 0.00),
            array('SAMBAL BAWANG LVL 4', 4.00, 0.00),
            array('SAMBAL IJO', 3.00, 0.00),
            array('SAMBAL MATAH ', 3.00, 0.00),
            array('SAUS TOMAT 1KG ', 2.00, 15000.00),
            array('SAUS SAMBAL 1KG', 1.00, 19500.00),
            array('SAUS MAYONAISE', 384.00, 0.00),
            array('SAUS KEJU TOPPING', 0.00, 0.00),
            array('SAUS KEJU KULIT', 0.00, 0.00),
            array('SAUS BLACKPAPER', 1.00, 0.00),
            array('KEJU MOZARELLA', 0.00, 26000.00),
            array('SUSU ULTRA UHT', 2.00, 17000.00),
            array('TEPUNG MAIZENA STEAK 1KG', 834.00, 12600.00),
            array('TISU HANDTOWEL', 4.00, 8100.00),
            array('PAPER BAG ALA CARTE', 0.00, 80000.00),
            array('KERTAS KENTANG', 2.00, 20000.00),
            array('MARGARIN', 3.00, 6000.00),
            array('ROTI BURGER', 13.00, 11111.00),
            array('FRENCH FRIES', 2735.00, 27.50),
            array('SARUNG TANGAN', 4.00, 7000.00),
            array('LUNCH BOX M', 13.00, 800.00),
            array('SEALER', 2.00, 65000.00),
            array('KERTAS NASI', 5.00, 8500.00),
            array('STIKER', 497.00, 103.50),
            array('CUP TEH', 6.00, 9125.00),
            array('LUNCH BOX L', 150.00, 1500.00),
            array('TEMPAT SAMBAL DINE IN', 25.00, 0.00),
            array('PLASTIK 1 KG', 3.00, 7600.00),
            array('CUP SAMBAL 35', 5.00, 7200.00),
            array('CUP SAMBAL 25', 5.00, 6000.00),
            array('SEDOTAN', 4.00, 20000.00),
            array('KRESEK 24', 5.00, 7200.00),
            array('KRESEK 28', 3.00, 7200.00),
            array('KRESEK 35', 3.00, 11500.00),
            array('TISU TOILET', 4.00, 8500.00),
            array('KRESEK SAMPAH', 91.00, 60000.00),
            array('STELLA REFIL SPRAY', 2.00, 30500.00),
            array('SABUN CUCI TANGAN', 4.00, 18900.00),
            array('SABUN TOILET', 4.00, 11700.00),
            array('CLING', 3.00, 3230.00),
            array('SUNLIGHT', 1.00, 22000.00),
            array('VIXAL', 1.00, 16500.00),
            array('KAPUR BARUS', 5.00, 20000.00),
            array('DETERGEN', 3.00, 5000.00),
            array('PEMBERSIH TOILET DUDUK', 1.00, 14000.00),
            array('PEMBERSIH WASTAFEL', 0.00, 51000.00),
            array('SUPER PEL', 0.00, 14000.00),
            array('REFIL HANDSANITIZER', 1.00, 37000.00),
            array('KRESEK 10', 4.00, 4200.00),
            array('BIG COLA', 1.00, 20500.00),
            array('KENTANG', 0.00, 0.00),
            array('BUNCIS', 0.00, 0.00),
            array('SELADA', 0.00, 0.00),
            array('TIMUN', 0.00, 0.00),
            array('WORTEL', 0.00, 0.00),
            array('TOMAT MERAH', 0.00, 0.00),
            array('BAWANG PUTIH', 0.00, 0.00),
            array('GARAM 250gr', 0.00, 1800.00),
            array('BOX BENTO/KOTAK STEAK', 41.00, 1750.00),
            array('SASA MICIN 250 gr', 0.00, 12187.00),
            array('OBAT MINYAK', 200.00, 213.26),
        );

        $m_adjin = new \Model\Storage\Adjin_model();
        $now = $m_adjin->getDate();

        $kode_adjin = $m_adjin->getNextIdRibuan();

        $m_adjin->kode_adjin = $kode_adjin;
        $m_adjin->tgl_adjin = '2022-10-13';
        $m_adjin->branch_kode = 'JBR1';
        $m_adjin->keterangan = 'STOK OPNAME';
        $m_adjin->save();

        foreach ($data_barang_excel as $key => $value) {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('nama', 'like', '%'.$value[0].'%')->orderBy('nama', 'asc')->first();

            if ( $d_item ) {
                $m_adjini = new \Model\Storage\AdjinItem_model();
                $m_adjini->adjin_kode = $kode_adjin;
                $m_adjini->item_kode = $d_item->kode;
                $m_adjini->jumlah = $value[1];
                $m_adjini->harga = $value[2];
                $m_adjini->save();

                $m_stok = new \Model\Storage\Stok_model();
                $m_stok->id_header = 1028;
                $m_stok->tgl_trans = '2022-10-13 12:00:00';
                $m_stok->tanggal = '2022-10-13';
                $m_stok->kode_trans = $kode_adjin;
                $m_stok->branch_kode = 'JBR1';
                $m_stok->item_kode = $d_item->kode;
                $m_stok->harga_beli = $value[2];
                $m_stok->harga_jual = $value[2];
                $m_stok->jumlah = $value[1];
                $m_stok->sisa_stok = $value[1];
                $m_stok->tbl_name = $m_adjin->getTable();
                $m_stok->save();
            }
        }
    }
}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PenerimaanBarangMutasi extends Public_Controller {

    private $pathView = 'transaksi/penerimaan_barang_mutasi/';
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
                "assets/transaksi/penerimaan_barang_mutasi/js/penerimaan-barang-mutasi.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/penerimaan_barang_mutasi/css/penerimaan-barang-mutasi.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['title_panel'] = 'Penerimaan Barang Mutasi';

            // Load Indexx
            $data['title_menu'] = 'Penerimaan Barang Mutasi';
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

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];

        $data = $this->getDataLists( $start_date, $end_date );

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function getDataLists($start_date, $end_date) {
        // $m_mutasi = new \Model\Storage\Mutasi_model();
        // $d_mutasi = $m_mutasi->whereBetween('tgl_mutasi', [$start_date, $end_date])->with(['gudang_asal', 'gudang_tujuan'])->orderBy('tgl_mutasi', 'desc')->get();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                m.kode_mutasi,
                m.nama_pic,
                m.tgl_mutasi,
                m.asal,
                g_asal.nama as nama_gudang_asal,
                m.tujuan,
                g_tujuan.nama as nama_gudang_tujuan,
                m.no_sj,
                m.g_status,
                gi.coa,
                cast(gi.ket_coa as varchar(max)) as ket_coa,
                (isnull(sh.harga, 0) / mi.pengali) as harga,
                cast(sum(((mi.jumlah * mi.pengali) * isnull(sh.harga, 0))) as decimal(15, 2)) as total
            from mutasi_item mi
            right join
                mutasi m
                on
                    mi.mutasi_kode = m.kode_mutasi
            left join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            left join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            left join
                item i
                on
                    i.kode = mi.item_kode
            left join
                group_item gi
                on
                    gi.kode = i.group_kode
            left join
                (
                    select  
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        sum(st.jumlah) as jumlah,
                        cast(st.tbl_name as varchar(max)) as tbl_name
                    from stok_trans st
                    right join
                        stok s
                        on
                            st.id_header = s.id
                    group by
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        cast(st.tbl_name as varchar(max))
                ) st
                on
                    st.kode_trans = m.kode_mutasi and
                    st.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = st.id_header and
                    sh.item_kode = mi.item_kode
            where
                m.tgl_mutasi between '".$start_date."' and '".$end_date."'
            group by
                m.kode_mutasi,
                m.nama_pic,
                m.tgl_mutasi,
                m.asal,
                g_asal.nama,
                m.tujuan,
                g_tujuan.nama,
                m.no_sj,
                m.g_status,
                gi.coa,
                cast(gi.ket_coa as varchar(max)),
                sh.harga,
                mi.pengali
        ";
        $d_mutasi = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_mutasi->count() > 0 ) {
            $d_mutasi = $d_mutasi->toArray();

            foreach ($d_mutasi as $key => $value) {
                if ( !isset($data[ $value['kode_mutasi'] ]) ) {
                    $data[ $value['kode_mutasi'] ] = array(
                        'kode_mutasi' => $value['kode_mutasi'],
                        'tgl_mutasi' => $value['tgl_mutasi'],
                        'nama_pic' => $value['nama_pic'],
                        'nama_gudang_asal' => $value['nama_gudang_asal'],
                        'nama_gudang_tujuan' => $value['nama_gudang_tujuan'],
                        'g_status' => $value['g_status'],
                        'total' => $value['total'],
                        'list_coa' => null
                    );
                    $data[ $value['kode_mutasi'] ]['list_coa'][ $value['coa'] ] = array(
                        'coa' => $value['coa'],
                        'ket_coa' => $value['ket_coa']
                    );
                } else {
                    $data[ $value['kode_mutasi'] ]['total'] += $value['total'];
                    $data[ $value['kode_mutasi'] ]['list_coa'][ $value['coa'] ] = array(
                        'coa' => $value['coa'],
                        'ket_coa' => $value['ket_coa']
                    );
                }
            }
        }

        return $data;
    }

    public function viewForm($kode)
    {
        // $m_mutasi = new \Model\Storage\Mutasi_model();
        // $d_mutasi = $m_mutasi->where('kode_mutasi', $kode)->with(['gudang_asal', 'gudang_tujuan', 'detail'])->first();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                m.kode_mutasi,
                m.nama_pic,
                m.tgl_mutasi,
                m.asal,
                g_asal.nama as nama_gudang_asal,
                m.tujuan,
                g_tujuan.nama as nama_gudang_tujuan,
                m.no_sj,
                m.lampiran,
                m.keterangan,
                m.g_status,
                mi.item_kode,
                i.nama as nama_item,
                mi.jumlah,
                mi.satuan,
                mi.pengali,
                gi.nama as nama_group_item,
                gi.coa,
                gi.ket_coa,
                (isnull(sh.harga, 0) / mi.pengali) as harga,
                ((mi.jumlah * mi.pengali) * isnull(sh.harga, 0)) as total
            from mutasi_item mi
            right join
                mutasi m
                on
                    mi.mutasi_kode = m.kode_mutasi
            left join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            left join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            left join
                item i
                on
                    i.kode = mi.item_kode
            left join
                group_item gi
                on
                    gi.kode = i.group_kode
            left join
                (
                    select  
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        sum(st.jumlah) as jumlah,
                        cast(st.tbl_name as varchar(max)) as tbl_name
                    from stok_trans st
                    right join
                        stok s
                        on
                            st.id_header = s.id
                    group by
                        s.id_header, 
                        s.item_kode, 
                        st.kode_trans,
                        cast(st.tbl_name as varchar(max))
                ) st
                on
                    st.kode_trans = m.kode_mutasi and
                    st.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = st.id_header and
                    sh.item_kode = mi.item_kode
            where
                m.kode_mutasi = '".$kode."'
        ";
        $d_mutasi = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_mutasi ) {
            $d_mutasi = $d_mutasi->toArray();

            $data = array(
                'kode_mutasi' => $d_mutasi[0]['kode_mutasi'],
                'nama_pic' => $d_mutasi[0]['nama_pic'],
                'tgl_mutasi' => $d_mutasi[0]['tgl_mutasi'],
                'asal' => $d_mutasi[0]['asal'],
                'nama_gudang_asal' => $d_mutasi[0]['nama_gudang_asal'],
                'tujuan' => $d_mutasi[0]['tujuan'],
                'nama_gudang_tujuan' => $d_mutasi[0]['nama_gudang_tujuan'],
                'no_sj' => $d_mutasi[0]['no_sj'],
                'lampiran' => $d_mutasi[0]['lampiran'],
                'keterangan' => $d_mutasi[0]['keterangan'],
                'g_status' => $d_mutasi[0]['g_status']
            );

            foreach ($d_mutasi as $key => $value) {
                $data['detail'][] = array(
                    'item_kode' => $value['item_kode'],
                    'nama_item' => $value['nama_item'],
                    'jumlah' => $value['jumlah'],
                    'satuan' => $value['satuan'],
                    'pengali' => $value['pengali'],
                    'nama_group_item' => $value['nama_group_item'],
                    'coa' => $value['coa'],
                    'ket_coa' => $value['ket_coa'],
                    'harga' => $value['harga'],
                    'total' => $value['total']
                );
            }
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function approve()
    {
        $kode_mutasi = $this->input->post('kode_mutasi');

        try {
            $m_mutasi = new \Model\Storage\Mutasi_model();
            $now = $m_mutasi->getDate();

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_hitung_stok_awal @tanggal = '".$now['waktu']."'";

            // $d_conf = $conf->hydrateRaw($sql);

            $m_mutasi->where('kode_mutasi', $kode_mutasi)->update(
                array(
                    'g_status' => getStatus('approve')
                )
            );

            $d_mutasi = $m_mutasi->where('kode_mutasi', $kode_mutasi)->first();

            $deskripsi_log = 'di-terima oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_mutasi, $deskripsi_log, $kode_mutasi );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_mutasi);
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

            $m_conf = new \Model\Storage\Conf();

            $tgl_transaksi = null;
            $gudang = null;
            $barang = null;

            $sql_tgl_dan_gudang = "
                select m.* from mutasi m
                where
                    m.kode_mutasi = '".$kode."'
            ";
            $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
            if ( $d_tgl_dan_gudang->count() > 0 ) {
                $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
                $tgl_transaksi = $d_tgl_dan_gudang['tgl_mutasi'];
                $gudang = $d_tgl_dan_gudang['asal'].','.$d_tgl_dan_gudang['tujuan'];
            }

            $sql_barang = "
                select m.tgl_mutasi, mi.item_kode from mutasi_item mi
                right join
                    mutasi m
                    on
                        m.kode_mutasi = mi.mutasi_kode
                where
                    mi.mutasi_kode = '".$kode."'
                group by
                    m.tgl_mutasi,
                    mi.item_kode
            ";
            $d_barang = $m_conf->hydrateRaw( $sql_barang );
            if ( $d_barang->count() > 0 ) {
                $d_barang = $d_barang->toArray();

                foreach ($d_barang as $key => $value) {
                    $barang[] = $value['item_kode'];
                }
            }

            $sql = "EXEC sp_hitung_stok_by_barang @barang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($barang))))."', @tgl_transaksi = '".$tgl_transaksi."', @gudang = '".str_replace('"', '', str_replace(']', '', str_replace('[', '', json_encode($gudang))))."'";

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_tambah_stok @kode = '".$kode."', @table = 'terima'";

            $d_conf = $m_conf->hydrateRaw($sql);

            // $conf = new \Model\Storage\Conf();
            // $sql = "EXEC sp_tambah_stok @kode = '".$kode."', @table = 'mutasi'";

            // $d_conf = $conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di terima.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
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

        $start_date = $_data_params['start_date'];
        $end_date = $_data_params['end_date'];

        $data = $this->getDataLists($start_date, $end_date);

        cetak_r($data, 1);

        $content['data'] = $data;
        $res_view_html = $this->load->view('transaksi/penerimaan_barang_mutasi/export_excel', $content, true);

        $filename = 'export-riwayat-penerimaan-barang-mutasi-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        // header("Content-type: application/xls");
        header("Content-type: application/ms-excel; charset=utf-8");
        // header("Content-Type: application/vnd.ms-excel"); 
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }

    public function tes()
    {
        $kode = 'MT23070004';

        $m_conf = new \Model\Storage\Conf();

        $tgl_transaksi = null;
        $gudang = null;
        $barang = null;

        $sql_tgl_dan_gudang = "
            select m.* from mutasi m
            where
                m.kode_mutasi = '".$kode."'
        ";
        $d_tgl_dan_gudang = $m_conf->hydrateRaw( $sql_tgl_dan_gudang );
        if ( $d_tgl_dan_gudang->count() > 0 ) {
            $d_tgl_dan_gudang = $d_tgl_dan_gudang->toArray()[0];
            $tgl_transaksi = $d_tgl_dan_gudang['tgl_mutasi'];
            $gudang = $d_tgl_dan_gudang['asal'].','.$d_tgl_dan_gudang['tujuan'];
        }

        $sql_barang = "
            select m.tgl_mutasi, mi.item_kode from mutasi_item mi
            right join
                mutasi m
                on
                    m.kode_mutasi = mi.mutasi_kode
            where
                mi.mutasi_kode = '".$kode."'
            group by
                m.tgl_mutasi,
                mi.item_kode
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
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mutasi extends Public_Controller {

    private $pathView = 'transaksi/mutasi/';
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
                "assets/jquery/list.min.js",
                "assets/transaksi/mutasi/js/mutasi.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/mutasi/css/mutasi.css"
            ));

            $data = $this->includes;

            // $m_item = new \Model\Storage\Item_model();
            // $d_item = $m_item->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Mutasi Barang';

            // Load Indexx
            $data['title_menu'] = 'Mutasi Barang';
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

    public function getItem($kode_gudang, $tanggal)
    {
        // $m_item = new \Model\Storage\Item_model();
        // $d_item = $m_item->with(['satuan', 'group'])->orderBy('nama', 'asc')->get();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                i.kode,
                i.nama,
                gi.kode as kode_group,
                gi.nama as nama_group,
                gi.coa,
                gi.ket_coa,
                _is.satuan, 
                _is.pengali,
                (isnull(sh.harga, 0) * _is.pengali) as harga
            from item i
            right join
                item_satuan _is
                on
                    _is.item_kode = i.kode
            right join
                group_item gi
                on
                    i.group_kode = gi.kode
            left join
                (
                    select sh.* from stok_harga sh
                    right join
                        stok_tanggal st
                        on
                            sh.id_header = st.id
                    where 
                        st.gudang_kode = '".$kode_gudang."' and 
                        st.tanggal = '".$tanggal."'
                ) sh
                on
                    sh.item_kode = i.kode
            group by
                i.kode,
                i.nama,
                gi.kode,
                gi.nama,
                gi.coa,
                gi.ket_coa,
                _is.satuan, 
                _is.pengali,
                sh.harga
        ";
        $d_item = $m_conf->hydrateRaw( $sql );

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $d_item = $d_item->toArray();

            foreach ($d_item as $key => $value) {
                if ( !isset($data_item[ $value['kode'] ]) ) {
                    $data_item[ $value['kode'] ] = array(
                        'kode' => $value['kode'],
                        'nama' => $value['nama'],
                        'kode_group' => $value['kode_group'],
                        'nama_group' => $value['nama_group'],
                        'coa' => $value['coa'],
                        'ket_coa' => $value['ket_coa'],
                        'harga' => $value['harga']
                    );
                }

                $data_item[ $value['kode'] ]['satuan'][] = array(
                    'satuan' => $value['satuan'],
                    'pengali' => $value['pengali'],
                    'harga' => $value['harga']
                );
            }
        }

        return $data_item;
    }

    public function getHargaItem()
    {
        $params = $this->input->get('params');

        $kode_gudang = $params['asal'];
        $tanggal = $params['tgl_mutasi'];

        $data = $this->getItem($kode_gudang, $tanggal);

        $opt = "<option value=''>-- Pilih Item --</option>";
        if ( !empty($data) ) {
            foreach ($data as $key => $value) {
                $opt .= "<option value='".$value['kode']."' data-satuan='".json_encode($value['satuan'])."' data-coa='".$value['coa']."' data-ketcoa='".$value['ket_coa']."'>".strtoupper($value['nama'])."</option>";
            }
        }

        $html = $opt;

        echo $html;
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
                sum(((mi.jumlah * mi.pengali) * isnull(sh.harga, 0))) as total
            from mutasi_item mi
            right join
                mutasi m
                on
                    mi.mutasi_kode = m.kode_mutasi
            right join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            right join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            right join
                item i
                on
                    i.kode = mi.item_kode
            right join
                group_item gi
                on
                    gi.kode = i.group_kode
            left join
                stok_trans st
                on
                    st.kode_trans = m.kode_mutasi
            left join
                stok s
                on
                    s.id = st.id_header and
                    s.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = s.id_header and
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

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
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
            right join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            right join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            right join
                item i
                on
                    i.kode = mi.item_kode
            right join
                group_item gi
                on
                    gi.kode = i.group_kode
            left join
                stok_trans st
                on
                    st.kode_trans = m.kode_mutasi
            right join
                stok s
                on
                    s.id = st.id_header and
                    s.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = s.id_header and
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

    public function addForm()
    {
        // $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function editForm($kode)
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
            right join
                gudang g_asal
                on
                    m.asal = g_asal.kode_gudang
            right join
                gudang g_tujuan
                on
                    m.tujuan = g_tujuan.kode_gudang
            right join
                item i
                on
                    i.kode = mi.item_kode
            right join
                group_item gi
                on
                    gi.kode = i.group_kode
            left join
                stok_trans st
                on
                    st.kode_trans = m.kode_mutasi
            right join
                stok s
                on
                    s.id = st.id_header and
                    s.item_kode = mi.item_kode
            left join
                (
                    select sh1.* from stok_harga sh1
                    right join
                        (select max(id) as id, id_header, item_kode from stok_harga group by id_header, item_kode) sh2
                        on
                            sh1.id = sh2.id
                ) sh
                on
                    sh.id_header = s.id_header and
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
        $content['item'] = $this->getItem( $data['asal'], $data['tgl_mutasi'] );
        $content['gudang'] = $this->getGudang();

        // cetak_r( $data );

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = json_decode($this->input->post('data'),TRUE);
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;

        try {
            $path_name = null;
            if ( !empty($file) ) {
                $moved = uploadFile($file);
                if ( $moved ) {
                    $path_name = $moved['path'];
                }
            }

            $m_mutasi = new \Model\Storage\Mutasi_model();
            $now = $m_mutasi->getDate();

            $kode_mutasi = $m_mutasi->getNextIdRibuan();

            $m_mutasi->kode_mutasi = $kode_mutasi;
            $m_mutasi->nama_pic = $params['nama_pic'];
            $m_mutasi->tgl_mutasi = $params['tgl_mutasi'];
            $m_mutasi->asal = $params['asal'];
            $m_mutasi->tujuan = $params['tujuan'];
            $m_mutasi->no_sj = $params['no_sj'];
            $m_mutasi->lampiran = $path_name;
            $m_mutasi->keterangan = $params['keterangan'];
            $m_mutasi->g_status = getStatus('submit');
            $m_mutasi->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_mutasii = new \Model\Storage\MutasiItem_model();
                $m_mutasii->mutasi_kode = $kode_mutasi;
                $m_mutasii->item_kode = $v_det['item_kode'];
                $m_mutasii->jumlah = $v_det['jumlah'];
                $m_mutasii->satuan = $v_det['satuan'];
                $m_mutasii->pengali = $v_det['pengali'];
                $m_mutasii->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_mutasi, $deskripsi_log, $kode_mutasi );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_mutasi);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = json_decode($this->input->post('data'),TRUE);
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;

        try {
            $m_mutasi = new \Model\Storage\Mutasi_model();
            $now = $m_mutasi->getDate();

            $kode_mutasi = $params['kode_mutasi'];

            $d_mutasi = $m_mutasi->where('kode_mutasi', $kode_mutasi)->first();
            $path_name = $d_mutasi->lampiran;
            if ( !empty($file) ) {
                $moved = uploadFile($file);
                if ( $moved ) {
                    $path_name = $moved['path'];
                }
            }

            $m_mutasi->where('kode_mutasi', $kode_mutasi)->update(
                array(
                    'nama_pic' => $params['nama_pic'],
                    'tgl_mutasi' => $params['tgl_mutasi'],
                    'asal' => $params['asal'],
                    'tujuan' => $params['tujuan'],
                    'no_sj' => $params['no_sj'],
                    'lampiran' => $path_name,
                    'keterangan' => $params['keterangan']
                )
            );

            $m_mutasii = new \Model\Storage\MutasiItem_model();
            $m_mutasii->where('mutasi_kode', $kode_mutasi)->delete();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_mutasii = new \Model\Storage\MutasiItem_model();
                $m_mutasii->mutasi_kode = $kode_mutasi;
                $m_mutasii->item_kode = $v_det['item_kode'];
                $m_mutasii->jumlah = $v_det['jumlah'];
                $m_mutasii->satuan = $v_det['satuan'];
                $m_mutasii->pengali = $v_det['pengali'];
                $m_mutasii->save();
            }

            $d_mutasi = $m_mutasi->where('kode_mutasi', $kode_mutasi)->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_mutasi, $deskripsi_log, $kode_mutasi );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_mutasi);
            $this->result['message'] = 'Data berhasil di update.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $m_mutasi = new \Model\Storage\Mutasi_model();
            $now = $m_mutasi->getDate();

            $kode_mutasi = $params['kode_mutasi'];

            $d_mutasi = $m_mutasi->where('kode_mutasi', $kode_mutasi)->first();

            $m_mutasii = new \Model\Storage\MutasiItem_model();
            $m_mutasii->where('mutasi_kode', $kode_mutasi)->delete();
            $m_mutasi->where('kode_mutasi', $kode_mutasi)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_mutasi, $deskripsi_log, $kode_mutasi );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_mutasi);
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
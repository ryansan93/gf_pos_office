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

        $start_date = ($params['start_date'] >= $tgl_stok_opname) ? $params['start_date'] : $tgl_stok_opname;
        $end_date = $params['end_date'];

        $m_mutasi = new \Model\Storage\Mutasi_model();
        $d_mutasi = $m_mutasi->whereBetween('tgl_mutasi', [$start_date, $end_date])->with(['gudang_asal', 'gudang_tujuan'])->orderBy('tgl_mutasi', 'desc')->get();

        $data = null;
        if ( $d_mutasi->count() > 0 ) {
            $data = $d_mutasi->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_mutasi = new \Model\Storage\Mutasi_model();
        $d_mutasi = $m_mutasi->where('kode_mutasi', $kode)->with(['gudang_asal', 'gudang_tujuan', 'detail'])->first();

        $data = null;
        if ( $d_mutasi ) {
            $data = $d_mutasi->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function editForm($kode)
    {
        $m_mutasi = new \Model\Storage\Mutasi_model();
        $d_mutasi = $m_mutasi->where('kode_mutasi', $kode)->with(['gudang_asal', 'gudang_tujuan', 'detail'])->first();

        $data = null;
        if ( $d_mutasi ) {
            $data = $d_mutasi->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;
        $content['item'] = $this->getItem();
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
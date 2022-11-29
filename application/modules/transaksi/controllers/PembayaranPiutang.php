<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PembayaranPiutang extends Public_Controller {

    private $pathView = 'transaksi/pembayaran_piutang/';
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
                "assets/jquery/list.min.js",
                "assets/transaksi/pembayaran_piutang/js/pembayaran-piutang.js",
            ));
            $this->add_external_css(array(
                "assets/transaksi/pembayaran_piutang/css/pembayaran-piutang.css",
            ));

            $data = $this->includes;

            // $m_item = new \Model\Storage\Item_model();
            // $d_item = $m_item->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Pembayaran Piutang';

            // Load Indexx
            $data['title_menu'] = 'Pembayaran Piutang';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $_d_jenis_kartu = $m_jenis_kartu->where('status', 1)->get();

        $d_jenis_kartu = null;
        if ( $_d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $_d_jenis_kartu->toArray();
        }

        return $d_jenis_kartu;
    }

    public function getDataHutang()
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('lunas', 0)->where('mstatus', 1)->with(['branch'])->orderBy('kode_faktur', 'asc')->get();

        $data_jual = null;
        if ( $d_jual->count() > 0 ) {
            $data_jual = $d_jual->toArray();
        }

        return $data_jual;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'].' 00:00:00';
        $end_date = $params['end_date'].' 23:59:59';

        $m_bnk = new \Model\Storage\BayarNonKasir_model();
        $d_bnk = $m_bnk->whereBetween('tgl_bayar', [$start_date, $end_date])->orderBy('tgl_bayar', 'desc')->get();

        $data = null;
        if ( $d_bnk->count() > 0 ) {
            $data = $d_bnk->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_bnk = new \Model\Storage\BayarNonKasir_model();
        $d_bnk = $m_bnk->where('kode', $kode)->with(['bayar', 'jenis_kartu'])->first()->toArray();

        $content['data'] = $d_bnk;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['jenis_kartu'] = $this->getJenisKartu();
        $content['data_hutang'] = $this->getDataHutang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

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

            $m_bnk = new \Model\Storage\BayarNonKasir_model();
            $now = $m_bnk->getDate();

            $kode_bnk = $m_bnk->getNextId();

            $m_bnk->kode = $kode_bnk;
            $m_bnk->tgl_bayar = $params['tgl_bayar'];
            $m_bnk->tot_tagihan = $params['tot_tagihan'];
            $m_bnk->tot_bayar = $params['tot_bayar'];
            $m_bnk->lampiran = $path_name;
            $m_bnk->jenis_bayar = $params['jenis_pembayaran'];
            $m_bnk->jenis_kartu_kode = ($params['kode_kartu'] != 'tunai') ? $params['kode_kartu'] : '';
            $m_bnk->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_bayar = new \Model\Storage\Bayar_model();

                $m_bayar->tgl_trans = $now['waktu'];
                $m_bayar->faktur_kode = $v_det['kode_faktur'];
                $m_bayar->jml_tagihan = $v_det['jml_tagihan'];
                $m_bayar->jml_bayar = $v_det['jml_tagihan'];
                $m_bayar->jenis_bayar = $params['jenis_pembayaran'];
                $m_bayar->jenis_kartu_kode = ($params['kode_kartu'] != 'tunai') ? $params['kode_kartu'] : '';
                $m_bayar->kode_bayar_non_kasir = $kode_bnk;
                $m_bayar->save();

                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $v_det['kode_faktur'])->update(
                    array(
                        'lunas' => 1
                    )
                );
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_bnk, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_bnk);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $kode = $this->input->post('kode');

        try {
            $m_bnk = new \Model\Storage\BayarNonKasir_model();
            $d_bnk = $m_bnk->where('kode', $kode)->with(['bayar'])->first();

            $_d_bnk = $d_bnk->toArray();
            foreach ($_d_bnk['bayar'] as $key => $value) {
                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $value['faktur_kode'])->update(
                    array(
                        'lunas' => 0
                    )
                );

                $m_bayar = new \Model\Storage\Bayar_model();
                $m_bayar->where('id', $value['id'])->delete();
            }

            $m_bnk->where('kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_bnk, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
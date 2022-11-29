<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan extends Public_Controller {

    private $pathView = 'transaksi/penerimaan/';
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
                "assets/transaksi/penerimaan/js/penerimaan.js",
            ));
            $this->add_external_css(array(
                "assets/transaksi/penerimaan/css/penerimaan.css",
            ));

            $data = $this->includes;

            // $m_item = new \Model\Storage\Item_model();
            // $d_item = $m_item->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Penerimaan Barang';

            // Load Indexx
            $data['title_menu'] = 'Penerimaan Barang';
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
        } else if ( !empty($id) && !empty($resubmit) ) {
            $html = $this->editForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function listFakturPembelian()
    {
        $params = $this->input->post('params');
        try {
            $tgl_stok_opname = $this->config->item('tgl_stok_opname');

            $start_date = ($params['start_date'] >= $tgl_stok_opname) ? $params['start_date'] : $tgl_stok_opname;
            $end_date = $params['end_date'];

            $m_beli = new \Model\Storage\Beli_model();
            $d_beli = $m_beli->whereBetween('tgl_beli', [$start_date, $end_date])->with(['supplier', 'branch'])->get();

            $data = null;
            if ( $d_beli->count() > 0 ) {
                $_data = null;
                $d_beli = $d_beli->toArray();
                foreach ($d_beli as $k_beli => $v_beli) {
                    $m_terima = new \Model\Storage\Terima_model();
                    $d_terima = $m_terima->where('beli_kode', $v_beli['kode_beli'])->first();

                    if ( !$d_terima ) {
                        $key = $v_beli['supplier']['nama'].'|'.$v_beli['kode_beli'];

                        $_data[ $key ] = array(
                            'kode_beli' => $v_beli['kode_beli'],
                            'no_faktur' => $v_beli['no_faktur'],
                            'supplier' => $v_beli['supplier']['nama'],
                            'branch' => $v_beli['branch']['nama']
                        );
                    }
                }

                if ( !empty($_data) ) {
                    ksort($_data);
                    foreach ($_data as $key => $value) {
                        $data[] = $value;
                    }
                }
            }

            $this->result['status'] = 1;
            $this->result['content'] = array('list' => $data);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function dataBeli()
    {
        $kode_beli = $this->input->post('kode_beli');
        try {
            $m_beli = new \Model\Storage\Beli_model();
            $d_beli = $m_beli->where('kode_beli', $kode_beli)->with(['supplier', 'branch', 'detail'])->first();

            $data = null;
            if ( $d_beli ) {
                $data = $d_beli->toArray();
            }

            $content['data'] = $data;
            $html = $this->load->view($this->pathView . 'dataBeliForm', $content, TRUE);
            
            $this->result['status'] = 1;
            $this->result['content'] = array('html' => $html);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $tgl_stok_opname = $this->config->item('tgl_stok_opname');

        $m_terima = new \Model\Storage\Terima_model();
        $d_terima = $m_terima->where('tgl_terima', '>', $tgl_stok_opname)->with(['beli'])->orderBy('tgl_terima', 'desc')->get();

        $data = null;
        if ( $d_terima->count() > 0 ) {
            $data = $d_terima->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_terima = new \Model\Storage\Terima_model();
        $d_terima = $m_terima->where('kode_terima', $kode)->with(['beli', 'detail'])->first();

        $data = null;
        if ( $d_terima ) {
            $d_terima = $d_terima->toArray();

            $data = $d_terima;
            foreach ($d_terima['detail'] as $k_det => $v_det) {
                $m_belii = new \Model\Storage\BeliItem_model();
                $d_belii = $m_belii->where('beli_kode', $data['beli_kode'])->where('item_kode', $v_det['item_kode'])->sum('jumlah');

                $data['detail'][$k_det]['jumlah'] = $d_belii;
            }
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $html = $this->load->view($this->pathView . 'addForm', null, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_beli = new \Model\Storage\Beli_model();
            $d_beli = $m_beli->where('kode_beli', $params['beli_kode'])->first();

            /* STOK */
            $date = $this->config->item('date');
            $tgl_stok_opname = $this->config->item('tgl_stok_opname');

            if ( $date >= $tgl_stok_opname ) {
                $m_stokt = new \Model\Storage\StokTanggal_model();
                $d_stokt = $m_stokt->where('tanggal', $date)->where('branch_kode', $d_beli->branch_kode)->first();

                $id_header = null;
                if ( $d_stokt ) {
                    $id_header = $d_stokt->id;
                } else {
                    $m_stokt->tanggal = $date;
                    $m_stokt->branch_kode = $d_beli->branch_kode;
                    $m_stokt->save();

                    $id_header = $m_stokt->id;
                }

                $d_stokt_prev = $m_stokt->where('tanggal', '<', $date)->where('branch_kode', $d_beli->branch_kode)->orderBy('tanggal', 'desc')->first();

                if ( $d_stokt_prev ) {
                    $m_stok = new \Model\Storage\Stok_model();
                    $d_stok = $m_stok->where('id_header', $d_stokt_prev->id)->where('sisa_stok', '>', 0)->get();

                    if ( $d_stok->count() > 0 ) {
                        $d_stok = $d_stok->toArray();

                        foreach ($d_stok as $k_stok => $v_stok) {
                            $m_stok = new \Model\Storage\Stok_model();
                            $d_stok_cek = $m_stok->where('id_header', $id_header)->where('kode_trans', $v_stok['kode_trans'])->where('branch_kode', $v_stok['branch_kode'])->where('item_kode', $v_stok['item_kode'])->first();

                            if ( !$d_stok_cek ) {
                                $m_stok = new \Model\Storage\Stok_model();
                                $m_stok->id_header = $id_header;
                                $m_stok->tgl_trans = $v_stok['tgl_trans'];
                                $m_stok->tanggal = $v_stok['tanggal'];
                                $m_stok->kode_trans = $v_stok['kode_trans'];
                                $m_stok->branch_kode = $v_stok['branch_kode'];
                                $m_stok->item_kode = $v_stok['item_kode'];
                                $m_stok->harga_beli = $v_stok['harga_beli'];
                                $m_stok->harga_jual = $v_stok['harga_jual'];
                                $m_stok->jumlah = $v_stok['jumlah'];
                                $m_stok->sisa_stok = $v_stok['sisa_stok'];
                                $m_stok->tbl_name = $v_stok['tbl_name'];
                                $m_stok->save();
                            }
                        }
                    }
                }
            }
            /* END - STOK */
            
            $m_terima = new \Model\Storage\Terima_model();
            $now = $m_terima->getDate();

            $kode_terima = $m_terima->getNextIdRibuan();

            $m_terima->kode_terima = $kode_terima;
            $m_terima->tgl_terima = $params['tgl_terima'];
            $m_terima->beli_kode = $params['beli_kode'];
            $m_terima->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_terimai = new \Model\Storage\TerimaItem_model();
                $m_terimai->terima_kode = $kode_terima;
                $m_terimai->item_kode = $v_det['item_kode'];
                $m_terimai->harga = $v_det['harga'];
                $m_terimai->jumlah_terima = $v_det['jumlah_terima'];
                $m_terimai->save();

                if ( $date >= $tgl_stok_opname ) {
                    $m_stok = new \Model\Storage\Stok_model();
                    $m_stok->id_header = $id_header;
                    $m_stok->tgl_trans = $now['waktu'];
                    $m_stok->tanggal = $params['tgl_terima'];
                    $m_stok->kode_trans = $kode_terima;
                    $m_stok->branch_kode = $d_beli->branch_kode;
                    $m_stok->item_kode = $v_det['item_kode'];
                    $m_stok->harga_beli = $v_det['harga'];
                    $m_stok->harga_jual = $v_det['harga'];
                    $m_stok->jumlah = $v_det['jumlah_terima'];
                    $m_stok->sisa_stok = $v_det['jumlah_terima'];
                    $m_stok->tbl_name = $m_terima->getTable();
                    $m_stok->save();
                }
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_terima, $deskripsi_log, $kode_terima );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_terima);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
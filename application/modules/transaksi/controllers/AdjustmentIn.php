<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdjustmentIn extends Public_Controller {

    private $pathView = 'transaksi/adjustment_in/';
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
                "assets/transaksi/adjustment_in/js/adjustment-in.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/adjustment_in/css/adjustment-in.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $r_content['branch'] = $this->getBranch();
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', $r_content, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Adjustment In';

            // Load Indexx
            $data['title_menu'] = 'Adjustment In';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
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

        $m_adjin = new \Model\Storage\Adjin_model();
        $d_adjin = $m_adjin->whereBetween('tgl_adjin', [$start_date, $end_date])->whereIn('branch_kode', $params['branch_kode'])->with(['branch'])->orderBy('tgl_adjin', 'desc')->get();

        $data = null;
        if ( $d_adjin->count() > 0 ) {
            $data = $d_adjin->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_adjin = new \Model\Storage\Adjin_model();
        $d_adjin = $m_adjin->where('kode_adjin', $kode)->with(['branch', 'detail'])->first();

        $data = null;
        if ( $d_adjin ) {
            $data = $d_adjin->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['item'] = $this->getItem();
        $content['branch'] = $this->getBranch();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            /* STOK */
            $date = $this->config->item('date');
            $tgl_stok_opname = $this->config->item('tgl_stok_opname');

            if ( $date >= $tgl_stok_opname ) {
                $m_stokt = new \Model\Storage\StokTanggal_model();
                $d_stokt = $m_stokt->where('tanggal', $date)->where('branch_kode', $params['branch'])->first();

                $id_header = null;
                if ( $d_stokt ) {
                    $id_header = $d_stokt->id;
                } else {
                    $m_stokt->tanggal = $date;
                    $m_stokt->branch_kode = $params['branch'];
                    $m_stokt->save();

                    $id_header = $m_stokt->id;
                }

                $d_stokt_prev = $m_stokt->where('tanggal', '<', $date)->where('branch_kode', $params['branch'])->orderBy('tanggal', 'desc')->first();

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
            
            $m_adjin = new \Model\Storage\Adjin_model();
            $now = $m_adjin->getDate();

            $kode_adjin = $m_adjin->getNextIdRibuan();

            $m_adjin->kode_adjin = $kode_adjin;
            $m_adjin->tgl_adjin = $params['tgl_adjust'];
            $m_adjin->branch_kode = $params['branch'];
            $m_adjin->keterangan = $params['keterangan'];
            $m_adjin->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_adjini = new \Model\Storage\AdjinItem_model();
                $m_adjini->adjin_kode = $kode_adjin;
                $m_adjini->item_kode = $v_det['item_kode'];
                $m_adjini->jumlah = $v_det['jumlah'];
                $m_adjini->harga = $v_det['harga'];
                $m_adjini->save();

                $m_stok = new \Model\Storage\Stok_model();
                $m_stok->id_header = $id_header;
                $m_stok->tgl_trans = $now['waktu'];
                $m_stok->tanggal = $params['tgl_adjust'];
                $m_stok->kode_trans = $kode_adjin;
                $m_stok->branch_kode = $params['branch'];
                $m_stok->item_kode = $v_det['item_kode'];
                $m_stok->harga_beli = $v_det['harga'];
                $m_stok->harga_jual = $v_det['harga'];
                $m_stok->jumlah = $v_det['jumlah'];
                $m_stok->sisa_stok = $v_det['jumlah'];
                $m_stok->tbl_name = $m_adjin->getTable();
                $m_stok->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_adjin, $deskripsi_log, $kode_adjin );

            $this->result['status'] = 1;
            $this->result['content'] = array('id' => $kode_adjin);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
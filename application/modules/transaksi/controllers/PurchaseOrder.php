<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PurchaseOrder extends Public_Controller {

    private $pathView = 'transaksi/purchase_order/';
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
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/transaksi/purchase_order/js/purchase-order.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/transaksi/purchase_order/css/purchase-order.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', null, TRUE);
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Purchase Order';

            // Load Indexx
            $data['title_menu'] = 'Purchase Order';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
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

    public function getTax()
    {
        $m_tp = new \Model\Storage\TaxPembelian_model();
        $d_tp = $m_tp->where('mstatus', 1)->orderBy('id', 'desc')->first();

        $tax = 0;
        if ( $d_tp ) {
            $tax = $d_tp->toArray();
        }

        return $tax;
    }

    public function autocompleteSupplier() {
        $term = $this->input->get('term');

        $data = array();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select supplier from po
            where
                supplier like '%".$term."%'
            group by
                supplier
        ";
        $d_supl = $m_conf->hydrateRaw( $sql );

        if ( $d_supl->count() > 0 ) {
            $d_supl = $d_supl->toArray();

            foreach ($d_supl as $key => $value) {
                $data[] = array(
                    'label'=>$value['supplier'],
                    'value'=>$value['supplier'],
                    'id' => null
                );
            }
        } else {
            $data = array(
                'label'=>"not found",
                'value'=>"",
                'id' => ""
            );
        }

        display_json($data);
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

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];

        $m_po = new \Model\Storage\Po_model();
        $d_po = $m_po->whereBetween('tgl_po',[$start_date, $end_date])->with(['gudang'])->orderBy('tgl_po', 'desc')->get();

        $data = null;
        if ( $d_po->count() > 0 ) {
            $data = $d_po->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, true);

        echo $html;
    }

    public function viewForm($kode)
    {
        $m_po = new \Model\Storage\Po_model();
        $d_po = $m_po->where('no_po', $kode)->with(['gudang', 'detail'])->first();

        $data = null;
        if ( $d_po ) {
            $data = $d_po->toArray();
        }

        $m_terima = new \Model\Storage\Terima_model();
        $d_terima = $m_terima->where('po_no', $kode)->first();

        $terima = 0;
        if ( $d_terima ) {
            $terima = 1;
        }

        $content['terima'] = $terima;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function editForm($kode)
    {
        $m_po = new \Model\Storage\Po_model();
        $d_po = $m_po->where('no_po', $kode)->with(['gudang', 'detail'])->first();

        $data = null;
        if ( $d_po ) {
            $data = $d_po->toArray();
        }

        $content['tax'] = $this->getTax();
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        return $html;
    }

    public function addForm()
    {
        $content['tax'] = $this->getTax();
        $content['item'] = $this->getItem();
        $content['gudang'] = $this->getGudang();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {            
            $m_po = new \Model\Storage\Po_model();
            $now = $m_po->getDate();

            $no_po = $m_po->getNextNoPo();

            $m_po->no_po = $no_po;
            $m_po->tgl_po = $params['tgl_po'];
            $m_po->supplier = $params['supplier'];
            $m_po->pic = !empty($params['nama_pic']) ? $params['nama_pic'] : null;
            $m_po->gudang_kode = $params['gudang'];
            $m_po->done = 0;
            $m_po->tax = (isset($params['tax']) && !empty($params['tax'])) ? $params['tax'] : null;
            $m_po->tax_pembelian_id = (isset($params['tax_id']) && !empty($params['tax_id'])) ? $params['tax_id'] : null;
            $m_po->save();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_poi = new \Model\Storage\PoItem_model();
                $m_poi->po_no = $no_po;
                $m_poi->item_kode = $v_det['item_kode'];
                $m_poi->harga = $v_det['harga'];
                $m_poi->jumlah = $v_det['jumlah'];
                $m_poi->satuan = $v_det['satuan'];
                $m_poi->pengali = $v_det['pengali'];
                $m_poi->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_po, $deskripsi_log, $no_po );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            $this->result['content'] = array('id' => $no_po);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {            
            $m_po = new \Model\Storage\Po_model();
            $now = $m_po->getDate();

            $no_po = $params['id'];

            $m_po->where('no_po', $no_po)->update(
                array(
                    'tgl_po' => $params['tgl_po'],
                    'supplier' => $params['supplier'],
                    'pic' => !empty($params['nama_pic']) ? $params['nama_pic'] : null,
                    'gudang_kode' => $params['gudang'],
                    'tax' => (isset($params['tax']) && !empty($params['tax'])) ? $params['tax'] : null,
                    'tax_pembelian_id' => (isset($params['tax_id']) && !empty($params['tax_id'])) ? $params['tax_id'] : null
                )
            );

            $m_poi = new \Model\Storage\PoItem_model();
            $m_poi->where('po_no', $no_po)->delete();

            foreach ($params['detail'] as $k_det => $v_det) {
                $m_poi = new \Model\Storage\PoItem_model();
                $m_poi->po_no = $no_po;
                $m_poi->item_kode = $v_det['item_kode'];
                $m_poi->harga = $v_det['harga'];
                $m_poi->jumlah = $v_det['jumlah'];
                $m_poi->satuan = $v_det['satuan'];
                $m_poi->pengali = $v_det['pengali'];
                $m_poi->save();
            }

            $d_po = $m_po->where('no_po', $no_po)->first();

            $this->updatePo( $no_po );

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_po, $deskripsi_log, $no_po );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';
            $this->result['content'] = array('id' => $no_po);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {            
            $no_po = $params['id'];

            $m_po = new \Model\Storage\Po_model();
            $d_po = $m_po->where('no_po', $no_po)->first();

            $m_po->where('no_po', $no_po)->delete();

            $m_poi = new \Model\Storage\PoItem_model();
            $m_poi->where('po_no', $no_po)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_po, $deskripsi_log, $no_po );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
            $this->result['content'] = array('id' => $no_po);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportPdf($_no_po)
    {
        $no_po = exDecrypt( $_no_po );

        $m_po = new \Model\Storage\Po_model();
        $d_po = $m_po->where('no_po', $no_po)->with(['gudang', 'detail'])->first();

        $data = null;
        if ( $d_po ) {
            $data = $d_po->toArray();
        }

        $content['data'] = $data;

        $res_view_html = $this->load->view($this->pathView.'exportPdf', $content, true);

        $this->load->library('PDFGenerator');
        $this->pdfgenerator->generate($res_view_html, $no_po, "a5", "landscape");
    }

    public function updatePo($no_po)
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                pi.po_no as no_po,
                pi.item_kode as item_kode,
                pi.harga as harga,
                pi.jumlah as jumlah_po,
                t.jumlah_terima
            from po_item pi
            right join
                po p 
                on
                    pi.po_no = p.no_po
            right join
                (
                    select ti.item_kode, ti.harga, sum(ti.jumlah_terima) as jumlah_terima, t.po_no from terima_item ti 
                    right join
                        terima t
                        on
                            ti.terima_kode = t.kode_terima 
                    where
                        t.po_no is not null
                    group by
                        ti.item_kode, ti.harga, t.po_no
                ) t
                on
                    t.po_no = p.no_po and
                    t.item_kode = pi.item_kode
            where
                pi.jumlah > t.jumlah_terima and
                p.no_po = '".$no_po."'
        ";
        $d_po = $m_conf->hydrateRaw( $sql );

        if ( $d_po->count() == 0 ) {
            $m_po = new \Model\Storage\Po_model();
            $m_po->where('no_po', $no_po)->update(
                array('done' => 1)
            );
        } else {
            $m_po = new \Model\Storage\Po_model();
            $m_po->where('no_po', $no_po)->update(
                array('done' => 0)
            );
        }
    }

    public function tes()
    {
        $m_po = new \Model\Storage\Po_model();
        $no_po = $m_po->getNextNoPo();

        cetak_r( $no_po );
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends Public_Controller {

    private $pathView = 'parameter/item/';
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
                "assets/parameter/item/js/item.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/item/css/item.css",
            ));

            $data = $this->includes;

            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->with(['group', 'satuan'])->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_item;
            $content['title_panel'] = 'Master Item';

            // Load Indexx
            $data['title_menu'] = 'Master Item';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGroupItem()
    {
        $m_gi = new \Model\Storage\GroupItem_model();
        $d_gi = $m_gi->orderBy('nama', 'desc')->get();

        $data = null;
        if ( $d_gi->count() > 0 ) {
            $data = $d_gi->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['group'] = $this->getGroupItem();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_item = new \Model\Storage\Item_model();
            $d_item_kode = $m_item->where('kode', $params['kode'])->first();

            if ( !$d_item_kode ) {
                $kode = $m_item->getNextId();

                $m_item->kode = $kode;
                $m_item->nama = $params['nama'];
                $m_item->brand = $params['brand'];
                $m_item->group_kode = $params['group'];
                $m_item->keterangan = $params['keterangan'];
                $m_item->kode_text = $params['kode'];
                $m_item->save();

                foreach ($params['satuan'] as $k_satuan => $v_satuan) {
                    $m_is = new \Model\Storage\ItemSatuan_model();
                    $m_is->item_kode = $params['kode'];
                    $m_is->satuan = $v_satuan['satuan'];
                    $m_is->pengali = $v_satuan['pengali'];
                    $m_is->save();
                }

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_item, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                $this->result['message'] = 'Kode yang anda input sudah ada di data item.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->where('kode', $kode)->with(['satuan'])->first()->toArray();

        $content['data'] = $d_item;
        $content['group'] = $this->getGroupItem();

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_item = new \Model\Storage\Item_model();
            $m_item->where('kode', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'brand' => $params['brand'],
                    'group_kode' => $params['group'],
                    'keterangan' => $params['keterangan']
                )
            );

            $m_is = new \Model\Storage\ItemSatuan_model();
            $m_is->where('item_kode', $params['kode'])->delete();

            foreach ($params['satuan'] as $k_satuan => $v_satuan) {
                $m_is = new \Model\Storage\ItemSatuan_model();
                $m_is->item_kode = $params['kode'];
                $m_is->satuan = $v_satuan['satuan'];
                $m_is->pengali = $v_satuan['pengali'];
                $m_is->save();
            }

            $d_item = $m_item->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_item, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $kode = $this->input->post('kode');

        try {
            $m_item = new \Model\Storage\Item_model();
            $d_item = $m_item->where('kode', $kode)->first();

            $m_item->where('kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_item, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
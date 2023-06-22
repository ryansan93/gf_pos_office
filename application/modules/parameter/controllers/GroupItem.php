<?php defined('BASEPATH') OR exit('No direct script access allowed');

class GroupItem extends Public_Controller {

    private $pathView = 'parameter/group_item/';
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
                "assets/parameter/group_item/js/group-item.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/group_item/css/group-item.css",
            ));

            $data = $this->includes;

            $m_gi = new \Model\Storage\GroupItem_model();
            $d_gi = $m_gi->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_gi;
            $content['title_panel'] = 'Master Group Item';

            // Load Indexx
            $data['title_menu'] = 'Master Group Item';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function modalAddForm()
    {
        $content = null;

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_gi = new \Model\Storage\GroupItem_model();
            $d_gi = $m_gi->where('kode', $params['kode'])->first();

            if ( !$d_gi ) {
                $m_gi->kode = $params['kode'];
                $m_gi->nama = $params['nama'];
                $m_gi->coa = $params['coa'];
                $m_gi->ket_coa = $params['ket_coa'];
                $m_gi->mstatus = 1;
                $m_gi->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_gi, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                $this->result['message'] = 'Kode sudah di ada, harap cek kembali kode yang anda inputkan.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_gi = new \Model\Storage\GroupItem_model();
        $d_gi = $m_gi->where('kode', $kode)->first()->toArray();

        $content['data'] = $d_gi;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_gi = new \Model\Storage\GroupItem_model();
            $m_gi->where('kode', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'coa' => $params['coa'],
                    'ket_coa' => $params['ket_coa'],
                    'mstatus' => 1
                )
            );

            $d_gi = $m_gi->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_gi, $deskripsi_log );

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
            $m_gi = new \Model\Storage\GroupItem_model();
            $d_gi = $m_gi->where('kode', $kode)->first();

            // $m_gi->where('kode', $kode)->delete();
            $m_gi->where('kode', $kode)->update(
                array(
                    'mstatus' => 0
                )
            );

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_gi, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
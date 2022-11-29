<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends Public_Controller {

    private $pathView = 'parameter/supplier/';
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
                "assets/parameter/supplier/js/supplier.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/supplier/css/supplier.css",
            ));

            $data = $this->includes;

            $m_supplier = new \Model\Storage\Supplier_model();
            $d_supplier = $m_supplier->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_supplier;
            $content['title_panel'] = 'Master Supplier';

            // Load Indexx
            $data['title_menu'] = 'Master Supplier';
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
            $m_supl = new \Model\Storage\Supplier_model();

            $kode = $m_supl->getNextId();

            $m_supl->kode = $kode;
            $m_supl->nama = $params['nama'];
            $m_supl->alamat = $params['alamat'];
            $m_supl->npwp = $params['npwp'];
            $m_supl->penanggung_jawab = $params['penanggung_jawab'];
            $m_supl->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_supl, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_supl = new \Model\Storage\Supplier_model();
        $d_supl = $m_supl->where('kode', $kode)->first()->toArray();

        $content['data'] = $d_supl;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_supl = new \Model\Storage\Supplier_model();
            $m_supl->where('kode', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'alamat' => $params['alamat'],
                    'npwp' => $params['npwp'],
                    'penanggung_jawab' => $params['penanggung_jawab']
                )
            );

            $d_supl = $m_supl->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_supl, $deskripsi_log );

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
            $m_supl = new \Model\Storage\Supplier_model();
            $d_supl = $m_supl->where('kode', $kode)->first();

            $m_supl->where('kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_supl, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
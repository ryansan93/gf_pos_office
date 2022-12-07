<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang extends Public_Controller {

    private $pathView = 'parameter/gudang/';
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
                "assets/parameter/gudang/js/gudang.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/gudang/css/gudang.css",
            ));

            $data = $this->includes;

            $m_gudang = new \Model\Storage\Gudang_model();
            $d_gudang = $m_gudang->orderBy('kode_gudang', 'asc')->with(['branch'])->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_gudang;
            $content['title_panel'] = 'Master Gudang';

            // Load Indexx
            $data['title_menu'] = 'Master Gudang';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_gudang = new \Model\Storage\Branch_model();
        $d_gudang = $m_gudang->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gudang->count() > 0 ) {
            $data = $d_gudang->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['branch'] = $this->getBranch();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_gudang = new \Model\Storage\Gudang_model();
            $d_gudang_kode = $m_gudang->where('kode_gudang', $params['kode'])->first();

            if ( !$d_gudang_kode ) {
                $m_gudang->kode_gudang = $params['kode'];
                $m_gudang->nama = $params['nama'];
                $m_gudang->branch_kode = $params['branch_kode'];
                $m_gudang->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_gudang, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                $this->result['message'] = 'Kode gudang sudah terpakai, harap cek kembali Kode gudang.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->where('kode_gudang', $kode)->with(['branch'])->first()->toArray();

        $content['branch'] = $this->getBranch();
        $content['data'] = $d_gudang;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_gudang = new \Model\Storage\Gudang_model();
            $m_gudang->where('kode_gudang', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'branch_kode' => $params['branch_kode']
                )
            );

            $d_gudang = $m_gudang->where('kode_gudang', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_gudang, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $kode_gudang = $this->input->post('kode_gudang');

        try {
            $m_gudang = new \Model\Storage\Gudang_model();
            $d_gudang = $m_gudang->where('kode_gudang', $kode_gudang)->first();

            $m_gudang->where('kode_gudang', $kode_gudang)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_gudang, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
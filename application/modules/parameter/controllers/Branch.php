<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Branch extends Public_Controller {

    private $pathView = 'parameter/Branch/';
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
                "assets/parameter/branch/js/branch.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/branch/css/branch.css",
            ));

            $data = $this->includes;

            $m_branch = new \Model\Storage\Branch_model();
            $d_branch = $m_branch->orderBy('kode_branch', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_branch;
            $content['title_panel'] = 'Master Branch';

            // Load Indexx
            $data['title_menu'] = 'Master Branch';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function modalAddForm()
    {
        $html = $this->load->view($this->pathView . 'addForm', null, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_branch = new \Model\Storage\Branch_model();
            $d_branch_kode = $m_branch->where('kode_branch', $params['kode'])->first();
            $d_branch_pin = $m_branch->where('pin_branch', $params['pin'])->first();

            if (!$d_branch_kode && !$d_branch_pin) {
                $m_branch->kode_branch = $params['kode'];
                $m_branch->nama = $params['nama'];
                $m_branch->alamat = $params['alamat'];
                $m_branch->telp = $params['no_telp'];
                $m_branch->pin_branch = $params['pin'];
                $m_branch->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_branch, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                if ( $d_branch_kode && !$d_branch_pin ) {
                    $this->result['message'] = 'Kode branch sudah terpakai, harap cek kembali Kode branch.';
                } else if ( !$d_branch_kode && $d_branch_pin ) {
                    $this->result['message'] = 'PIN branch sudah terpakai, harap cek kembali PIN branch.';
                } else if ($d_branch_kode && $d_branch_pin) {
                    $this->result['message'] = 'PIN & Kode branch sudah terpakai, harap cek kembali PIN & Kode branch.';
                }
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $kode = $this->input->get('kode');

        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->where('kode_branch', $kode)->first()->toArray();

        $content['data'] = $d_branch;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_branch = new \Model\Storage\Branch_model();
            $_d_branch_pin = $m_branch->where('pin_branch', $params['pin'])->first();

            $m_branch->where('kode_branch', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'alamat' => $params['alamat'],
                    'telp' => $params['no_telp']
                )
            );

            $d_branch_pin = $m_branch->where('kode_branch', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_branch_pin, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $kode_branch = $this->input->post('kode_branch');

        try {
            $m_branch = new \Model\Storage\Branch_model();
            $d_branch_pin = $m_branch->where('kode_branch', $kode_branch)->first();

            $m_branch->where('kode_branch', $kode_branch)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_branch_pin, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
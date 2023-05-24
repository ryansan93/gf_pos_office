<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends Public_Controller {

    private $pathView = 'parameter/Shift/';
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
                "assets/parameter/shift/js/shift.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/shift/css/shift.css",
            ));

            $data = $this->includes;

            $m_shift = new \Model\Storage\Shift_model();
            $d_shift = $m_shift->orderBy('nama', 'asc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_shift;
            $content['title_panel'] = 'Master Shift';

            // Load Indexx
            $data['title_menu'] = 'Master Shift';
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
            $m_shift = new \Model\Storage\Shift_model();
            $m_shift->nama = $params['shift'];
            $m_shift->start_time = $params['start'];
            $m_shift->end_time = $params['end'];
            $m_shift->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_shift, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $id = $this->input->get('id');

        $m_shift = new \Model\Storage\Shift_model();
        $d_shift = $m_shift->where('id', $id)->first()->toArray();

        $content['data'] = $d_shift;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_shift = new \Model\Storage\Shift_model();
            $m_shift->where('id', $params['id'])->update(
                array(
                    'nama' => $params['shift'],
                    'start_time' => $params['start'],
                    'end_time' => $params['end']
                )
            );

            $d_shift = $m_shift->where('id', $params['id'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_shift, $deskripsi_log );

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
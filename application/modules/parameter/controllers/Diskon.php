<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Diskon extends Public_Controller {

    private $pathView = 'parameter/Diskon/';
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
                "assets/parameter/diskon/js/diskon.js",
            ));
            $this->add_external_css(array(
                "assets/parameter/diskon/css/diskon.css",
            ));

            $data = $this->includes;

            $m_diskon = new \Model\Storage\Diskon_model();
            $d_diskon = $m_diskon->orderBy('start_date', 'desc')->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_diskon;
            $content['title_panel'] = 'Master Diskon';

            // Load Indexx
            $data['title_menu'] = 'Master Diskon';
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
            $m_diskon = new \Model\Storage\Diskon_model();

            $kode = $m_diskon->getNextId();

            $m_diskon->kode = $kode;
            $m_diskon->nama = $params['nama'];
            $m_diskon->deskripsi = $params['deskripsi'];
            $m_diskon->start_date = $params['tgl_mulai'];
            $m_diskon->end_date = $params['tgl_akhir'];
            $m_diskon->level = $params['level'];
            $m_diskon->save();

            $m_diskond = new \Model\Storage\DiskonDet_model();
            $m_diskond->diskon_kode = $kode;
            $m_diskond->persen = $params['persen'];
            $m_diskond->nilai = $params['nilai'];
            $m_diskond->non_member = $params['non_member'];
            $m_diskond->member = $params['member'];
            $m_diskond->min_beli = $params['min_beli'];
            $m_diskond->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_diskon, $deskripsi_log );

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

        $m_diskon = new \Model\Storage\Diskon_model();
        $d_diskon = $m_diskon->where('kode', $kode)->with(['detail'])->first()->toArray();

        $content['data'] = $d_diskon;

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_diskon = new \Model\Storage\Diskon_model();
            $m_diskon->where('kode', $params['kode'])->update(
                array(
                    'nama' => $params['nama'],
                    'deskripsi' => $params['deskripsi'],
                    'start_date' => $params['tgl_mulai'],
                    'end_date' => $params['tgl_akhir'],
                    'level' => $params['level']
                )
            );

            $m_diskond = new \Model\Storage\DiskonDet_model();
            $m_diskond->where('diskon_kode', $params['kode'])->update(
                array(
                    'persen' => $params['persen'],
                    'nilai' => $params['nilai'],
                    'non_member' => $params['non_member'],
                    'member' => $params['member'],
                    'min_beli' => $params['min_beli']
                )
            );

            $d_diskon = $m_diskon->where('kode', $params['kode'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_diskon, $deskripsi_log );

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
            $m_diskon = new \Model\Storage\Diskon_model();
            $d_kode = $m_diskon->where('kode', $kode)->first();

            $m_diskond = new \Model\Storage\DiskonDet_model();
            $m_diskon->where('kode', $kode)->delete();
            $m_diskond->where('diskon_kode', $kode)->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_kode, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
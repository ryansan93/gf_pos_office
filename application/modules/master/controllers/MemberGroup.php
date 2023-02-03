<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MemberGroup extends Public_Controller {

    private $pathView = 'master/member_group/';
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
                "assets/master/member_group/js/member-group.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/master/member_group/css/member-group.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['data'] = $this->getData();
            $content['title_panel'] = 'Master Member Group';

            // Load Indexx
            $data['title_menu'] = 'Master Member Group';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getData()
    {
        $m_member_group = new \Model\Storage\MemberGroup_model();
        $now = $m_member_group->getDate();

        $d_member_group = $m_member_group->where('status', 1)->orderBy('nama', 'desc')->get();

        $data = null;
        if ( $d_member_group->count() > 0 ) {
            $data = $d_member_group->toArray();
        }

        return $data;
    }

    public function addForm()
    {
        $content = null;

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function viewForm()
    {
        $id = $this->input->get('kode');

        $m_mg = new \Model\Storage\MemberGroup_model();
        $now = $m_mg->getDate();

        $d_mg = $m_mg->where('id', $id)->first();

        $data = null;
        if ( $d_mg ) {
            $data = $d_mg->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();
            $now = $m_mg->getDate();

            $m_mg->nama = $params['nama'];
            $m_mg->status = 1;
            $m_mg->save();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();

            $id = $params['kode'];

            $m_mg->where('id', $id)->update(
                array(
                    'nama' => $params['nama']
                )
            );

            $d_member = $m_mg->where('id', $id)->first()->toArray();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');
        try {
            $m_mg = new \Model\Storage\MemberGroup_model();

            $id = $params['kode'];

            $m_mg->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_member = $m_mg->where('id', $id)->first()->toArray();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_mg, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member group berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
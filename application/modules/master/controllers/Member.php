<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Public_Controller {

    private $pathView = 'master/Member/';
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
                "assets/master/member/js/member.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/master/member/css/member.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['data'] = $this->getDataLists();
            $content['title_panel'] = 'Master Member';

            // Load Indexx
            $data['title_menu'] = 'Master Member';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getDataLists()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select mbr.*, mg.nama as nama_grup, GETDATE() as tanggal from member mbr
            left join
                member_group mg
                on
                    mbr.member_group_id = mg.id
            order by
                mbr.tgl_berakhir asc,
                mbr.nama asc
        ";
        $d_mbr = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_mbr->count() > 0 ) {
            $data = $d_mbr->toArray();
        }

        return $data;
    }

    public function getDataMemberGroup()
    {
        $m_member_group = new \Model\Storage\MemberGroup_model();
        $d_member_group = $m_member_group->where('status', 1)->orderBy('nama', 'desc')->get();

        $data = null;
        if ( $d_member_group->count() > 0 ) {
            $data = $d_member_group->toArray();
        }

        return $data;
    }

    public function addForm()
    {
        $content['member_group'] = $this->getDataMemberGroup();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function viewForm()
    {
        $kode = $this->input->get('kode');

        $m_member = new \Model\Storage\Member_model();
        $now = $m_member->getDate();

        $d_member = $m_member->where('kode_member', $kode)->first();

        $data = null;
        if ( $d_member ) {
            $data = $d_member->toArray();
        }

        $content['akses'] = $this->hakAkses;
        $content['tanggal'] = $now['tanggal'];
        $content['member_group'] = $this->getDataMemberGroup();
        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $m_member->getNextId();

            $m_member->kode_member = $kode_member;
            $m_member->nama = $params['nama'];
            $m_member->no_telp = $params['no_telp'];
            $m_member->alamat = $params['alamat'];
            $m_member->privilege = 0;
            $m_member->status = 1;
            $m_member->tgl_berakhir = prev_date(date('Y-m-d', strtotime($now['tanggal']. ' + 1 years')));
            $m_member->mstatus = 1;
            $m_member->member_group_id = $params['member_group_id'];
            $m_member->save();

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'nama' => $params['nama'],
                    'no_telp' => $params['no_telp'],
                    'alamat' => $params['alamat'],
                    'privilege' => $params['privilege'],
                    'member_group_id' => $params['member_group_id']
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'status' => 0
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function aktif()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'mstatus' => 1,
                    'tgl_berakhir' => prev_date(date('Y-m-d', strtotime($now['tanggal']. ' + 1 years')))
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-aktifkan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di aktifkan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function nonAktif()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();
            $now = $m_member->getDate();

            $kode_member = $params['kode'];

            $m_member->where('kode_member', $kode_member)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-nonaktifkan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_member, $deskripsi_log, $kode_member );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di nonaktifkan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
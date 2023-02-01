<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meja extends Public_Controller {

    private $pathView = 'parameter/Meja/';
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
                "assets/parameter/meja/js/meja.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/meja/css/meja.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['data'] = $this->getDataLists();
            $content['title_panel'] = 'Master Layout Meja';

            // Load Indexx
            $data['title_menu'] = 'Master Layout Meja';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getDataLists()
    {
        $m_lantai = new \Model\Storage\Lantai_model();
        $d_lantai = $m_lantai->orderBy('branch_kode', 'asc')->get();

        $data = null;
        if ( $d_lantai->count() > 0 ) {
            $d_lantai = $d_lantai->toArray();

            foreach ($d_lantai as $_lt => $v_lt) {
                $m_branch = new \Model\Storage\Branch_model();
                $d_branch = $m_branch->where('kode_branch', $v_lt['branch_kode'])->first();

                if ( !isset($data[ $v_lt['branch_kode'] ]) ) {
                    $data[ $v_lt['branch_kode'] ] = array(
                        'kode' => $v_lt['branch_kode'],
                        'nama' => $d_branch->nama,
                        'lantai' => null
                    );
                }

                $data[ $v_lt['branch_kode'] ]['lantai'][ $v_lt['id'] ] = array(
                    'kode' => $v_lt['id'],
                    'nama' => $v_lt['nama_lantai'],
                    'kontrol_meja' => $v_lt['kontrol_meja'],
                    'status' => $v_lt['mstatus'],
                );

                $m_meja = new \Model\Storage\Meja_model();
                $d_meja = $m_meja->where('lantai_id', $v_lt['id'])->orderBy('nama_meja', 'asc')->get();

                if ( $d_meja->count() > 0 ) {
                    $d_meja = $d_meja->toArray();

                    foreach ($d_meja as $k_m => $v_m) {
                        $key_meja = $v_m['id'].' | '.$v_m['nama_meja'];
                        $data[ $v_lt['branch_kode'] ]['lantai'][ $v_lt['id'] ]['meja'][ $v_m['id'] ] = array(
                            'kode' => $v_m['id'],
                            'nama' => $v_m['nama_meja']
                        );

                        ksort($data[ $v_lt['branch_kode'] ]['lantai'][ $v_lt['id'] ]['meja']);
                    }
                }
            }
        }

        return $data;
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->get();

        $data = null;
        if ( $d_branch->count() ) {
            $data = $d_branch->toArray();
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
            $m_lantai = new \Model\Storage\Lantai_model();
            $m_lantai->nama_lantai = $params['lantai'];
            $m_lantai->branch_kode = $params['kode_branch'];
            $m_lantai->mstatus = 1;
            $m_lantai->kontrol_meja = $params['kontrol_meja'];
            $m_lantai->save();

            $id = $m_lantai->id;

            foreach ($params['meja'] as $k_meja => $v_meja) {
                $m_meja = new \Model\Storage\Meja_model();
                $m_meja->lantai_id = $id;
                $m_meja->nama_meja = $v_meja;
                $m_meja->save();
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_lantai, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $id = $params['id'];

            $m_lantai = new \Model\Storage\Lantai_model();
            $m_lantai->where('id', $id)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_lantai = $m_lantai->where('id', $id)->first();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_lantai, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di non aktif kan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function aktif()
    {
        $params = $this->input->post('params');

        try {
            $id = $params['id'];

            $m_lantai = new \Model\Storage\Lantai_model();
            $m_lantai->where('id', $id)->update(
                array(
                    'mstatus' => 1
                )
            );

            $d_lantai = $m_lantai->where('id', $id)->first();

            $deskripsi_log = 'di-aktif kan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/aktif', $d_lantai, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di aktif kan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
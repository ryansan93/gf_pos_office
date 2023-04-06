<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PinOtorisasi extends Public_Controller {

    private $pathView = 'parameter/pin_otorisasi/';
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
                "assets/select2/js/select2.min.js",
                "assets/parameter/pin_otorisasi/js/pin-otorisasi.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/pin_otorisasi/css/pin-otorisasi.css",
            ));

            $data = $this->includes;

            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po = $m_po->with(['user', 'det_fitur'])->get()->toArray();

            $content['akses'] = $this->hakAkses;
            $content['data'] = $d_po;
            $content['title_panel'] = 'Master Pin Otorisasi';

            // Load Indexx
            $data['title_menu'] = 'Master Pin Otorisasi';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getUser()
    {
        $m_user = new \Model\Storage\User_model();
        $d_user = $m_user->with(['detail_user'])->get();

        $data = null;
        if ( $d_user->count() > 0 ) {
            $d_user = $d_user->toArray();
            foreach ($d_user as $k_user => $v_user) {
                $key = $v_user['detail_user']['nama_detuser'].' | '.$v_user['id_user'];
                $data[ $key ] = $v_user;
            }
        }

        if ( !empty($data) ) {
            ksort($data);
        }

        return $data;
    }

    public function getFitur()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select df.*, mf.nama_fitur from detail_fitur df
            left join
                ms_fitur mf
                on
                    df.id_fitur = mf.id_fitur
            where
            mf.status = 1
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['user'] = $this->getUser();
        $content['fitur'] = $this->getFitur();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po_pin = $m_po->where('pin', $params['pin'])->first();

            if (!$d_po_pin) {
                $m_po->user_id = $params['kode'];
                $m_po->pin = $params['pin'];
                $m_po->status = 1;
                $m_po->id_detfitur = $params['id_detfitur'];
                $m_po->save();

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_po, $deskripsi_log );

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di simpan.';
            } else {
                $this->result['message'] = 'PIN sudah terpakai, harap cek kembali PIN Otorisasi.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalEditForm()
    {
        $id = $this->input->get('id');

        $m_po = new \Model\Storage\PinOtorisasi_model();
        $d_po = $m_po->where('id', $id)->first()->toArray();

        $content['data'] = $d_po;
        $content['user'] = $this->getUser();
        $content['fitur'] = $this->getFitur();

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po_pin = $m_po->where('pin', $params['pin'])->first();

            $m_po->where('id', $params['id'])->update(
                array(
                    'user_id' => $params['kode'],
                    'pin' => $params['pin'],
                    'status' => 1,
                    'id_detfitur' => $params['id_detfitur']
                )
            );

            $d_po = $m_po->where('id', $params['id'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_po, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di edit.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id = $this->input->post('id');

        try {
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $m_po->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_po = $m_po->where('id', $id)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_po, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
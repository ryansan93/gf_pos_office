<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Printer extends Public_Controller {

    private $pathView = 'utility/printer/';
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
    public function index()
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    'assets/utility/printer/js/printer.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/utility/printer/css/printer.css'
                )
            );
            $data = $this->includes;

            $content['akses'] = $this->hakAkses;

            $content['data'] = $this->getLists();

            $data['title_menu'] = 'Setting Printer';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getLists()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select p.*, b.nama as nama_branch, ps.nama as nama_station from printer p
            right join
                branch b
                on
                    p.branch_kode = b.kode_branch
            right join
                printer_station ps
                on
                    p.printer_station_id = ps.id
            where
                p.status = 1
        ";
        $d_printer = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_printer->count() > 0 ) {
            $d_printer = $d_printer->toArray();

            foreach ($d_printer as $k_p => $v_p) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select pkm.*, km.nama from printer_kategori_menu pkm
                    right join
                        kategori_menu km
                        on
                            pkm.kategori_menu_id = km.id
                    where
                        pkm.id_header = '".$v_p['id']."'
                ";
                $d_pkm = $m_conf->hydrateRaw( $sql );
                if ( $d_pkm->count() > 0 ) {
                    $d_pkm = $d_pkm->toArray();
                }

                $data[] = array(
                    'id' => $v_p['id'],
                    'branch' => $v_p['nama_branch'],
                    'sharing_name' => $v_p['sharing_name'],
                    'lokasi' => $v_p['lokasi'],
                    'status' => $v_p['status'],
                    'nama_station' => $v_p['nama_station'],
                    'jml_print' => $v_p['jml_print'],
                    'kategori_menu' => $d_pkm
                );
            }
        }

        return $data;
    }

    public function branch()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select b.* from branch b order by b.nama asc
        ";
        $d_branch = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function printerStation()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select ps.* from printer_station ps order by ps.nama asc
        ";
        $d_ps = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_ps->count() > 0 ) {
            $data = $d_ps->toArray();
        }

        return $data;
    }

    public function kategoriMenu()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select km.* from kategori_menu km order by km.nama asc
        ";
        $d_km = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_km->count() > 0 ) {
            $data = $d_km->toArray();
        }

        return $data;
    }

    public function modalAddForm()
    {
        $content['branch'] = $this->branch();
        $content['printer_station'] = $this->printerStation();
        $content['kategori_menu'] = $this->kategoriMenu();
        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        echo $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        try {
            $m_printer = new \Model\Storage\Printer_model();
            $m_printer->branch_kode = $params['branch_kode'];
            $m_printer->printer_station_id = $params['printer_station'];
            $m_printer->sharing_name = $params['sharing_name'];
            $m_printer->lokasi = $params['lokasi'];
            $m_printer->status = 1;
            $m_printer->jml_print = $params['jml_print'];
            $m_printer->save();

            if ( isset($params['kategori_menu']) && !empty($params['kategori_menu']) ) {
                foreach ($params['kategori_menu'] as $k_km => $v_km) {
                    $m_pkm = new \Model\Storage\PrinterKategoriMenu_model();
                    $m_pkm->id_header = $m_printer->id;
                    $m_pkm->kategori_menu_id = $v_km;
                    $m_pkm->save();
                }
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_printer, $deskripsi_log );

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

        $data = null;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select p.* from printer p
            where
                p.id = ".$id."
        ";
        $d_printer = $m_conf->hydrateRaw( $sql );
        if ( $d_printer->count() > 0 ) {
            $d_printer = $d_printer->toArray();

            $data = $d_printer[0];
            $data['kategori_menu'] = null;

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select pkm.* from printer_kategori_menu pkm
                where
                    pkm.id_header = ".$id."
            ";
            $d_pkm = $m_conf->hydrateRaw( $sql );
            if ( $d_pkm->count() > 0 ) {
                $d_pkm = $d_pkm->toArray();

                foreach ($d_pkm as $k_pkm => $v_pkm) {
                    $data['kategori_menu'][] = $v_pkm['kategori_menu_id'];
                }
            }
        }

        $content['data'] = $data;
        $content['branch'] = $this->branch();
        $content['printer_station'] = $this->printerStation();
        $content['kategori_menu'] = $this->kategoriMenu();
        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        echo $html;
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_printer = new \Model\Storage\Printer_model();
            $m_printer->where('id', $params['id'])->update(
                array(
                    'branch_kode' => $params['branch_kode'],
                    'printer_station_id' => $params['printer_station'],
                    'sharing_name' => $params['sharing_name'],
                    'lokasi' => $params['lokasi'],
                    'jml_print' => $params['jml_print']
                )
            );

            if ( isset($params['kategori_menu']) && !empty($params['kategori_menu']) ) {
                $m_pkm = new \Model\Storage\PrinterKategoriMenu_model();
                $m_pkm->where('id_header', $params['id'])->delete();

                foreach ($params['kategori_menu'] as $k_km => $v_km) {
                    $m_pkm = new \Model\Storage\PrinterKategoriMenu_model();
                    $m_pkm->id_header = $params['id'];
                    $m_pkm->kategori_menu_id = $v_km;
                    $m_pkm->save();
                }
            }

            $d_printer = $m_printer->where('id', $params['id'])->first();

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_printer, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di ubah.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $id = $this->input->post('id');

        try {
            $m_printer = new \Model\Storage\Printer_model();
            $m_printer->where('id', $id)->update(
                array(
                    'status' => 0
                )
            );

            $d_printer = $m_printer->where('id', $id)->first();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $m_printer, $deskripsi_log );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
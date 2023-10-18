<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ClosingOrder extends Public_Controller {

    private $pathView = 'transaksi/closing_order/';
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
                "assets/jquery/list.min.js",
                "assets/transaksi/closing_order/js/closing-order.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/closing_order/css/closing-order.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['branch'] = $this->getBranch();
            $content['title_panel'] = 'Closing Order';

            // Load Indexx
            $data['title_menu'] = 'Closing Order';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch() {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select * from branch order by nama asc
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function getLists() {
        $params = $this->input->get('params');

        $tanggal = $params['tanggal'];
        $branch = $params['branch'];

        $start_date = $tanggal.' 00:00:00.001';
        $end_date = $tanggal.' 23:59:59.999';

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                co.kode,
                co.tanggal,
                co.user_id,
                du.nama_detuser as nama_user,
                co.branch_kode,
                b.nama as nama_branch
            from closing_order co
            left join
                (
                    select du1.* from detail_user du1
                    right join
                        (select max(id_detuser) as id_detuser, id_user from detail_user group by id_user) du2
                        on
                            du1.id_detuser = du2.id_detuser
                ) du
                on
                    du.id_user = co.user_id
            left join
                branch b
                on
                    co.branch_kode = b.kode_branch
            where
                co.tanggal between '".$start_date."' and '".$end_date."' and
                co.branch_kode in ('".implode("', '", $branch)."')
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }

    public function delete() {
        $params = $this->input->post('params');

        try {
            $kode = $params['kode'];

            $m_co = new \Model\Storage\ClosingOrder_model();
            $d_co = $m_co->where('kode', $kode)->first();

            $m_com = new \Model\Storage\ClosingOrderMenu_model();
            $m_com->where('closing_order_kode', $kode)->delete();
            $m_co->where('kode', $kode)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_co, $deskripsi_log, $kode );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ProdukTerlaris extends Public_Controller {

    private $pathView = 'report/produk_terlaris/';
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
                    'assets/report/produk_terlaris/js/produk-terlaris.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/produk_terlaris/css/produk-terlaris.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Produk Terlaris';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getData($start_date, $end_date)
    {
        $data = null;

        $m_pi = new \Model\Storage\PesananItem_model();
        $sql = "
            select * from 
            (
                select 
                    pi.menu_kode,
                    pi.menu_nama,
                    km.nama as kategori,
                    jm.nama as jenis,
                    sum(pi.jumlah) as qty,
                    sum(pi.total) as total
                from pesanan_item pi
                right join
                    pesanan p
                    on
                        pi.pesanan_kode = p.kode_pesanan
                right join
                    menu m
                    on
                        pi.menu_kode = m.kode_menu
                left join
                    kategori_menu km
                    on
                        m.kategori_menu_id = km.id
                right join
                    jenis_menu jm
                    on
                        m.jenis_menu_id = jm.id
                where
                    p.mstatus = 1 and
                    p.tgl_pesan between '".$start_date."' and '".$end_date."'
                group by
                    pi.menu_kode,
                    pi.menu_nama,
                    km.nama,
                    jm.nama
            ) data
            order by
                qty desc
        ";
        $d_pi = $m_pi->hydrateRaw( $sql );

        if ( $d_pi->count() > 0 ) {
            $data = $d_pi->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];

        $data = $this->getData( $start_date, $end_date );

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }
}
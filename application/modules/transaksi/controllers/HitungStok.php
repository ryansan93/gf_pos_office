<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HitungStok extends Public_Controller {

    private $url;

    function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index($segment=0)
    {
        $akses = hakAkses($this->url);
        if ( $akses['a_view'] == 1 ) {
            $this->add_external_js(array(
                "assets/transaksi/hitung_stok/js/hitung-stok.js",
            ));
            $this->add_external_css(array(
                "assets/transaksi/hitung_stok/css/hitung-stok.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;

            // Load Indexx
            $data['title_menu'] = 'Hitung Stok';
            $data['view'] = $this->load->view('transaksi/hitung_stok/index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('kode_branch', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function hitungStok()
    {
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        $target = $this->input->post('target');

        try {
            $_target = date('Y-m-t', strtotime($target));
            $tgl_proses = $_target;
            
            $startDate = substr($startDate, 0, 7).'-01';
            $_endDate = date('Y-m-t', strtotime($startDate));

            $stok_voadip = $this->hitungStokBarang( $startDate, $_endDate );

            $lanjut = 0;

            if ( substr($startDate, 0, 6) <= substr($endDate, 0, 6) ) {
                $lanjut = 1;
            }

            $new_start_date = date("Y-m-d", strtotime ( '+1 month' , strtotime ( $startDate ) ));

            $params = array(
                'start_date' => $new_start_date,
                'end_date' => $endDate,
                'target' => $_target,
                'text_target' => strtoupper(substr(tglIndonesia($new_start_date, '-', ' '), 3)),
            );
            
            $this->result['lanjut'] = $lanjut;
            $this->result['params'] = $params;
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di proses';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungStokBarang($startDate, $endDate)
    {
        $_startDate = $startDate;

        $conf = new \Model\Storage\Conf();
        $now = $conf->getDate();

        $branch = $this->getBranch();

        while ($startDate <= $endDate) {
            if ( $startDate >= '2022-10-13' && $startDate <= $now['tanggal'] ) {
                // foreach ($branch as $k_branch => $v_branch) {
                //     $kode_branch = $v_branch['kode_branch'];

                $conf = new \Model\Storage\Conf();
                $sql = "EXEC hitung_stok '$startDate'";

                $d_conf = $conf->hydrateRaw($sql);

                // cetak_r( $d_conf );
                // }
            }

            $startDate = next_date( $startDate );
        }
    }

    public function tes()
    { }
}
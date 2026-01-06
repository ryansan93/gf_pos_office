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
                "assets/select2/js/select2.min.js",
                "assets/transaksi/hitung_stok/js/hitung-stok.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/hitung_stok/css/hitung-stok.css",
            ));

            $data = $this->includes;

            $content['akses'] = $akses;
            $content['gudang'] = $this->getGudang();
            $content['item'] = $this->getItem();

            // Load Indexx
            $data['title_menu'] = 'Hitung Stok';
            $data['view'] = $this->load->view('transaksi/hitung_stok/index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGudang()
    {
        $m_gudang = new \Model\Storage\Gudang_model();
        $d_gudang = $m_gudang->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gudang->count() > 0 ) {
            $data = $d_gudang->toArray();
        }

        return $data;
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['satuan'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function hitungStok() {
        $params = $this->input->post('params');
        
        try {
            $m_conf = new \Model\Storage\Conf();
            $sql = "EXEC sp_hitung_stok_by_barang @barang = '".$params['item']."', @tgl_transaksi = '".$params['tanggal']."', @gudang = '".$params['gudang']."'";
            cetak_r( $sql, 1 );

            $d_conf = $m_conf->hydrateRaw($sql);

            $this->result['status'] = 1;
            $this->result['message'] = 'Hitung stok berhasil, cek data pada laporan stok.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    // public function hitungStok()
    // {
    //     $startDate = $this->input->post('startDate');
    //     $endDate = $this->input->post('endDate');
    //     $target = $this->input->post('target');

    //     try {
    //         $_target = date('Y-m-t', strtotime($target));
    //         $tgl_proses = $_target;
            
    //         $startDate = substr($startDate, 0, 7).'-01';
    //         $_endDate = date('Y-m-t', strtotime($startDate));

    //         $stok_voadip = $this->hitungStokBarang( $startDate, $_endDate );

    //         $lanjut = 0;

    //         if ( substr($startDate, 0, 6) <= substr($endDate, 0, 6) ) {
    //             $lanjut = 1;
    //         }

    //         $new_start_date = date("Y-m-d", strtotime ( '+1 month' , strtotime ( $startDate ) ));

    //         $params = array(
    //             'start_date' => $new_start_date,
    //             'end_date' => $endDate,
    //             'target' => $_target,
    //             'text_target' => strtoupper(substr(tglIndonesia($new_start_date, '-', ' '), 3)),
    //         );
            
    //         $this->result['lanjut'] = $lanjut;
    //         $this->result['params'] = $params;
    //         $this->result['status'] = 1;
    //         $this->result['message'] = 'Data berhasil di proses';
    //     } catch (Exception $e) {
    //         $this->result['message'] = $e->getMessage();
    //     }

    //     display_json( $this->result );
    // }

    // public function hitungStokBarang($startDate, $endDate)
    // {
    //     $_startDate = $startDate;

    //     $conf = new \Model\Storage\Conf();
    //     $now = $conf->getDate();

    //     $branch = $this->getBranch();

    //     while ($startDate <= $endDate) {
    //         if ( $startDate >= '2022-10-13' && $startDate <= $now['tanggal'] ) {
    //             // foreach ($branch as $k_branch => $v_branch) {
    //             //     $kode_branch = $v_branch['kode_branch'];

    //             $conf = new \Model\Storage\Conf();
    //             $sql = "EXEC hitung_stok '$startDate'";

    //             $d_conf = $conf->hydrateRaw($sql);

    //             // cetak_r( $d_conf );
    //             // }
    //         }

    //         $startDate = next_date( $startDate );
    //     }
    // }

    public function tes()
    { }
}
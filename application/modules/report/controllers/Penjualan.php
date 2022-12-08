<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends Public_Controller {

    private $pathView = 'report/penjualan/';
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
                    'assets/report/penjualan/js/penjualan.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/penjualan/css/penjualan.css'
                )
            );
            $data = $this->includes;

            $content['report_harian'] = $this->load->view($this->pathView . 'report_harian', null, TRUE);
            $content['report_harian_produk'] = $this->load->view($this->pathView . 'report_harian_produk', null, TRUE);
            $content['report_by_induk_menu'] = $this->load->view($this->pathView . 'report_by_induk_menu', null, TRUE);
            $content['branch'] = $this->getBranch();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Penjualan';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch()
    {
        $m_branch = new \Model\Storage\Branch_model();
        $d_branch = $m_branch->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $start_date = $params['start_date'].' 00:00:00';
            $end_date = $params['end_date'].' 23:59:59';
            $branch = $params['branch'];

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('branch', $branch)->where('mstatus', 1)->with(['detail', 'bayar'])->orderBy('tgl_trans', 'asc')->get();

            $data = null;
            if ( $d_jual->count() > 0 ) {
                $data = $d_jual->toArray();
            }

            $mappingDataReportHarian = $this->mappingDataReportHarian( $data );
            $mappingDataReportHarianProduk = $this->mappingDataReportHarianProduk( $data );
            // $mappingDataReportByIndukMenu = $this->mappingDataReportByIndukMenu( $data );
            $mappingDataReportDetailPembayaran = $this->mappingDataReportDetailPembayaran( $data );

            $content_report_harian['data'] = $mappingDataReportHarian;
            $html_report_harian = $this->load->view($this->pathView . 'list_report_harian', $content_report_harian, TRUE);

            $content_report_harian_produk['data'] = $mappingDataReportHarianProduk;
            $html_report_harian_produk = $this->load->view($this->pathView . 'list_report_harian_produk', $content_report_harian_produk, TRUE);

            $content_report_detail_pembayaran['data'] = $mappingDataReportDetailPembayaran;
            $html_report_detail_pembayaran = $this->load->view($this->pathView . 'list_report_detail_pembayaran', $content_report_detail_pembayaran, TRUE);

            $list_html = array(
                'list_report_harian' => $html_report_harian,
                'list_report_harian_produk' => $html_report_harian_produk,
                'list_report_detail_pembayaran' => $html_report_detail_pembayaran
            );

            $this->result['status'] = 1;
            $this->result['content'] = $list_html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function mappingDataReportHarian($_data)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));
                $key_faktur = $v_data['kode_faktur'];
                $key_kasir = $v_data['kasir'];
                $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['nama_kasir'] = $v_data['nama_kasir'];
                if ( !isset($data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir']) ) {
                    $data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir'] = $v_data['total'];
                } else {
                    $data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir'] += $v_data['total'];
                }
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['kode_faktur'] = $key_faktur;
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['member'] = $v_data['member'];
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] = $v_data['total'];
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] = $v_data['ppn'];
                $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] = $v_data['grand_total'];

                foreach ($v_data['detail'] as $k_det => $v_det) {
                    $key_menu = $v_det['menu_kode'];
                    $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['kode'] = $v_det['menu_kode'];
                    $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['nama'] = $v_det['menu']['nama'];
                    $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['harga'] = $v_det['harga'];
                    if ( isset($data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah']) ) {
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] += $v_det['jumlah'];
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] += $v_det['total'];
                    } else {
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] = $v_det['jumlah'];
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] = $v_det['total'];
                    }

                    foreach ($v_det['detail'] as $k_di => $v_di) {
                        $key_detail_menu = $v_di['menu_kode'];
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['kode'] = $v_di['menu_kode'];
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['nama'] = $v_di['menu']['nama'];
                        $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['jumlah'] = $v_di['jumlah'];
                    }
                }
            }
        }

        return $data;
    }

    public function mappingDataReportHarianProduk($_data)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $ppn_persen = ($v_data['ppn'] > 0) ? $v_data['total'] / $v_data['ppn'] : 0;

                foreach ($v_data['detail'] as $k_det => $v_det) {
                    $key_kategori = $v_det['menu']['kategori']['id'];
                    $key_menu = $v_det['menu_kode'];
                    $data[ $key_kategori ]['id'] = $key_kategori;
                    $data[ $key_kategori ]['nama'] = $v_det['menu']['kategori']['nama'];

                    if ( !empty($v_det['detail']) ) {
                        foreach ($v_det['detail'] as $k_di => $v_di) {
                            $key_menu .= ' | '.$v_di['menu_kode'];
                        }
                    }

                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['kode'] = $key_menu;
                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['member'] = $v_data['member'];
                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['nama'] = $v_det['menu']['nama'];
                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['harga'] = $v_det['harga'];

                    $ppn_nilai = ($ppn_persen > 0) ? $v_det['total'] * ($ppn_persen / 100) : 0;
                    $grand_total = $ppn_nilai + $v_det['total'];
                    if ( isset($data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah']) ) {
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] += $v_det['jumlah'];
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] += $v_det['total'];
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] += $ppn_nilai;
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] += $grand_total;
                    } else {
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] = $v_det['jumlah'];
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] = $v_det['total'];
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] = $ppn_nilai;
                        $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] = $grand_total;
                    }
                    $data[ $key_kategori ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['detail'] = $v_det['detail'];
                }
            }
        }

        return $data;
    }

    public function mappingDataReportDetailPembayaran($_data)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));
                if ( !isset($data[ $key_tanggal ]) ) {
                    $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                }

                if ( $v_data['lunas'] == 1 ) {
                    foreach ($v_data['bayar'] as $k_byr => $v_byr) {
                        if ( $v_data['mstatus'] == 1 && $v_data['lunas'] == 1 ) {
                            if ( $v_byr['jml_tagihan'] <= $v_byr['jml_bayar'] ) {
                                foreach ($v_byr['bayar_det'] as $k_bayar => $v_bayar) {
                                    if ( stristr($v_bayar['jenis_bayar'], 'tunai') !== false || stristr($v_bayar['jenis_bayar'], 'saldo member') !== false ) {
                                        if ( $v_byr['jml_tagihan'] > 0 ) {
                                            if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]) ) {
                                                $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['nama'] = $v_bayar['jenis_bayar'];
                                                $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] = $v_byr['jml_tagihan'];
                                            } else {
                                                $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] += $v_byr['jml_tagihan'];
                                            }
                                        }
                                    } else {
                                        $key_jb = strtolower($v_bayar['jenis_kartu']['nama']);

                                        if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]) ) {
                                            $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['nama'] = $key_jb;
                                            $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] = $v_byr['jml_tagihan'];
                                        } else {
                                            $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] += $v_byr['jml_tagihan'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $key_jb = 'belum bayar';
                    if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]) ) {
                        $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['nama'] = 'BELUM BAYAR';
                        $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] = $v_data['grand_total'];
                    } else {
                        $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] += $v_data['grand_total'];
                    }
                }

                if ( isset($data[ $key_tanggal ]['jenis_pembayaran']) && !empty($data[ $key_tanggal ]['jenis_pembayaran']) ) {
                    ksort( $data[ $key_tanggal ]['jenis_pembayaran'] );
                }
            }

            if ( !empty($data) ) {
                ksort( $data );
            }
        }

        return $data;
    }
}

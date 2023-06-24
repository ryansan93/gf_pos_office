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
            $content['shift'] = $this->getShift();
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

    public function getShift()
    {
        $m_shift = new \Model\Storage\Shift_model();
        $d_shift = $m_shift->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_shift->count() > 0 ) {
            $data = $d_shift->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $shift = $params['shift'];
            $branch = $params['branch'];
            $start_date = $params['start_date'].' 00:00:00';
            $end_date = $params['end_date'].' 23:59:59';

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    _data.kode_branch,
                    max(_data.tgl_trans) as tgl_trans,
                    _data.kode_faktur,
                    max(_data.mstatus) as mstatus
                from 
                (
                    select j.branch as kode_branch, j.tgl_trans as tgl_trans, j.kode_faktur as kode_faktur, j.mstatus from jual j where mstatus = 1

                    union all

                    select j.branch as kode_branch, j.tgl_trans as tgl_trans, jg.faktur_kode_gabungan as kode_faktur, j.mstatus from jual_gabungan jg
                    right join
                        jual j
                        on
                            jg.faktur_kode = j.kode_faktur
                    where
                        j.mstatus = 1
                ) _data
                where
                    _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                    _data.kode_branch = '".$branch."' and
                    _data.kode_faktur is not null
                group by
                    _data.kode_branch,
                    _data.kode_faktur
            ";
            $d_jual = $m_conf->hydrateRaw( $sql );

            $data = null;
            if ( $d_jual->count() > 0 ) {
                $data = $d_jual->toArray();
            }

            $mappingDataReportHarian = $this->mappingDataReportHarian( $data, $shift );
            $mappingDataReportHarianProduk = $this->mappingDataReportHarianProduk( $data, $shift );
            // $mappingDataReportByIndukMenu = $this->mappingDataReportByIndukMenu( $data );
            $mappingDataReportDetailPembayaran = $this->mappingDataReportDetailPembayaran( $data, $shift );

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

    public function mappingDataReportHarian($_data, $_shift)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_shift as $k_shift => $v_shift) {
                $m_shift = new \Model\Storage\Shift_model();
                $d_shift = $m_shift->where('id', $v_shift)->first()->toArray();

                $nama_shift = $d_shift['nama'];
                $jam_awal = substr($d_shift['start_time'], 0, 5);
                $jam_akhir = substr($d_shift['end_time'], 0, 5);

                foreach ($_data as $k_data => $v_data) {
                    $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                    $jam_transaksi = substr($v_data['tgl_trans'], 11, 5);

                    if ( $jam_transaksi >= $jam_awal && $jam_transaksi < $jam_akhir ) {
                        $key_shift = $v_shift;

                        $data[ $key_shift ]['id'] = $v_shift;
                        $data[ $key_shift ]['nama'] = $nama_shift;

                        $m_conf = new \Model\Storage\Conf();
                        $sql = "
                            select 
                                j.kasir,
                                j.nama_kasir,
                                j.member,
                                case
                                    when jp.exclude = 1 then
                                        ji.total
                                    when jp.include = 1 then
                                        ji.total - ji.service_charge - ji.ppn
                                end as total,
                                ji.service_charge,
                                ji.ppn,
                                ji.harga,
                                ji.menu_kode,
                                m.nama as nama_menu,
                                ji.jumlah,
                                j.mstatus
                            from jual_item ji
                            right join
                                jenis_pesanan jp
                                on
                                    ji.kode_jenis_pesanan = jp.kode
                            right join
                                menu m
                                on
                                    ji.menu_kode = m.kode_menu
                            right join
                                jual j
                                on
                                    ji.faktur_kode = j.kode_faktur
                            where
                                j.kode_faktur = '".$v_data['kode_faktur']."'
                        ";
                        $d_ji = $m_conf->hydrateRaw( $sql );

                        if ( $d_ji->count() > 0 ) {
                            $d_ji = $d_ji->toArray();

                            foreach ($d_ji as $k_ji => $v_ji) {
                                $key_faktur = $v_data['kode_faktur'];
                                $key_kasir = $v_ji['kasir'];
                                $key_menu = $v_ji['menu_kode'];

                                $data[ $key_shift ]['detail'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['nama_kasir'] = $v_ji['nama_kasir'];
                                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['kode_faktur'] = $key_faktur;
                                $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['member'] = $v_ji['member'];

                                if ( isset($data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total']) ) {
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] += $v_ji['total'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] += $v_ji['service_charge'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] += $v_ji['ppn'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] += ($v_ji['total'] + $v_ji['service_charge'] + $v_ji['ppn']);
                                } else {
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] = $v_ji['total'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] = $v_ji['service_charge'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] = $v_ji['ppn'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] = ($v_ji['total'] + $v_ji['service_charge'] + $v_ji['ppn']);
                                }

                                if ( !isset($data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]) ) {
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['kode'] = $v_ji['menu_kode'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['nama'] = $v_ji['nama_menu'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['harga'] = $v_ji['harga'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] = $v_ji['jumlah'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] = $v_ji['total'];
                                } else {
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] += $v_ji['jumlah'];
                                    $data[ $key_shift ]['detail'][ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] += $v_ji['total'];
                                }
                            }
                        }

                        // $d_cek_faktur = 0;
                        // if ( $v_data['mstatus'] == 0 ) {
                        //     $m_conf = new \Model\Storage\Conf();
                        //     $sql = "
                        //         select * from jual_gabungan jg 
                        //         where
                        //             jg.faktur_kode_gabungan = '".$v_data['kode_faktur']."'
                        //     ";
                        //     $d_cek_faktur = $m_conf->hydrateRaw( $sql )->count(); 
                        // } else {
                        //     $d_cek_faktur = 1;
                        // }

                        // if ( $d_cek_faktur > 0 ) {
                        //     $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));
                        //     $key_faktur = $v_data['kode_faktur'];
                        //     $key_kasir = $v_data['kasir'];
                        //     $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['nama_kasir'] = $v_data['nama_kasir'];
                        //     if ( !isset($data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir']) ) {
                        //         $data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir'] = $v_data['total'];
                        //     } else {
                        //         $data[ $key_tanggal ]['kasir'][ $key_kasir ]['total_kasir'] += $v_data['total'];
                        //     }
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['kode_faktur'] = $key_faktur;
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['member'] = $v_data['member'];
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['total'] = $v_data['total'];
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['ppn'] = $v_data['ppn'];
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['service_charge'] = $v_data['service_charge'];
                        //     $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['grand_total'] = $v_data['grand_total'];

                        //     foreach ($v_data['detail'] as $k_det => $v_det) {
                        //         $key_menu = $v_det['menu_kode'];
                        //         $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['kode'] = $v_det['menu_kode'];
                        //         $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['nama'] = $v_det['menu']['nama'];
                        //         $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['harga'] = $v_det['harga'];
                        //         if ( isset($data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah']) ) {
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] += $v_det['jumlah'];
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] += $v_det['total'];
                        //         } else {
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['jumlah'] = $v_det['jumlah'];
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['total'] = $v_det['total'];
                        //         }

                        //         foreach ($v_det['detail'] as $k_di => $v_di) {
                        //             $key_detail_menu = $v_di['menu_kode'];
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['kode'] = $v_di['menu_kode'];
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['nama'] = $v_di['menu']['nama'];
                        //             $data[ $key_tanggal ]['kasir'][ $key_kasir ]['faktur'][ $key_faktur ]['menu'][ $key_menu ]['detail'][ $key_detail_menu ]['jumlah'] = $v_di['jumlah'];
                        //         }
                        //     }
                        // }
                    }
                }
            }
        }

        return $data;
    }

    public function mappingDataReportHarianProduk($_data, $_shift)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_shift as $k_shift => $v_shift) {
                $m_shift = new \Model\Storage\Shift_model();
                $d_shift = $m_shift->where('id', $v_shift)->first()->toArray();

                $nama_shift = $d_shift['nama'];
                $jam_awal = substr($d_shift['start_time'], 0, 5);
                $jam_akhir = substr($d_shift['end_time'], 0, 5);

                foreach ($_data as $k_data => $v_data) {
                    $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                    $jam_transaksi = substr($v_data['tgl_trans'], 11, 5);

                    if ( $jam_transaksi >= $jam_awal && $jam_transaksi < $jam_akhir ) {
                        $key_shift = $v_shift;

                        $data[ $key_shift ]['id'] = $v_shift;
                        $data[ $key_shift ]['nama'] = $nama_shift;

                        $m_conf = new \Model\Storage\Conf();
                        $sql = "
                            select 
                                j.kasir,
                                j.nama_kasir,
                                j.member,
                                case
                                    when jp.exclude = 1 then
                                        ji.total
                                    when jp.include = 1 then
                                        ji.total - ji.service_charge - ji.ppn
                                end as total,
                                ji.service_charge,
                                ji.ppn,
                                ji.harga,
                                ji.menu_kode,
                                m.nama as nama_menu,
                                jm.nama as nama_jenis_menu,
                                jm.id as id_jenis_menu,
                                ji.jumlah,
                                j.mstatus
                            from jual_item ji
                            right join
                                jenis_pesanan jp
                                on
                                    ji.kode_jenis_pesanan = jp.kode
                            right join
                                menu m
                                on
                                    ji.menu_kode = m.kode_menu
                            right join
                                jenis_menu jm
                                on
                                    m.jenis_menu_id = jm.id
                            right join
                                jual j
                                on
                                    ji.faktur_kode = j.kode_faktur
                            where
                                j.kode_faktur = '".$v_data['kode_faktur']."'
                        ";
                        $d_ji = $m_conf->hydrateRaw( $sql );

                        if ( $d_ji->count() > 0 ) {
                            $d_ji = $d_ji->toArray();

                            foreach ($d_ji as $k_ji => $v_ji) {
                                $key_jenis = $v_ji['id_jenis_menu'];
                                $key_menu = $v_ji['menu_kode'];
                                $data[ $key_shift ]['detail'][ $key_jenis ]['id'] = $key_jenis;
                                $data[ $key_shift ]['detail'][ $key_jenis ]['nama'] = $v_ji['nama_jenis_menu'];

                                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['kode'] = $key_menu;
                                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['member'] = $v_ji['member'];
                                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['nama'] = $v_ji['nama_menu'];
                                $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['harga'] = $v_ji['harga'];

                                if ( isset($data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah']) ) {
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] += $v_ji['jumlah'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] += $v_ji['total'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] += $v_ji['ppn'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] += $v_ji['service_charge'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] += ($v_ji['total'] + $v_ji['service_charge'] + $v_ji['ppn']);
                                } else {
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] = $v_ji['jumlah'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] = $v_ji['total'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] = $v_ji['ppn'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] = $v_ji['service_charge'];
                                    $data[ $key_shift ]['detail'][ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] = ($v_ji['total'] + $v_ji['service_charge'] + $v_ji['ppn']);
                                }
                                // $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['detail'] = $v_det['detail'];
                            }
                        }

                        // $d_cek_faktur = 0;
                        // if ( $v_data['mstatus'] == 0 ) {
                        //     $m_conf = new \Model\Storage\Conf();
                        //     $sql = "
                        //         select * from jual_gabungan jg 
                        //         where
                        //             jg.faktur_kode_gabungan = '".$v_data['kode_faktur']."'
                        //     ";
                        //     $d_cek_faktur = $m_conf->hydrateRaw( $sql )->count();
                        // } else {
                        //     $d_cek_faktur = 1;
                        // }

                        // if ( $d_cek_faktur > 0 ) {
                        //     $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                        //     $ppn_persen = ($v_data['ppn'] > 0) ? ($v_data['ppn'] / $v_data['total']) * 100 : 0;
                        //     $service_charge_persen = ($v_data['service_charge'] > 0) ? ($v_data['service_charge'] / $v_data['total']) * 100 : 0;

                        //     foreach ($v_data['detail'] as $k_det => $v_det) {
                        //         $key_jenis = $v_det['menu']['jenis']['id'];
                        //         $key_menu = $v_det['menu_kode'];
                        //         $data[ $key_jenis ]['id'] = $key_jenis;
                        //         $data[ $key_jenis ]['nama'] = $v_det['menu']['jenis']['nama'];

                        //         if ( !empty($v_det['detail']) ) {
                        //             foreach ($v_det['detail'] as $k_di => $v_di) {
                        //                 $key_menu .= ' | '.$v_di['menu_kode'];
                        //             }
                        //         }

                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['kode'] = $key_menu;
                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['member'] = $v_data['member'];
                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['nama'] = $v_det['menu']['nama'];
                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['harga'] = $v_det['harga'];

                        //         $ppn_nilai = ($ppn_persen > 0) ? $v_det['total'] * ($ppn_persen / 100) : 0;
                        //         $service_charge_nilai = ($service_charge_persen > 0) ? $v_det['total'] * ($service_charge_persen / 100) : 0;
                        //         $grand_total = $ppn_nilai + $service_charge_nilai + $v_det['total'];
                        //         if ( isset($data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah']) ) {
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] += $v_det['jumlah'];
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] += $v_det['total'];
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] += $ppn_nilai;
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] += $service_charge_nilai;
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] += $grand_total;
                        //         } else {
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['jumlah'] = $v_det['jumlah'];
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['total'] = $v_det['total'];
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['ppn'] = $ppn_nilai;
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['service_charge'] = $service_charge_nilai;
                        //             $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['grand_total'] = $grand_total;
                        //         }
                        //         $data[ $key_jenis ]['list_tanggal'][ $key_tanggal ]['menu'][ $key_menu ]['detail'] = $v_det['detail'];
                        //     }
                        // }
                    }
                }
            }
        }

        return $data;
    }

    public function mappingDataReportDetailPembayaran($_data, $_shift)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $key_tanggal = str_replace('-', '', substr($v_data['tgl_trans'], 0, 10));

                $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);

                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select 
                        bd.id_header,
                        j.kode_faktur,
                        bd.jenis_bayar,
                        bd.kode_jenis_kartu,
                        case
                            when ISNULL(jk.cl, 0) = 0 then
                                bd.nominal
                            when ISNULL(jk.cl, 0) = 1 then
                                tagihan.grand_total
                        end as nominal,
                        ISNULL(jk.cl, 0) as cl,
                        j.lunas,
                        tagihan.grand_total as jml_tagihan
                    from bayar_det bd
                    right join
                        jenis_kartu jk
                        on
                            bd.kode_jenis_kartu = jk.kode_jenis_kartu
                    right join
                        (
                            select 
                                *
                            from (
                                select 
                                    b.id,
                                    b.faktur_kode, 
                                    b.jml_bayar as jml_bayar,
                                    (b.total - b.diskon) as jml_tagihan
                                from bayar b 
                                where 
                                    b.faktur_kode is not null and 
                                    mstatus = 1

                                union all

                                select
                                    b.id,
                                    bh.faktur_kode,
                                    bh.bayar as jml_bayar,
                                    bh.hutang as jml_tagihan
                                from bayar_hutang bh
                                right join
                                    bayar b
                                    on
                                        bh.id_header = b.id
                                where
                                    b.mstatus = 1
                            ) _data
                            where
                                _data.faktur_kode is not null
                        ) b
                        on
                            b.id = bd.id_header
                    right join
                        jual j
                        on
                            b.faktur_kode = j.kode_faktur
                    right join
                        (
                            select 
                                j.kode_faktur as faktur_kode,
                                case
                                    when jp.exclude = 1 then
                                        sum(ji.total)
                                    when jp.include = 1 then
                                        sum(ji.total - ji.service_charge - ji.ppn)
                                end as total,
                                sum(ji.ppn) as total_ppn,
                                sum(ji.service_charge) as total_service_charge,
                                sum(ji.total) + ISNULL(jg.total, 0) as grand_total
                            from jual_item ji
                            right join
                                jenis_pesanan jp
                                on
                                    ji.kode_jenis_pesanan = jp.kode
                            right join
                                jual j
                                on
                                    ji.faktur_kode = j.kode_faktur
                            left join
                                (
                                    select sum(jml_tagihan) as total, faktur_kode from jual_gabungan group by faktur_kode
                                ) jg
                                on
                                    j.kode_faktur = jg.faktur_kode
                            where
                                j.kode_faktur is not null
                            group by
                                jp.exclude,
                                jp.include,
                                j.kode_faktur,
                                jg.total
                        ) tagihan
                        on
                            tagihan.faktur_kode = j.kode_faktur
                    where
                        j.kode_faktur = '".$v_data['kode_faktur']."' and
                        bd.jenis_bayar is not null
                ";
                $d_bayar = $m_conf->hydrateRaw( $sql );

                if ( $d_bayar->count() > 0 ) {
                    $d_bayar = $d_bayar->toArray();

                    foreach ($d_bayar as $k_bayar => $v_bayar) {
                        // if ( $v_bayar['cl'] == 1 ) {
                        //     cetak_r( $v_data['kode_faktur'] );
                        //     cetak_r( $v_bayar['nominal'] );
                        // }

                        if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]) ) {
                            $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['nama'] = $v_bayar['jenis_bayar'];
                            $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] = $v_bayar['nominal'];
                        } else {
                            $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] += $v_bayar['nominal'];
                        }
                    }
                } else {
                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select 
                            ji.faktur_kode,
                            case
                                when jp.exclude = 1 then
                                    sum(ji.total)
                                when jp.include = 1 then
                                    sum(ji.total - ji.service_charge - ji.ppn)
                            end as total,
                            sum(ji.ppn) as total_ppn,
                            sum(ji.service_charge) as total_service_charge,
                            sum(ji.total) as grand_total
                        from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                ji.kode_jenis_pesanan = jp.kode
                        right join
                            jual j
                            on
                                ji.faktur_kode = j.kode_faktur
                        where
                            j.kode_faktur = '".$v_data['kode_faktur']."' and
                            j.mstatus = 1 and
                            NOT EXISTS (select * from jual_gabungan where faktur_kode_gabungan = '".$v_data['kode_faktur']."')
                        group by
                            jp.exclude,
                            jp.include,
                            ji.faktur_kode
                    ";
                    $d_pending = $m_conf->hydrateRaw( $sql );

                    if ( $d_pending->count() > 0 ) {
                        $d_pending = $d_pending->toArray()[0];

                        // cetak_r( $d_pending );

                        $key_jb = 'pending';
                        $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['nama'] = 'PENDING';
                        $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] = $d_pending['grand_total'];
                    }
                }

                if ( isset($data[ $key_tanggal ]['jenis_pembayaran']) && !empty($data[ $key_tanggal ]['jenis_pembayaran']) ) {
                    ksort( $data[ $key_tanggal ]['jenis_pembayaran'] );
                }

                // $d_cek_faktur = 0;
                // if ( $v_data['mstatus'] == 0 ) {
                //     $m_conf = new \Model\Storage\Conf();
                //     $sql = "
                //         select * from jual_gabungan jg 
                //         where
                //             jg.faktur_kode_gabungan = '".$v_data['kode_faktur']."'
                //     ";
                //     $d_cek_faktur = $m_conf->hydrateRaw( $sql )->count();
                // } else {
                //     $d_cek_faktur = 1;
                // }

                // if ( $d_cek_faktur > 0 ) {
                //     if ( !isset($data[ $key_tanggal ]) ) {
                //         $data[ $key_tanggal ]['tanggal'] = substr($v_data['tgl_trans'], 0, 10);
                //     }

                //     if ( $v_data['lunas'] == 1 ) {
                //         foreach ($v_data['bayar'] as $k_byr => $v_byr) {
                //             if ( $v_byr['mstatus'] == 1 && $v_data['lunas'] == 1 ) {
                //                 if ( $v_byr['jml_tagihan'] <= $v_byr['jml_bayar'] ) {

                //                     foreach ($v_byr['bayar_det'] as $k_bayar => $v_bayar) {
                //                         if ( stristr($v_bayar['jenis_bayar'], 'tunai') !== false || stristr($v_bayar['jenis_bayar'], 'saldo member') !== false ) {
                //                             if ( $v_byr['jml_tagihan'] > 0 ) {
                //                                 if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]) ) {
                //                                     $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['nama'] = $v_bayar['jenis_bayar'];
                //                                     $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] = $v_byr['jml_tagihan'];
                //                                 } else {
                //                                     $data[ $key_tanggal ]['jenis_pembayaran'][ $v_bayar['jenis_bayar'] ]['total'] += $v_byr['jml_tagihan'];
                //                                 }
                //                             }
                //                         } else {
                //                             $key_jb = strtolower($v_bayar['jenis_bayar']);

                //                             if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]) ) {
                //                                 $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['nama'] = $v_bayar['jenis_bayar'];
                //                                 $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] = $v_byr['jml_tagihan'];
                //                             } else {
                //                                 $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] += $v_byr['jml_tagihan'];
                //                             }
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     } else {
                //         $key_jb = 'belum bayar';
                //         if ( !isset($data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]) ) {
                //             $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['nama'] = 'BELUM BAYAR';
                //             $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] = $v_data['grand_total'];
                //         } else {
                //             $data[ $key_tanggal ]['jenis_pembayaran'][ $key_jb ]['total'] += $v_data['grand_total'];
                //         }
                //     }

                //     if ( isset($data[ $key_tanggal ]['jenis_pembayaran']) && !empty($data[ $key_tanggal ]['jenis_pembayaran']) ) {
                //         ksort( $data[ $key_tanggal ]['jenis_pembayaran'] );
                //     }
                // }
            }

            if ( !empty($data) ) {
                ksort( $data );
            }
        }

        return $data;
    }

    public function excryptParamsExportExcel()
    {
        $params = $this->input->post('params');

        try {
            $paramsEncrypt = exEncrypt( json_encode($params) );

            $this->result['status'] = 1;
            $this->result['content'] = array('data' => $paramsEncrypt);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function exportExcel($_params)
    {
        $_data_params = json_decode( exDecrypt( $_params ), true );

        $shift = $_data_params['shift'];
        $branch = $_data_params['branch'];
        $start_date = $_data_params['start_date'].' 00:00:00';
        $end_date = $_data_params['end_date'].' 23:59:59';
        $tipe = $_data_params['tipe'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                _data.kode_branch,
                max(_data.tgl_trans) as tgl_trans,
                _data.kode_faktur,
                max(_data.mstatus) as mstatus
            from 
            (
                select j.branch as kode_branch, j.tgl_trans as tgl_trans, j.kode_faktur as kode_faktur, j.mstatus from jual j where mstatus = 1

                union all

                select j.branch as kode_branch, j.tgl_trans as tgl_trans, jg.faktur_kode_gabungan as kode_faktur, j.mstatus from jual_gabungan jg
                right join
                    jual j
                    on
                        jg.faktur_kode = j.kode_faktur
                where
                    j.mstatus = 1
            ) _data
            where
                _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                _data.kode_branch = '".$branch."' and
                _data.kode_faktur is not null
            group by
                _data.kode_branch,
                _data.kode_faktur
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );

        $_data = null;
        if ( $d_jual->count() > 0 ) {
            $_data = $d_jual->toArray();
        }

        $data_pembayaran = $this->mappingDataReportDetailPembayaran( $_data, $shift );

        $detail = null;
        $nama_view = null;
        $filename = null;
        if ( $tipe == 'harian' ) {
            $detail = $this->mappingDataReportHarian( $_data, $shift );
            $nama_view = 'export_excel_harian';
            $filename = 'export-penjualan-harian-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';
        } else {
            $detail = $this->mappingDataReportHarianProduk( $_data, $shift );
            $nama_view = 'export_excel_produk';
            $filename = 'export-penjualan-produk-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';
        }


        $data = array(
            'shift' => $shift,
            'branch' => $branch,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'pembayaran' => $data_pembayaran,
            'detail' => $detail
        );

        $content['data'] = $data;
        $res_view_html = $this->load->view('report/penjualan/'.$nama_view, $content, true);

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}

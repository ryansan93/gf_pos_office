<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SummaryPenjualanHarian extends Public_Controller {

    private $pathView = 'report/summary_penjualan_harian/';
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
                    'assets/report/summary_penjualan_harian/js/summary-penjualan-harian.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/summary_penjualan_harian/css/summary-penjualan-harian.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['branch'] = $this->getBranch();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Summary Penjualan Harian';
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

    public function getKategoriMenu()
    {
        $data = null;

        $m_km = new \Model\Storage\KategoriMenu_model();
        $d_km = $m_km->where('status', 1)->get();

        if ( $d_km->count() ) {
            $data = $d_km->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $mappingDataReport = $this->mappingDataReport( $params );

            $content_report['data'] = $mappingDataReport;
            $html_report = $this->load->view($this->pathView . 'list_report', $content_report, TRUE);

            $list_html = array(
                'list_report' => $html_report
            );

            $this->result['status'] = 1;
            $this->result['content'] = $list_html;
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function mappingDataReport($params)
    {
        $start_date = $params['start_date'].' 00:00:01';
        $end_date = $params['end_date'].' 23:59:59';
        $branch = $params['branch'];

        $data = null;

        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select 
                jl.kode_faktur as kode_faktur_asli,
                jl.kode_faktur_utama as kode_faktur,
                jl.tgl_trans,
                km.id,
                km.nama,
                case
                    when jp.exclude = 1 then
                        (sum(ji.total) + sum(ji.ppn) + sum(ji.service_charge))
                    when jp.include = 1 then
                        sum(ji.total)
                end as total
            from jual_item ji
            right join
                jenis_pesanan jp
                on
                    jp.kode = ji.kode_jenis_pesanan
            right join
                menu m
                on
                    ji.menu_kode = m.kode_menu
            left join
                kategori_menu km
                on
                    m.kategori_menu_id = km.id
            right join
                (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.kode_faktur as kode_faktur_utama,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            jg.faktur_kode as kode_faktur_utama,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            jg.faktur_kode,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur is not null
                ) jl
                on
                    jl.kode_faktur = ji.faktur_kode 
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur_utama = byr.faktur_kode
            where
                jl.kode_faktur_utama is not null and
                m.ppn = 1 and
                m.service_charge = 1
            group by
                jl.kode_faktur,
                jl.kode_faktur_utama,
                jl.tgl_trans,
                jp.exclude,
                jp.include,
                km.id,
                km.nama
        ";

        $d_jual_by_kategori_menu = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_kategori_menu->count() > 0 ) {
            $d_jual_by_kategori_menu = $d_jual_by_kategori_menu->toArray();

            foreach ($d_jual_by_kategori_menu as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]['kategori_menu']) ) {
                    $data[ $key ]['kategori_menu'][1] += ($value['id'] == 1) ? $value['total'] : 0;
                    $data[ $key ]['kategori_menu'][2] += ($value['id'] == 2) ? $value['total'] : 0;
                    $data[ $key ]['kategori_menu'][3] += ($value['id'] == 3) ? $value['total'] : 0;
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['kategori_menu'] = array(
                        '1' => ($value['id'] == 1) ? $value['total'] : 0,
                        '2' => ($value['id'] == 2) ? $value['total'] : 0,
                        '3' => ($value['id'] == 3) ? $value['total'] : 0,
                    );
                }
            }
        }

        $sql = "
            select 
                jl.kode_faktur as kode_faktur_asli,
                jl.kode_faktur_utama as kode_faktur,
                jl.tgl_trans,
                dsk.diskon_tipe,
                sum(bd.nilai) as nilai
            from (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.kode_faktur as kode_faktur_utama,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            jg.faktur_kode as kode_faktur_utama,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            jg.faktur_kode,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur is not null
                ) jl
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur_utama = byr.faktur_kode
            right join
                bayar_diskon bd
                on
                    byr.id = bd.id_header
            right join
                diskon dsk
                on
                    bd.diskon_kode = dsk.kode
            where
                jl.kode_faktur_utama is not null
            group by
                jl.kode_faktur,
                jl.kode_faktur_utama,
                jl.tgl_trans,
                dsk.diskon_tipe
        ";

        $d_jual_by_diskon = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_diskon->count() > 0 ) {
            $d_jual_by_diskon = $d_jual_by_diskon->toArray();

            foreach ($d_jual_by_diskon as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]['diskon']) ) {
                    $data[ $key ]['diskon'][1] += ($value['diskon_tipe'] == 1) ? $value['nilai'] : 0;
                    $data[ $key ]['diskon'][2] += ($value['diskon_tipe'] == 2) ? $value['nilai'] : 0;
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['diskon'] = array(
                        '1' => ($value['diskon_tipe'] == 1) ? $value['nilai'] : 0,
                        '2' => ($value['diskon_tipe'] == 2) ? $value['nilai'] : 0
                    );
                }
            }
        }

        $sql = "
            select 
                jl.kode_faktur as kode_faktur_asli,
                jl.kode_faktur_utama as kode_faktur,
                jl.tgl_trans,
                dsk.diskon_requirement,
                sum(bd.nilai) as nilai
            from (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.kode_faktur as kode_faktur_utama,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            jg.faktur_kode as kode_faktur_utama,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            jg.faktur_kode,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur is not null
                ) jl
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur_utama = byr.faktur_kode
            right join
                bayar_diskon bd
                on
                    byr.id = bd.id_header
            right join
                diskon dsk
                on
                    bd.diskon_kode = dsk.kode
            where
                jl.kode_faktur_utama is not null
            group by
                jl.kode_faktur,
                jl.kode_faktur_utama,
                jl.tgl_trans,
                dsk.diskon_requirement
        ";

        $d_jual_by_diskon_requirement = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_diskon_requirement->count() > 0 ) {
            $d_jual_by_diskon_requirement = $d_jual_by_diskon_requirement->toArray();

            foreach ($d_jual_by_diskon_requirement as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]['diskon_requirement']) ) {
                    $data[ $key ]['diskon_requirement'][ $value['diskon_requirement'] ] += $value['nilai'];
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['diskon_requirement'][ $value['diskon_requirement'] ] = $value['nilai'];
                }
            }
        }

        $sql = "
            select 
                jl.kode_faktur as kode_faktur,
                jl.tgl_trans,
                kjk.id,
                sum(bd.nominal) as nilai
            from (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur is not null
                ) jl
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur = byr.faktur_kode
            right join
                bayar_det bd
                on
                    byr.id = bd.id_header
            right join
                jenis_kartu jk
                on
                    bd.kode_jenis_kartu = jk.kode_jenis_kartu
            right join
                kategori_jenis_kartu kjk
                on
                    kjk.id = jk.kategori_jenis_kartu_id
            where
                jl.kode_faktur is not null
            group by
                jl.kode_faktur,
                jl.tgl_trans,
                kjk.id,
                bd.nominal
        ";

        $d_jual_by_kategori_pembayaran = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_kategori_pembayaran->count() > 0 ) {
            $d_jual_by_kategori_pembayaran = $d_jual_by_kategori_pembayaran->toArray();

            foreach ($d_jual_by_kategori_pembayaran as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]['kategori_pembayaran']) ) {
                    $data[ $key ]['kategori_pembayaran'][1] += ($value['id'] == 1) ? $value['nilai'] : 0;
                    $data[ $key ]['kategori_pembayaran'][2] += ($value['id'] == 2) ? $value['nilai'] : 0;
                    $data[ $key ]['kategori_pembayaran'][3] += ($value['id'] == 3) ? $value['nilai'] : 0;
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['kategori_pembayaran'] = array(
                        '1' => ($value['id'] == 1) ? $value['nilai'] : 0,
                        '2' => ($value['id'] == 2) ? $value['nilai'] : 0,
                        '3' => ($value['id'] == 3) ? $value['nilai'] : 0
                    );
                }

                // cetak_r( $key );
                // cetak_r( $data[ $key ]['kategori_pembayaran'] );
            }
        }

        $sql = "
            select 
                jl.kode_faktur as kode_faktur_asli,
                jl.kode_faktur_utama as kode_faktur,
                jl.tgl_trans,
                sum(ji.total) as total
            from jual_item ji
            right join
                menu m
                on
                    ji.menu_kode = m.kode_menu
            right join
                (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.kode_faktur as kode_faktur_utama,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            jg.faktur_kode as kode_faktur_utama,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            jg.faktur_kode,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur is not null
                ) jl
                on
                    jl.kode_faktur = ji.faktur_kode 
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur_utama = byr.faktur_kode
            where
                jl.kode_faktur is not null and
                m.ppn = 0 and
                m.service_charge = 0
            group by
                jl.kode_faktur,
                jl.kode_faktur_utama,
                jl.tgl_trans
        ";

        $d_jual_by_other_income = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_other_income->count() > 0 ) {
            $d_jual_by_other_income = $d_jual_by_other_income->toArray();

            foreach ($d_jual_by_other_income as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]) ) {
                    $data[ $key ]['other_income'] = $value['total'];
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['other_income'] = $value['total'];
                }
            }
        }

        $sql = "
            select 
                jl.kode_faktur_utama as kode_faktur,
                jl.tgl_trans,
                case
                    when jp.exclude = 1 then
                        (sum(ji.total) + sum(ji.ppn) + sum(ji.service_charge))
                    when jp.include = 1 then
                        sum(ji.total)
                end as total
            from jual_item ji
            right join
                jenis_pesanan jp
                on
                    jp.kode = ji.kode_jenis_pesanan
            right join
                menu m
                on
                    ji.menu_kode = m.kode_menu
            right join
                (
                    select * from (
                        select 
                            j.kode_faktur as kode_faktur,
                            j.kode_faktur as kode_faktur_utama,
                            j.tgl_trans
                        from jual j 
                        where 
                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                            j.branch = '".$branch."' and
                            j.mstatus = 1
                        group by
                            j.kode_faktur,
                            j.tgl_trans

                        UNION ALL

                        select 
                            jg.faktur_kode_gabungan as kode_faktur,
                            jg.faktur_kode as kode_faktur_utama,
                            j.tgl_trans
                        from jual_gabungan jg
                        right join
                            (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                    j.branch = '".$branch."' and
                                    j.mstatus = 1
                                group by
                                    j.kode_faktur,
                                    j.tgl_trans
                            ) j
                            on
                                j.kode_faktur = jg.faktur_kode
                        group by
                            jg.faktur_kode_gabungan,
                            jg.faktur_kode,
                            j.tgl_trans
                    ) jl1
                    where
                        jl1.kode_faktur_utama is not null
                ) jl
                on
                    jl.kode_faktur = ji.faktur_kode 
            right join
                (select * from bayar where mstatus = 1) byr
                on
                    jl.kode_faktur_utama = byr.faktur_kode
            where
                jl.kode_faktur is not null
            group by
                jl.kode_faktur_utama,
                jl.tgl_trans,
                jp.exclude,
                jp.include
        ";

        $d_jual_by_faktur = $m_jual->hydrateRaw( $sql );
        if ( $d_jual_by_faktur->count() > 0 ) {
            $d_jual_by_faktur = $d_jual_by_faktur->toArray();

            foreach ($d_jual_by_faktur as $key => $value) {
                $key = str_replace('-', '', substr($value['tgl_trans'], 0, 10)).' | '.$value['kode_faktur'];

                if ( isset($data[ $key ]) ) {
                    $data[ $key ]['total'] = $value['total'];
                } else {
                    if ( !isset($data[ $key ]) ) {
                        $data[ $key ]['date'] = $value['tgl_trans'];
                        $data[ $key ]['kode_faktur'] = $value['kode_faktur'];
                    }
                    $data[ $key ]['total'] = $value['total'];
                }
            }
        }

        return $data;
    }

    public function excryptParamsExportPdf()
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

    public function exportPdf($_params)
    {
        $this->load->library('PDFGenerator');

        $_data_params = json_decode( exDecrypt( $_params ), true );

        $params = array(
            'start_date' => $_data_params['start_date'],
            'end_date' => $_data_params['end_date'],
            'branch' => $_data_params['branch']
        );

        $data = $this->mappingDataReport( $params );

        $content['branch'] = $_data_params['branch'];
        $content['start_date'] = $_data_params['start_date'];
        $content['end_date'] = $_data_params['end_date'];
        $content['data'] = $data;
        $html = $this->load->view('report/summary_penjualan_harian/export_pdf', $content, true);

        $this->pdfgenerator->generate($html, "SUMMARY PENJUALAN HARIAN", 'a4', 'landscape');
    }
}

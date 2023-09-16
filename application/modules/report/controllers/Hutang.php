<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hutang extends Public_Controller {

    private $pathView = 'report/hutang/';
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
                    'assets/report/hutang/js/hutang.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/hutang/css/hutang.css'
                )
            );
            $data = $this->includes;

            $content['branch'] = $this->getBranch();
            $content['report_hutang'] = $this->load->view($this->pathView . 'report_hutang', null, TRUE);
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Hutang Pelanggan';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getBranch() {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                b.kode_branch as kode,
                b.nama
            from
                branch b
            order by
                b.nama
        ";
        $d_conf = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function getDataHutang($start_date, $end_date, $branch)
    {
        $data = null;

        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select
                data.*,
                sum(b.diskon) as total_diskon
            from
            (
                select 
                    jual.kode_faktur_utama as kode_faktur,
                    sum(ji.total) as grand_total,
                    j.tgl_trans as tgl_pesan
                from jual_item ji
                right join
                    (
                        select 
                            jl1.*
                        from 
                            (
                                select 
                                    jual.*,
                                    p.kode_pesanan as pesanan_kode
                                from 
                                    (
                                        select 
                                            j.kode_faktur as kode_faktur,
                                            j.kode_faktur as kode_faktur_utama
                                        from jual j 
                                        where 
                                            j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                            j.mstatus = 1
                                        group by
                                            j.kode_faktur

                                        UNION ALL

                                        select 
                                            jg.faktur_kode_gabungan as kode_faktur_utama,
                                            jg.faktur_kode as kode_faktur
                                        from jual_gabungan jg
                                        right join
                                            (
                                                select 
                                                    j.kode_faktur as kode_faktur,
                                                    j.tgl_trans
                                                from jual j 
                                                where 
                                                    j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                                    j.mstatus = 1
                                                group by
                                                    j.kode_faktur,
                                                    j.tgl_trans
                                            ) j
                                            on
                                                j.kode_faktur = jg.faktur_kode
                                        where
                                            jg.faktur_kode_gabungan is not null
                                        group by
                                            jg.faktur_kode_gabungan,
                                            jg.faktur_kode
                                    ) jual
                                right join
                                    jual jl
                                    on
                                        jual.kode_faktur = jl.kode_faktur 
                                right join  
                                    pesanan p
                                    on
                                        p.kode_pesanan = jl.pesanan_kode 
                                where
                                    jual.kode_faktur is not null
                                group by
                                    jual.kode_faktur,
                                    jual.kode_faktur_utama,
                                    p.kode_pesanan
                            ) jl1
                        right join
                            jual j
                            on
                                j.kode_faktur = jl1.kode_faktur
                        right join
                            pesanan p
                            on
                                j.pesanan_kode = p.kode_pesanan
                        where
                            jl1.kode_faktur is not null
                    ) jual
                    on
                        jual.kode_faktur = ji.faktur_kode
                right join
                    pesanan p
                    on
                        jual.pesanan_kode = p.kode_pesanan
                right join
                    jual j
                    on
                        jual.kode_faktur_utama = j.kode_faktur
                where
                    jual.kode_faktur is not null and
                    (j.hutang = 1 or lunas = 0)
                group by 
                    jual.kode_faktur_utama,
                    j.lunas,
                    j.tgl_trans
            ) data
            right join
                jual jl
                on
                    data.kode_faktur = jl.kode_faktur
            right join
                (
                    select * from bayar where mstatus = 1
                ) b
                on
                    data.kode_faktur = b.faktur_kode
            where
                data.kode_faktur is not null and
                jl.branch in ('".implode("', '", $branch)."')
            group by
                data.kode_faktur,
                data.grand_total,
                data.tgl_pesan
        ";
        // $sql = "
        //     select j.*, p.tgl_pesan from jual j
        //     right join
        //         pesanan p
        //         on
        //             j.pesanan_kode = p.kode_pesanan
        //     where
        //         j.tgl_trans between '".$start_date."' and '".$end_date."' and
        //         (j.hutang = 1 or j.lunas = 0) and
        //         j.mstatus = 1
        // ";

        $d_jual_hutang = $m_jual->hydrateRaw( $sql );

        if ( $d_jual_hutang->count() > 0 ) {
            $d_jual_hutang = $d_jual_hutang->toArray();

            foreach ($d_jual_hutang as $key => $value) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select * from jual j
                    where
                        j.kode_faktur = '".$value['kode_faktur']."' and
                        j.mstatus = 1
                ";
                $d_jual = $m_conf->hydrateRaw( $sql );
                if ( $d_jual->count() > 0 ) {
                    $d_jual = $d_jual->toArray();

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select distinct
                            b.tgl_trans as tgl_bayar,
                            bd.id_header,
                            bd.jenis_bayar,
                            bd.kode_jenis_kartu,
                            bd.nominal,
                            bd.no_kartu,
                            cast(bd.nama_kartu as varchar(250)) as nama_kartu
                        from 
                        (
                            select b.id, b.faktur_kode, b.jml_bayar as jml_bayar from bayar b

                            union all

                            select b.id, bh.faktur_kode, bh.bayar as jml_bayar from bayar_hutang bh
                            right join
                                bayar b
                                on
                                    bh.id_header = b.id
                        ) byr
                        right join
                            bayar b 
                            on
                                byr.id = b.id
                        right join
                            bayar_det bd 
                            on
                                bd.id_header = b.id
                        where
                            b.mstatus = 1 and
                            byr.faktur_kode = '".$value['kode_faktur']."' and
                            byr.jml_bayar > 0
                    ";
                    $d_bayar_hutang = $m_conf->hydrateRaw($sql);

                    // if ( stristr($value['kode_faktur'], '2306160035') !== FALSE || stristr($value['kode_faktur'], '2306170228') !== FALSE || stristr($value['kode_faktur'], '2306170229') !== FALSE ) {
                    //     cetak_r( $sql );
                    // }

                    $jenis_bayar = null;

                    $total_bayar = 0;
                    $total_diskon = $value['total_diskon'];
                    if ( $d_bayar_hutang->count() > 0 ) {
                        $d_bayar_hutang = $d_bayar_hutang->toArray();

                        foreach ($d_bayar_hutang as $k_bh => $v_bh) {
                            $total_bayar += $v_bh['nominal'];

                            $jenis_bayar[] = $v_bh;
                        }
                    }

                    $tgl = !empty($value['tgl_pesan']) ? $value['tgl_pesan'] : $value['tgl_trans'];

                    $key = str_replace('-', '', $tgl).' | '.$value['kode_faktur'].' | '.$d_jual[0]['member'];

                    $member_group = null;

                    if ( !empty($d_jual[0]['kode_member']) ) {
                        $m_member = new \Model\Storage\Member_model();
                        $d_member = $m_member->where('kode_member', $d_jual[0]['kode_member'])->with(['member_group'])->first();

                        if ( $d_member ) {
                            $d_member = $d_member->toArray();
                            if ( !empty($d_member['member_group']) ) {
                                $member_group = $d_member['member_group']['nama'];
                            }
                        }
                    }

                    $data[ $key ] = array(
                        'member_group' => $member_group,
                        'member' => !empty($d_jual) ? $d_jual[0]['member'] : null,
                        'nama_kasir' => !empty($d_jual) ? $d_jual[0]['nama_kasir'] : null,
                        'tgl_pesan' => $tgl,
                        'faktur_kode' => $value['kode_faktur'],
                        'hutang' => $value['grand_total']-$total_diskon,
                        'bayar' => $total_bayar,
                        'remark' => !empty($d_jual) ? $d_jual[0]['remark'] : null,
                        'jenis_bayar' => $jenis_bayar
                    );
                }

            }

            ksort($data);
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'].' 00:00:00';
        $end_date = $params['end_date'].' 23:59:59';
        $branch = $params['branch'];

        $data = $this->getDataHutang( $start_date, $end_date, $branch );

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list_report_hutang', $content, TRUE);

        echo $html;
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

        $start_date = $_data_params['start_date'];
        $end_date = $_data_params['end_date'];
        $branch = $_data_params['branch'];

        $data = $this->getDataHutang( $start_date, $end_date, $branch );

        $content['data'] = $data;
        $content['start_date'] = $start_date;
        $content['end_date'] = $end_date;
        $res_view_html = $this->load->view('report/hutang/export_excel', $content, true);

        $filename = 'export-hutang-pelanggan-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}
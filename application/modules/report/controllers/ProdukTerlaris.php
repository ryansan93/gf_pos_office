<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ProdukTerlaris extends Public_Controller {

    private $pathView = 'report/produk_terlaris/';
    private $url;
    private $hakAkses;

    private $filter = array(
        'PRODUK' => 0, 
        'MEMBER' => 1, 
        'NON MEMBER' => 2
    );

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
            $content['filter'] = $this->filter;
            $content['branch'] = $this->getBranch();

            $data['title_menu'] = 'Laporan Performance Produk dan Member';
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

    public function getData($start_date, $end_date, $filter, $branch, $jumlah)
    {
        $data = null;

        $sql_branch = null;
        if ( stristr($branch, 'all') === false ) {
            $sql_branch = "and j.branch = '".$branch."'";
        }

        $m_pi = new \Model\Storage\PesananItem_model();
        $sql = null;
        if ( $filter == 0 ) {
            $sql_jumlah = null;
            if ( stristr($jumlah, 'all') === false ) {
                $sql_jumlah = "top ".$jumlah."";
            }

            $sql = "
                select ".$sql_jumlah." * from 
                (
                    select 
                        ji.menu_kode,
                        ji.menu_nama,
                        km.nama as kategori,
                        jm.nama as jenis,
                        sum(ji.jumlah) as qty,
                        sum(ji.total) as total
                    from jual_item ji
                    right join
                        (
                            select 
                                j.kode_faktur as kode_faktur,
                                j.kode_faktur as kode_faktur_utama,
                                j.tgl_trans
                            from jual j 
                            where 
                                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                j.mstatus = 1
                                ".$sql_branch."
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
                                jual j1
                                on
                                    j1.kode_faktur = jg.faktur_kode_gabungan
                            right join
                                (
                                    select 
                                        j.kode_faktur as kode_faktur,
                                        j.tgl_trans
                                    from jual j 
                                    where 
                                        j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                        j.mstatus = 1
                                        ".$sql_branch."
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
                        ) jl
                        on
                            ji.faktur_kode = jl.kode_faktur
                    right join
                        menu m
                        on
                            ji.menu_kode = m.kode_menu
                    left join
                        kategori_menu km
                        on
                            m.kategori_menu_id = km.id
                    right join
                        jenis_menu jm
                        on
                            m.jenis_menu_id = jm.id
                    group by
                        ji.menu_kode,
                        ji.menu_nama,
                        km.nama,
                        jm.nama
                ) data
                order by
                    data.qty desc
            ";
            $d_pi = $m_pi->hydrateRaw( $sql );

            if ( $d_pi->count() > 0 ) {
                $data = $d_pi->toArray();
            }
        } else {
            $sql_member = "and (j.kode_member is null or j.kode_member = '')";
            if ( $filter == 1 ) {
                $sql_member = "and j.kode_member is not null and j.kode_member <> ''";
            }

            $sql = "
                select 
                    data.*,
                    count(jl.kode_faktur) as jml_transaksi
                from
                (
                    select
                        rtrim(ltrim(REPLACE(REPLACE(j.member, CHAR(13), ''), CHAR(10), ''))) as member,
                        isnull(j.kode_member, '') as kode_member,
                        data.menu_kode,
                        data.menu_nama,
                        data.kategori,
                        data.jenis,
                        sum(data.qty) as qty,
                        sum(data.total) as total
                    from jual j
                    right join
                        (
                            select 
                                jl.kode_faktur_utama as kode_faktur,
                                ji.menu_kode,
                                ji.menu_nama,
                                km.nama as kategori,
                                jm.nama as jenis,
                                sum(ji.jumlah) as qty,
                                sum(ji.total) as total
                            from jual_item ji
                            right join
                                (
                                    select 
                                        j.kode_faktur as kode_faktur,
                                        j.kode_faktur as kode_faktur_utama,
                                        j.tgl_trans
                                    from jual j 
                                    where 
                                        j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                        j.mstatus = 1
                                        ".$sql_branch."
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
                                        jual j1
                                        on
                                            j1.kode_faktur = jg.faktur_kode_gabungan
                                    right join
                                        (
                                            select 
                                                j.kode_faktur as kode_faktur,
                                                j.tgl_trans
                                            from jual j 
                                            where 
                                                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                                j.mstatus = 1
                                                ".$sql_branch."
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
                                ) jl
                                on
                                    ji.faktur_kode = jl.kode_faktur
                            left join
                                menu m
                                on
                                    ji.menu_kode = m.kode_menu
                            left join
                                kategori_menu km
                                on
                                    m.kategori_menu_id = km.id
                            left join
                                jenis_menu jm
                                on
                                    m.jenis_menu_id = jm.id
                            group by
                                jl.kode_faktur_utama,
                                ji.menu_kode,
                                ji.menu_nama,
                                km.nama,
                                jm.nama
                        ) data
                        on
                            data.kode_faktur = j.kode_faktur
                    where
                        data.kode_faktur is not null
                        ".$sql_member."
                    group by
                        j.member,
                        isnull(j.kode_member, ''),
                        data.menu_kode,
                        data.menu_nama,
                        data.kategori,
                        data.jenis
                ) data
                left join
                (
                    select 
                        j.* 
                    from jual j
                    right join
                    (
                        select
                            jl.kode_faktur_utama,
                            jl.tgl_trans
                        from
                        (
                            select 
                                j.kode_faktur as kode_faktur,
                                j.kode_faktur as kode_faktur_utama,
                                j.tgl_trans
                            from jual j 
                            where 
                                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                j.mstatus = 1
                                ".$sql_branch."
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
                                jual j1
                                on
                                    j1.kode_faktur = jg.faktur_kode_gabungan
                            right join
                                (
                                    select 
                                        j.kode_faktur as kode_faktur,
                                        j.tgl_trans
                                    from jual j 
                                    where 
                                        j.tgl_trans between '".$start_date."' and '".$end_date."' and
                                        j.mstatus = 1
                                        ".$sql_branch."
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
                        ) jl
                        group by
                            jl.kode_faktur_utama,
                            jl.tgl_trans
                    ) jl
                    on
                        jl.kode_faktur_utama = j.kode_faktur
                    where
                        j.mstatus = 1
                ) jl
                on
                    rtrim(ltrim(REPLACE(REPLACE(jl.member, CHAR(13), ''), CHAR(10), ''))) = rtrim(ltrim(REPLACE(REPLACE(data.member, CHAR(13), ''), CHAR(10), ''))) and
                    jl.kode_member = data.kode_member
                where
                    data.member is not null and data.member <> ''
                group by
                    data.member,
                    data.kode_member,
                    data.menu_kode,
                    data.menu_nama,
                    data.kategori,
                    data.jenis,
                    data.qty,
                    data.total
                order by
                    count(jl.kode_faktur) desc,
                    data.member asc,
                    data.qty desc
            ";
            $d_pi = $m_pi->hydrateRaw( $sql );

            if ( $d_pi->count() > 0 ) {
                $d_pi = $d_pi->toArray();

                $jumlah_max = 0;
                if ( stristr($jumlah, 'all') === false ) {
                    $jumlah_max = $jumlah;
                }

                $jumlah_data = 0;
                foreach ($d_pi as $key => $value) {
                    if ( $jumlah_max == 0 || $jumlah_max > $jumlah_data ) {
                        $key_member = $value['member'].' | '.$value['kode_member'];

                        if ( !isset($data[ $key_member ]) ) {
                            $jumlah_data++;
                        }

                        $data[ $key_member ]['nama'] = $value['member'];
                        $data[ $key_member ]['kode'] = $value['kode_member'];
                        $data[ $key_member ]['jml_transaksi'] = (int) $value['jml_transaksi'];
                        $data[ $key_member ]['detail_menu'][] = array(
                            'menu_kode' => $value['menu_kode'],
                            'menu_nama' => $value['menu_nama'],
                            'kategori' => strtoupper($value['kategori']),
                            'jenis' => $value['jenis'],
                            'qty' => $value['qty'],
                            'total' => $value['total']
                        );
                    } else {
                        break;
                    }
                }
            }
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'].' 00:00:00.000';
        $end_date = $params['end_date'].' 23:59:59.999';
        $filter = $params['filter'];
        $branch = $params['branch'];
        $jumlah = $params['jumlah'];

        $data = $this->getData( $start_date, $end_date, $filter, $branch, $jumlah);

        $content['filter'] = $filter;
        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'report', $content, TRUE);

        echo $html;
    }
}
<?php defined('BASEPATH') or exit('No direct script access allowed');

class SalesRecapitulation extends Public_Controller
{
    private $pathView = 'transaksi/sales_recapitulation/';
    private $url;
    private $hakAkses;
    /**
     * Constructor
     */
    public function __construct()
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
        // if ( $this->hakAkses['a_view'] == 1 ) {
            $this->load->library('Mobile_Detect');
            $detect = new Mobile_Detect();

            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    "assets/transaksi/pembayaran/js/pembayaran.js",
                    "assets/transaksi/sales_recapitulation/js/sales-recapitulation.js"
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    "assets/transaksi/sales_recapitulation/css/sales-recapitulation.css"
                )
            );
            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['branch'] = $this->getBranch();

            $data['title_menu'] = 'Sales Recapitulation';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function getBranch()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select * from branch order by nama asc
        ";
        $d_branch = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'].' 00:00:01';
        $end_date = $params['end_date'].' 23:59:59';
        $kode_branch = $params['branch'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                _data.tgl_trans,
                _data.mstatus,
                _data.member,
                _data.kode_pesanan,
                _data.kode_faktur,
                _data.kode_faktur_utama,
                _data.nama_waitress,
                _data.nama_kasir,
                _data.grand_total,
                max(_data.status_gabungan) as status_gabungan
            from (
                select 
                    j.tgl_trans,
                    j.mstatus,
                    j.member,
                    p.kode_pesanan as kode_pesanan,
                    j.kode_faktur as kode_faktur,
                    j.kode_faktur as kode_faktur_utama,
                    p.nama_user as nama_waitress,
                    j.nama_kasir as nama_kasir,
                    j.grand_total as grand_total,
                    jg.id as id_gabungan,
                    0 as status_gabungan
                from jual j
                right join
                    pesanan p
                    on
                        j.pesanan_kode = p.kode_pesanan
                left outer join
                    (
                        select jg.*, j.member, j.nama_kasir as nama_kasir from jual_gabungan jg
                        right join
                            jual j
                            on
                                jg.faktur_kode = j.kode_faktur
                        where
                            j.mstatus = 1 and
                            jg.id is not null
                    ) jg
                    on
                        jg.faktur_kode_gabungan = j.kode_faktur
                where
                    j.mstatus = 1 and
                    jg.id is null
                group by
                    j.tgl_trans,
                    j.mstatus,
                    j.member,
                    p.kode_pesanan,
                    j.kode_faktur,
                    j.kode_faktur,
                    p.nama_user,
                    j.nama_kasir,
                    j.grand_total,
                    jg.id

                union all

                select
                    j.tgl_trans,
                    j.mstatus,
                    _jg.member,
                    p.kode_pesanan as kode_pesanan,
                    j.kode_faktur as kode_faktur,
                    _jg.faktur_kode as kode_faktur_utama,
                    p.nama_user as nama_waitress,
                    _jg.nama_kasir as nama_kasir,
                    j.grand_total as grand_total,
                    jg.id as id_gabungan,
                    1 as status_gabungan
                from jual_gabungan jg
                right join
                    (
                        select jg.*, j.member, j.nama_kasir as nama_kasir from jual_gabungan jg
                        right join
                            jual j
                            on
                                jg.faktur_kode = j.kode_faktur
                        where
                            j.mstatus = 1
                    ) _jg
                    on
                        jg.id = _jg.id
                right join
                    jual j
                    on
                        jg.faktur_kode_gabungan = j.kode_faktur
                right join
                    pesanan p
                    on
                        j.pesanan_kode = p.kode_pesanan
            ) _data            
            where 
                _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                _data.nama_kasir is not null and
                SUBSTRING(_data.kode_pesanan, 1, 3) = '".$kode_branch."'
            group by
                _data.tgl_trans,
                _data.mstatus,
                _data.member,
                _data.kode_pesanan,
                _data.kode_faktur,
                _data.kode_faktur_utama,
                _data.nama_waitress,
                _data.nama_kasir,
                _data.grand_total
            order by
                _data.tgl_trans desc,
                _data.kode_pesanan desc,
                _data.kode_faktur desc
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $data = $d_jual->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }

    public function viewForm()
    {
        $params = $this->input->get('params');

        $kode_faktur = $params['kode_faktur'];

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                j.kode_faktur, 
                j.tgl_trans, 
                j.member as member, 
                j.nama_kasir as kasir, 
                p.nama_user as waitress,
                ji.kode_faktur_item,
                ji.kode_jenis_pesanan,
                ji.menu_nama,
                ji.menu_kode,
                ji.jumlah,
                ji.harga,
                ji.request,
                ji.pesanan_item_kode,
                case
                    when jp.exclude = 1 then
                        ji.service_charge
                    when jp.include = 1 then
                        0
                end as service_charge,
                case
                    when jp.exclude = 1 then
                        ji.ppn
                    when jp.include = 1 then
                        0
                end as ppn,
                case
                    when jp.exclude = 1 then
                        ji.total
                    when jp.include = 1 then
                        ji.total
                end as total,
                b.id as bayar_id,
                b.jml_tagihan as grand_total,
                b.jml_bayar as total_bayar,
                b.diskon as total_diskon,
                b.jenis_bayar,
                b.kode_jenis_kartu,
                b.nominal
            from 
                jual_item ji
            right join
                jenis_pesanan jp
                on
                    jp.kode = ji.kode_jenis_pesanan
            right join
                jual j
                on
                    ji.faktur_kode = j.kode_faktur
            right join
                pesanan p
                on
                    j.pesanan_kode = p.kode_pesanan
            left join
                (
                    select 
                        b.id, 
                        b.tgl_trans,
                        b.faktur_kode,
                        b.jml_tagihan,
                        b.jml_bayar,
                        b.mstatus,
                        b.diskon,
                        b.total,
                        b.member_kode,
                        b.member,
                        b.kasir,
                        b.nama_kasir,
                        bd.jenis_bayar, 
                        bd.kode_jenis_kartu, 
                        bd.nominal, 
                        jk.nama as nama_jenis_kartu 
                    from bayar_det bd
                    right join
                        jenis_kartu jk
                        on
                            bd.kode_jenis_kartu = jk.kode_jenis_kartu
                    right join
                        bayar b
                        on
                            bd.id_header = b.id
                    where
                        b.mstatus = 1
                ) b
                on
                    b.faktur_kode = j.kode_faktur
            where
                j.kode_faktur = '".$kode_faktur."'
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );        

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $total_belanja = 0;
            $total_sc = 0;
            $total_ppn = 0;

            $detail = null;
            $jenis_bayar = null;
            foreach ($d_jual as $k_jual => $v_jual) {
                $total_belanja += $v_jual['total'];
                $total_sc += $v_jual['service_charge'];
                $total_ppn += $v_jual['ppn'];

                $detail[ $v_jual['kode_faktur_item'] ] = array(
                    'kode_faktur_item' => $v_jual['kode_faktur_item'],
                    'kode_jenis_pesanan' => $v_jual['kode_jenis_pesanan'],
                    'menu_nama' => $v_jual['menu_nama'],
                    'menu_kode' => $v_jual['menu_kode'],
                    'jumlah' => $v_jual['jumlah'],
                    'harga' => $v_jual['harga'],
                    'total' => $v_jual['total'],
                    'request' => $v_jual['request'],
                    'pesanan_item_kode' => $v_jual['pesanan_item_kode'],
                    'service_charge' => $v_jual['service_charge'],
                    'ppn' => $v_jual['ppn']
                );

                if ( isset($v_jual['kode_jenis_kartu']) && !empty($v_jual['kode_jenis_kartu']) ) {
                    $jenis_bayar[ $v_jual['kode_jenis_kartu'] ] = array(
                        'kode_jenis_kartu' => $v_jual['kode_jenis_kartu'],
                        'jenis_bayar' => $v_jual['jenis_bayar'],
                        'nominal' => $v_jual['nominal']
                    );
                }
            }

            $data = array(
                'kode_faktur' => $d_jual[0]['kode_faktur'],
                'tgl_trans' => $d_jual[0]['tgl_trans'],
                'member' => $d_jual[0]['member'],
                'kasir' => $d_jual[0]['kasir'],
                'waitress' => $d_jual[0]['waitress'],
                'total_belanja' => $total_belanja,
                'total_sc' => $total_sc,
                'total_ppn' => $total_ppn,
                'grand_total' => ($d_jual[0]['grand_total'] > 0) ? $d_jual[0]['grand_total'] : $total_belanja + $total_sc + $total_ppn,
                'total_bayar' => $d_jual[0]['total_bayar'],
                'total_diskon' => $d_jual[0]['total_diskon'],
                'kembalian' => $d_jual[0]['grand_total'] - $d_jual[0]['total_bayar'],
                'bayar_id' => $d_jual[0]['bayar_id'],
                'detail' => $detail,
                'jenis_bayar' => $jenis_bayar
            );
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        echo $html;
    }
}
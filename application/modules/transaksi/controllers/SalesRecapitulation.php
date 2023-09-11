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
                jg.grand_total_gabungan,
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
                    0 as status_gabungan,
                    j.branch
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
                    jg.id,
                    j.branch

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
                    1 as status_gabungan,
                    j.branch
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
            left join
                (
                    select faktur_kode, sum(jml_tagihan) as grand_total_gabungan from jual_gabungan group by faktur_kode
                ) jg
                on
                    jg.faktur_kode = _data.kode_faktur
            where 
                _data.tgl_trans between '".$start_date."' and '".$end_date."' and
                _data.nama_kasir is not null and
                _data.branch = '".$kode_branch."'
            group by
                _data.tgl_trans,
                _data.mstatus,
                _data.member,
                _data.kode_pesanan,
                _data.kode_faktur,
                _data.kode_faktur_utama,
                _data.nama_waitress,
                _data.nama_kasir,
                _data.grand_total,
                jg.grand_total_gabungan
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
                _data.*,
                jg.grand_total_gabungan as grand_total_gabungan
            from
            (
                select 
                    j.kode_faktur, 
                    j.tgl_trans, 
                    j.member as member, 
                    j.nama_kasir as kasir, 
                    p.nama_user as waitress,
                    ji.kode_faktur_item,
                    ji.kode_jenis_pesanan,
                    jp.nama as nama_jenis_pesanan,
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
                    b.id_bayar_det,
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
                            _data.*,
                            jk.nama as nama_jenis_kartu
                        from (
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
                                bd.id as id_bayar_det, 
                                bd.jenis_bayar, 
                                bd.kode_jenis_kartu, 
                                bd.nominal
                            from bayar b
                            left join
                                bayar_det bd
                                on
                                    bd.id_header = b.id
                                                    
                            union all
                                                    
                            select
                                b.id, 
                                b.tgl_trans,
                                bh.faktur_kode,
                                b.jml_tagihan,
                                b.jml_bayar,
                                b.mstatus,
                                b.diskon,
                                b.total,
                                b.member_kode,
                                b.member,
                                b.kasir,
                                b.nama_kasir,
                                bd.id as id_bayar_det, 
                                bd.jenis_bayar, 
                                bd.kode_jenis_kartu, 
                                bd.nominal
                            from bayar b
                            left join
                                bayar_det bd 
                                on
                                    bd.id_header = b.id
                            left join
                                bayar_hutang bh 
                                on
                                    bh.id_header = b.id
                        ) _data
                        left join
                            jenis_kartu jk
                            on
                                _data.kode_jenis_kartu = jk.kode_jenis_kartu
                        where
                            _data.faktur_kode is not null and
                            _data.mstatus = 1
                    ) b
                    on
                        b.faktur_kode = j.kode_faktur
            ) _data
            left join
                (
                    select faktur_kode, sum(jml_tagihan) as grand_total_gabungan from jual_gabungan group by faktur_kode
                ) jg
                on
                    jg.faktur_kode = _data.kode_faktur
            where
                _data.kode_faktur = '".$kode_faktur."'
        ";
        $d_jual = $m_conf->hydrateRaw( $sql );        

        $data = null;
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $grand_total = 0;
            $total_bayar = 0;

            $total_belanja = 0;
            $total_sc = 0;
            $total_ppn = 0;

            $detail = null;
            $jenis_bayar = null;
            $jenis_diskon = null;
            foreach ($d_jual as $k_jual => $v_jual) {
                $key_jp = $v_jual['nama_jenis_pesanan'].'|'.$v_jual['kode_jenis_pesanan'];

                if ( !isset($detail[ $key_jp ]['item'][ $v_jual['kode_faktur_item'] ]) ) {
                    $total_belanja += $v_jual['total'];
                    $total_sc += $v_jual['service_charge'];
                    $total_ppn += $v_jual['ppn'];
                }

                if ( $v_jual['total_bayar'] > 0 ) {
                    $total_bayar = $v_jual['total_bayar'];
                }

                $detail[ $key_jp ]['kode_jenis_pesanan'] = $v_jual['kode_jenis_pesanan'];
                $detail[ $key_jp ]['nama_jenis_pesanan'] = $v_jual['nama_jenis_pesanan'];
                $detail[ $key_jp ]['item'][ $v_jual['kode_faktur_item'] ] = array(
                    'kode_faktur_item' => $v_jual['kode_faktur_item'],
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

                if ( isset($v_jual['id_bayar_det']) && !empty($v_jual['id_bayar_det']) ) {
                    $jenis_bayar[ $v_jual['id_bayar_det'] ] = array(
                        'id' => $v_jual['id_bayar_det'],
                        'kode_jenis_kartu' => $v_jual['kode_jenis_kartu'],
                        'jenis_bayar' => $v_jual['jenis_bayar'],
                        'nominal' => $v_jual['nominal']
                    );
                }

                if ( isset($v_jual['bayar_id']) && !empty( $v_jual['bayar_id'] ) ) {
                    $bayar_id = (int) $v_jual['bayar_id'];

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select bd.*, d.nama from bayar_diskon bd
                        right join
                            diskon d
                            on
                                bd.diskon_kode = d.kode
                        where
                            bd.id_header = ".$bayar_id."
                    ";
                    $d_bd = $m_conf->hydrateRaw( $sql );
                    if ( $d_bd->count() > 0 ) {
                        $d_bd = $d_bd->toArray();

                        foreach ($d_bd as $k_bd => $v_bd) {
                            $key = $v_bd['id'];
                            $jenis_diskon[ $key ] = $v_bd;
                        }
                    }
                }
            }

            $grand_total = $total_belanja + $total_sc + $total_ppn + $d_jual[0]['grand_total_gabungan'];

            $grand_total = (($grand_total - $d_jual[0]['total_diskon']) > 0) ? $grand_total - $d_jual[0]['total_diskon'] : 0;

            $data = array(
                'kode_faktur' => $d_jual[0]['kode_faktur'],
                'tgl_trans' => $d_jual[0]['tgl_trans'],
                'member' => $d_jual[0]['member'],
                'kasir' => $d_jual[0]['kasir'],
                'waitress' => $d_jual[0]['waitress'],
                'total_belanja' => $total_belanja,
                'total_sc' => $total_sc,
                'total_ppn' => $total_ppn,
                'grand_total_gabungan' => ($d_jual[0]['grand_total_gabungan'] > 0) ? $d_jual[0]['grand_total_gabungan'] : 0,
                'grand_total' => $grand_total,
                'total_bayar' => $total_bayar,
                'total_diskon' => $d_jual[0]['total_diskon'],
                'kembalian' => ($total_bayar > 0 && ($total_bayar-$grand_total) > 0) ? $total_bayar - $grand_total : 0,
                'bayar_id' => $d_jual[0]['bayar_id'],
                'detail' => $detail,
                'jenis_bayar' => $jenis_bayar,
                'jenis_diskon' => $jenis_diskon
            );
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        echo $html;
    }

    public function getJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $d_jenis_kartu = $m_jenis_kartu->where('status', 1)->orderBy('urut', 'asc')->get();

        $data = null;
        if ( $d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $d_jenis_kartu->toArray();

            foreach ($d_jenis_kartu as $key => $value) {
                $kode_jenis_kartu = $value['kode_jenis_kartu'];

                $data[] = array(
                    'kode_jenis_kartu' => $kode_jenis_kartu,
                    'kategori_jenis_kartu_id' => $value['kategori_jenis_kartu_id'],
                    'nama' => $value['nama'],
                    'status' => $value['status'],
                    'cl' => $value['cl']
                );
            }
        }

        return $data;
    }

    public function modalAddPembayaran()
    {
        $id_bayar = $this->input->get('params');

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                b.id,
                b.jml_bayar,
                j.kode_faktur,
                j.total as total,
                j.service_charge as service_charge,
                j.ppn as ppn,
                (j.total + j.service_charge + j.ppn) as grand_total,
                jg.total as total_gabungan,
                jg.service_charge as sc_gabungan,
                jg.ppn as ppn_gabungan,
                (jg.total + jg.service_charge + jg.ppn) as grand_total_gabungan,
                bd.nilai as diskon
            from
                bayar b
            right join
                (
                    select max(id) as id, faktur_kode from bayar group by faktur_kode
                ) byr
                on
                    b.id = byr.id
            left join
                (
                    select
                        _data.kode_faktur,
                        sum(_data.service_charge) as service_charge,
                        sum(_data.ppn) as ppn,
                        sum(_data.total) as total
                    from
                    (
                        select 
                            j.kode_faktur,
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
                            end as total
                        from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                jp.kode = ji.kode_jenis_pesanan
                        right join
                            jual j
                            on
                                j.kode_faktur = ji.faktur_kode
                    ) _data
                    group by
                        _data.kode_faktur
                ) j
                on
                    j.kode_faktur = b.faktur_kode
            left join
                (
                    select
                        _data.faktur_kode,
                        sum(_data.service_charge) as service_charge,
                        sum(_data.ppn) as ppn,
                        sum(_data.total) as total
                    from
                    (
                        select 
                            jg.faktur_kode,
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
                            end as total
                        from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                jp.kode = ji.kode_jenis_pesanan
                        right join
                            jual_gabungan jg
                            on
                                jg.faktur_kode_gabungan = ji.faktur_kode
                    ) _data
                    group by
                        _data.faktur_kode
                ) jg
                on
                    jg.faktur_kode = b.faktur_kode
            left join
                (
                    select id_header, sum(nilai) as nilai from bayar_diskon group by id_header
                ) bd
                on
                    bd.id_header = b.id
            where
                b.id = '".$id_bayar."'
        ";
        $d_bayar = $m_conf->hydrateRaw( $sql );

        $kode_faktur = null;
        $sisa_tagihan = 0;
        if ( $d_bayar->count() > 0 ) {
            $d_bayar = $d_bayar->toArray()[0];

            $kode_faktur = $d_bayar['kode_faktur'];
            $jml_tagihan = $d_bayar['grand_total'] + $d_bayar['grand_total_gabungan'];

            if ( $jml_tagihan > $d_bayar['jml_bayar'] ) {
                $sisa_tagihan = $jml_tagihan - $d_bayar['jml_bayar'];
            }

            if ( !empty($d_bayar['diskon']) && $d_bayar['diskon'] > 0 ) {
                $sisa_tagihan -= $d_bayar['diskon'];
            }
        }

        $content['id_bayar'] = $id_bayar;
        $content['kode_faktur'] = $kode_faktur;
        $content['sisa_tagihan'] = $sisa_tagihan;
        $content['jenis_kartu'] = $this->getJenisKartu();
        $html = $this->load->view($this->pathView . 'modalAddPembayaran', $content, TRUE);

        echo $html;
    }

    public function modalAddDiskon()
    {
        $id_bayar = $this->input->get('params');

        $m_diskon = new \Model\Storage\Diskon_model();
        $now = $m_diskon->getDate();

        $today = $now['tanggal'];
        $jam = $now['jam'];

        $member = 0;
        $kode_branch = 0;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                b.*
            from
                bayar b
            right join
                (
                    select max(id) as id, faktur_kode from bayar group by faktur_kode
                ) byr
                on
                    b.id = byr.id
            where
                b.id = '".$id_bayar."'
        ";
        $d_bayar = $m_conf->hydrateRaw( $sql );

        // $sisa_tagihan = 0;
        if ( $d_bayar->count() > 0 ) {
            $d_bayar = $d_bayar->toArray()[0];

            // if ( $d_bayar['jml_tagihan'] > $d_bayar['jml_bayar'] ) {
            //     $sisa_tagihan = $d_bayar['jml_tagihan'] - $d_bayar['jml_bayar'];
            // }

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $d_bayar['faktur_kode'])->first();

            if ( $d_jual ) {
                $kode_branch = $d_jual->branch;
                if ( !empty($d_jual->kode_member) ) {
                    $member = 1;
                }
            }
        }

        if ( $member == 1 ) {
            $d_diskon = $m_diskon->where('start_date', '<=', $today)
                                 ->where('end_date', '>=', $today)
                                 ->where('start_time', '<=', $jam)
                                 ->where('end_time', '>=', $jam)
                                 ->where('member', 1)
                                 ->where('mstatus', 1)
                                 ->where('branch_kode', $kode_branch)
                                 ->get();
        } else {
            $d_diskon = $m_diskon->where('start_date', '<=', $today)
                                 ->where('end_date', '>=', $today)
                                 ->where('start_time', '<=', $jam)
                                 ->where('end_time', '>=', $jam)
                                 ->where('non_member', 1)
                                 ->where('mstatus', 1)
                                 ->where('branch_kode', $kode_branch)
                                 ->get();
        }

        $data = null;
        if ( $d_diskon->count() > 0 ) {
            $d_diskon = $d_diskon->toArray();
            foreach ($d_diskon as $key => $value) {
                $data[] = $d_diskon[$key];
            }
        }

        $content['id_bayar'] = $id_bayar;
        $content['diskon'] = $data;
        $html = $this->load->view($this->pathView . 'modalAddDiskon', $content, TRUE);

        echo $html;
    }

    public function savePembayaran()
    {
        $params = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('id', $params['id_bayar'])->first();

            // $id_header = null;

            // if ( $params['status_pembayaran'] == 1 ) {
            //     $m_bayar = new \Model\Storage\Bayar_model();
            //     $now = $m_bayar->getDate();

            //     $m_bayar->tgl_trans = $now['waktu'];
            //     $m_bayar->faktur_kode = null;
            //     $m_bayar->jml_tagihan = $d_bayar->jml_tagihan;
            //     $m_bayar->jml_bayar = $d_bayar->jml_bayar;
            //     $m_bayar->ppn = 0;
            //     $m_bayar->service_charge = 0;
            //     $m_bayar->diskon = 0;
            //     $m_bayar->total = 0;
            //     $m_bayar->member_kode = $d_bayar->member_kode;
            //     $m_bayar->member = $d_bayar->member;
            //     $m_bayar->kasir = $this->userid;
            //     $m_bayar->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
            //     $m_bayar->mstatus = 1;
            //     $m_bayar->save();

            //     $id_header = $m_bayar->id;

            //     $m_bayarh = new \Model\Storage\BayarHutang_model();
            //     $m_bayarh->id_header = $id_header;
            //     $m_bayarh->faktur_kode = $params['kode_faktur'];
            //     $m_bayarh->hutang = $value['hutang'];
            //     $m_bayarh->sudah_bayar = (isset($value['sudah_bayar']) && !empty($value['sudah_bayar']) && $value['sudah_bayar'] > 0) ? $value['sudah_bayar'] : 0;
            //     $m_bayarh->bayar = $params['jml_bayar'];
            //     $m_bayarh->save();

            //     if ( $value['bayar'] >= $value['hutang'] ) {
            //         $m_jual = new \Model\Storage\Jual_model();
            //         $m_jual->where('kode_faktur', $value['faktur_kode'])->update(
            //             array(
            //                 'lunas' => 1
            //             )
            //         );
            //     } else {
            //         $m_jual = new \Model\Storage\Jual_model();
            //         $m_jual->where('kode_faktur', $value['faktur_kode'])->update(
            //             array(
            //                 'lunas' => 0
            //             )
            //         );
            //     }
            // } else {
            // }
            $id_header = $params['id_bayar'];

            $m_bd = new \Model\Storage\BayarDet_model();
            $m_bd->id_header = $id_header;
            $m_bd->jenis_bayar = $params['jenis_bayar'];
            $m_bd->kode_jenis_kartu = $params['kode_jenis_kartu'];
            $m_bd->nominal = $params['jml_bayar'];
            $m_bd->no_kartu = $params['no_kartu'];
            $m_bd->nama_kartu = $params['nama_kartu'];
            $m_bd->save();

            $m_jk = new \Model\Storage\JenisKartu_model();
            $d_jk = $m_jk->where('kode_jenis_kartu', $params['kode_jenis_kartu'])->first();

            if ( $d_jk->cl == 1 ) {
                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $d_bayar->faktur_kode)->update(
                    array(
                        'hutang' => 1
                    )
                );
            }

            $deskripsi_log = 'Tambah pembayaran oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_bayar, $deskripsi_log, null, $keterangan, $id_verifikasi, $m_bd);

            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $d_bayar->faktur_kode);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function saveDiskon()
    {
        $params = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $id_bayar = null;
            $id_bayar_diskon = null;

            foreach ($params as $key => $value) {
                $m_bd = new \Model\Storage\BayarDiskon_model();
                $m_bd->id_header = $value['id_bayar'];
                $m_bd->diskon_kode = $value['kode_diskon'];
                $m_bd->save();

                $id_bayar_diskon[] = $m_bd->id;
                $id_bayar = $value['id_bayar'];
            }

            $d_bd = null;
            if ( !empty($id_bayar_diskon) ) {
                $m_bd = new \Model\Storage\BayarDiskon_model();
                $d_bd = $m_bd->whereIn('id', $id_bayar_diskon)->get();
            }

            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('id', $id_bayar)->first();

            $deskripsi_log = 'Tambah diskon oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_bayar, $deskripsi_log, null, $keterangan, $id_verifikasi, $d_bd);

            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $d_bayar->faktur_kode);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deletePesanan()
    {
        $params = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $kode_faktur_item = $params['kode_faktur_item'];

            $m_ji = new \Model\Storage\JualItem_model();
            $d_ji = $m_ji->where('kode_faktur_item', $kode_faktur_item)->first();

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $d_ji->faktur_kode)->first();

            $m_ji->where('kode_faktur_item', $kode_faktur_item)->delete();

            $deskripsi_log = 'Void pesanan oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_jual, $deskripsi_log, null, $keterangan, $id_verifikasi, $d_ji);

            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $d_ji->faktur_kode);
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deletePembayaran()
    {
        $params = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $id_bayar_det = $params['id'];

            $m_bd = new \Model\Storage\BayarDet_model();
            $d_bd = $m_bd->where('id', $id_bayar_det)->first();

            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('id', $d_bd->id_header)->first();

            $m_jk = new \Model\Storage\JenisKartu_model();
            $d_jk = $m_jk->where('kode_jenis_kartu', $d_bd->kode_jenis_kartu)->first();

            if ( $d_jk->cl == 1 ) {
                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $d_bayar->faktur_kode)->update(
                    array(
                        'hutang' => 0
                    )
                );
            }

            $m_bd->where('id', $id_bayar_det)->delete();

            $deskripsi_log = 'Void pembayaran oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_bayar, $deskripsi_log, null, $keterangan, $id_verifikasi, $d_bd);

            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $d_bayar->faktur_kode);
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deleteDiskon()
    {
        $params = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $id_bayar_diskon = $params['id'];

            $m_bd = new \Model\Storage\BayarDiskon_model();
            $d_bd = $m_bd->where('id', $id_bayar_diskon)->first();

            $m_bayar = new \Model\Storage\Bayar_model();
            $d_bayar = $m_bayar->where('id', $d_bd->id_header)->first();

            $m_bd->where('id', $id_bayar_diskon)->delete();

            $deskripsi_log = 'Void diskon oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_bayar, $deskripsi_log, null, $keterangan, $id_verifikasi, $d_bd);

            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $d_bayar->faktur_kode);
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deleteTransaksi()
    {
        $kode_faktur = $this->input->post('params');
        $keterangan = $this->input->post('keterangan');
        $id_verifikasi = $this->input->post('id_verifikasi');

        try {
            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();

            $m_pesanan = new \Model\Storage\Pesanan_model();
            $d_pesanan = $m_pesanan->where('kode_pesanan', $d_jual->pesanan_kode)->first();

            $deskripsi_log = 'Void transaksi oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $d_pesanan, $deskripsi_log, null, $keterangan, $id_verifikasi);

            $m_bayar = new \Model\Storage\Bayar_model();
            $m_bayar->where('faktur_kode', $kode_faktur)->update(
                array(
                    'mstatus' => 0
                )
            );

            $m_jual->where('kode_faktur', $kode_faktur)->update(
                array(
                    'mstatus' => 0
                )
            );

            $m_pesanan->where('kode_pesanan', $d_jual->pesanan_kode)->update(
                array(
                    'mstatus' => 0
                )
            );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitungUlang()
    {
        $kode_faktur = $this->input->post('params');

        try {
            $harga_hpp = 0;
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select b.id, max(d.harga_hpp) as harga_hpp from bayar_diskon bd
                right join
                    diskon d
                    on
                        bd.diskon_kode = d.kode
                right join
                    bayar b
                    on
                        bd.id_header = b.id
                where
                    b.faktur_kode = '".$kode_faktur."' and
                    b.mstatus = 1
                group by
                    b.id
            ";
            $d_conf = $m_conf->hydrateRaw( $sql );

            if ( $d_conf->count() > 0 ) {
                $d_conf = $d_conf->toArray()[0];

                $harga_hpp = $d_conf['harga_hpp'];
            }

            $result = $this->prosesUpdatePenjualan($kode_faktur, $harga_hpp);

            if ( isset($result['status']) && $result['status'] == 1 ) {
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select
                        _data.kode_faktur_utama as kode_faktur,
                        sum(_data.grand_total) as grand_total,
                        sum(_data.total) as total,
                        sum(_data.service_charge) as service_charge_real,
                        sum(_data.ppn) as ppn_real,
                        sum(_data.service_charge) as service_charge,
                        sum(_data.ppn) as ppn
                    from
                    (
                        select 
                            j.kode_faktur_utama, 
                            case
                                when jp.exclude = 1 then
                                    ji.total + ji.service_charge + ji.ppn
                                when jp.include = 1 then
                                    ji.total
                            end as grand_total,
                            case
                                when jp.exclude = 1 then
                                    ji.service_charge
                                when jp.include = 1 then
                                    0
                            end as service_charge_real,
                            case
                                when jp.exclude = 1 then
                                    ji.ppn
                                when jp.include = 1 then
                                    0
                            end as ppn_real,
                            ji.total, 
                            ji.service_charge, 
                            ji.ppn
                        from jual_item ji
                        right join
                            jenis_pesanan jp
                            on
                                jp.kode = ji.kode_jenis_pesanan
                        right join
                            (
                                select * from (
                                    select 
                                        j.kode_faktur as kode_faktur,
                                        j.kode_faktur as kode_faktur_utama,
                                        j.tgl_trans
                                    from jual j 
                                    where 
                                        j.kode_faktur = '".$kode_faktur."' and
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
                                                j.kode_faktur = '".$kode_faktur."' and
                                                j.mstatus = 1
                                            group by
                                                j.kode_faktur,
                                                j.tgl_trans
                                        ) j
                                        on
                                            j.kode_faktur = jg.faktur_kode
                                    where
                                        j1.mstatus = 1
                                    group by
                                        jg.faktur_kode_gabungan,
                                        jg.faktur_kode,
                                        j.tgl_trans
                                ) jl1
                                where
                                    jl1.kode_faktur is not null
                            ) j
                            on
                                ji.faktur_kode = j.kode_faktur
                    ) _data
                    group by
                        _data.kode_faktur_utama
                ";
                $d_ji_new = $m_conf->hydrateRaw( $sql );

                if ( $d_ji_new->count() > 0 ) {
                    $d_ji_new = $d_ji_new->toArray()[0];

                    $m_conf = new \Model\Storage\Conf();
                    $sql = "
                        select 
                            b.id,
                            b.tgl_trans,
                            byr.faktur_kode,
                            b.jml_tagihan,
                            b.jml_bayar,
                            b.jenis_bayar,
                            b.jenis_kartu_kode,
                            b.no_bukti,
                            b.kode_bayar_non_kasir,
                            b.mstatus,
                            b.ppn,
                            b.service_charge,
                            b.diskon,
                            b.total,
                            b.member_kode,
                            b.member,
                            b.kasir,
                            b.nama_kasir
                        from bayar b
                        right join
                            (
                                select * from
                                (
                                    select max(id) as id, faktur_kode from bayar where faktur_kode is not null group by faktur_kode
                                    
                                    union all
                                    
                                    select
                                        b.id,
                                        bh.faktur_kode 
                                    from bayar_hutang bh 
                                    right join
                                        bayar b
                                        on
                                            bh.id_header = b.id
                                ) _data
                                where
                                    _data.faktur_kode is not null
                            ) byr
                            on
                                b.id = byr.id
                        where
                            b.mstatus = 1 and
                            byr.faktur_kode = '".$kode_faktur."'
                        order by
                            b.id asc
                    ";
                    $d_bayar = $m_conf->hydrateRaw( $sql );

                    if ( $d_bayar->count() > 0 ) {
                        $d_bayar = $d_bayar->toArray();

                        $jml_tagihan_total = $d_ji_new['grand_total'];
                        $jml_bayar_total = 0;

                        $jml_tagihan = $d_ji_new['grand_total'];
                        foreach ($d_bayar as $k_bayar => $v_bayar) {
                            $jml_bayar = 0;

                            $m_conf = new \Model\Storage\Conf();
                            $sql = "
                                select bd.id_header, sum(bd.nominal) as total_bayar from bayar_det bd
                                where
                                    bd.id_header = ".$v_bayar['id']."
                                group by
                                    bd.id_header
                            ";
                            $d_tb = $m_conf->hydrateRaw( $sql );

                            if ( $d_tb->count() > 0 ) {
                                $d_tb = $d_tb->toArray()[0];

                                $jml_bayar = $d_tb['total_bayar'];
                                $jml_bayar_total += $jml_bayar;
                            }

                            $data_diskon = $this->hitDiskon($d_ji_new['kode_faktur'], $v_bayar['id']);

                            if ( isset($data_diskon['data_diskon']) && !empty($data_diskon['data_diskon']) ) {
                                foreach ($data_diskon['data_diskon'] as $k_dd => $v_dd) {
                                    $m_bd = new \Model\Storage\BayarDiskon_model();
                                    $m_bd->where('id', $v_dd['id'])->update(
                                        array(
                                            'nilai' => $v_dd['nominal']
                                        )
                                    );
                                }
                            }

                            $jml_tagihan = $jml_tagihan - $data_diskon['total_diskon'];

                            $jml_tagihan_total -= $data_diskon['total_diskon'];

                            $m_bayar = new \Model\Storage\Bayar_model();
                            $m_bayar->where('id', $v_bayar['id'])->update(
                                array(
                                    'jml_tagihan' => $d_ji_new['grand_total']-$data_diskon['total_diskon'],
                                    'jml_bayar' => $jml_bayar,
                                    'diskon' => $data_diskon['total_diskon'],
                                    'total' => $d_ji_new['grand_total'],
                                    'ppn' => $d_ji_new['ppn_real'],
                                    'service_charge' => $d_ji_new['service_charge_real']
                                )
                            );
                        }

                        $lunas = 0;
                        if ( $jml_bayar_total >= $jml_tagihan_total ) {
                            $lunas = 1;
                        }

                        $m_conf = new \Model\Storage\Conf();
                        $sql = "
                            select 
                                j.kode_faktur,
                                case
                                    when jp.include = 1 then
                                        sum(ji.total) - sum(ji.service_charge) - sum(ji.ppn)
                                    when jp.exclude = 1 then
                                        sum(ji.total)
                                end as total,
                                sum(ji.service_charge) as service_charge,
                                sum(ji.ppn) as ppn,
                                case
                                    when jp.include = 1 then
                                        sum(ji.total)
                                    when jp.exclude = 1 then
                                        sum(ji.total) + sum(ji.service_charge) + sum(ji.ppn)
                                end as grand_total
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
                                j.kode_faktur = '".$kode_faktur."'
                            group by
                                j.kode_faktur,
                                jp.include,
                                jp.exclude
                        ";
                        $d_nota = $m_conf->hydrateRaw( $sql );

                        if ( $d_nota->count() > 0 ) {
                            $d_nota = $d_nota->toArray()[0];

                            $m_jual = new \Model\Storage\Jual_model();
                            $m_jual->where('kode_faktur', $d_ji_new['kode_faktur'])->update(
                                array(
                                    'total' => $d_nota['total'],
                                    'service_charge' => $d_nota['service_charge'],
                                    'ppn' => $d_nota['ppn'],
                                    'grand_total' => $d_nota['grand_total'],
                                    'lunas' => $lunas
                                )
                            );
                        }
                    }
                }

                $m_jual = new \Model\Storage\Jual_model();
                $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();

                // $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
                // Modules::run( 'base/event/save', $d_jual, $deskripsi_log);

                $this->result['status'] = 1;
                $this->result['content'] = array('kode_faktur' => $kode_faktur);
                $this->result['message'] = 'Data berhasil di proses.';
            } else {
                $result['message'] = 'Data berhasil di proses.';
            }
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function hitDiskon($_kode_faktur, $_id_bayar)
    {
        $m_bayar = new \Model\Storage\Bayar_model();
        $d_bayar = $m_bayar;

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select bd.id, bd.diskon_kode from bayar_diskon bd
            where
                bd.id_header = '".$_id_bayar."'
        ";
        $d_bd = $m_conf->hydrateRaw( $sql );

        $_data_diskon = null;
        if ( $d_bd->count() > 0 ) {
            foreach ($d_bd as $k_bd => $v_bd) {
                $_data_diskon[] = array(
                    'id' => $v_bd['id'],
                    'diskon_kode' => $v_bd['diskon_kode']
                );
            }
        }

        $data_metode_bayar = (isset($_data_metode_bayar) && !empty($_data_metode_bayar) && ( !empty($_data_metode_bayar[0]) || !empty($_data_metode_bayar[count($_data_metode_bayar) - 1]) )) ? $_data_metode_bayar : null;

        $m_conf = new \Model\Storage\Conf();
        $now = $m_conf->getDate();

        $today = $now['tanggal'];

        $kode_faktur = $_kode_faktur;

        $data_diskon = null;
        $tot_belanja = 0;
        $tot_diskon = 0;
        $tot_ppn = 0;
        $tot_sc = 0;
        $jenis_harga_exclude = 0;
        $jenis_harga_include = 0;
        
        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select 
                jual_utama.branch,
                jual.kode_faktur_utama as kode_faktur,
                ji.kode_jenis_pesanan,
                jp.exclude,
                jp.include,
                sum(ji.jumlah) as jumlah, 
                sum(ji.total) as total,
                /* case 
                    when jp.exclude = 1 then
                        sum(ji.total)
                    when jp.include = 1 then
                        sum(ji.total) + ISNULL(sum(ji.ppn), 0) + ISNULL(sum(ji.service_charge), 0)
                end as total, */
                ISNULL(sum(ji.ppn), 0) as nilai_ppn, 
                ISNULL(sum(ji.service_charge), 0) as nilai_service_charge, 
                max(m.ppn) as ppn, 
                max(m.service_charge) as service_charge
            from jual_item ji
            right join
                (
                    select j.kode_faktur as kode_faktur_utama, j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                    UNION ALL
                    select jg.faktur_kode as kode_faktur_utama, jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                ) jual
                on
                    jual.kode_faktur = ji.faktur_kode 
            right join
                jual jual_utama
                on
                    jual_utama.kode_faktur = jual.kode_faktur_utama
            right join
                menu m
                on
                    m.kode_menu = ji.menu_kode
            right join
                jenis_pesanan jp
                on
                    jp.kode = ji.kode_jenis_pesanan
            where
                ji.jumlah > 0
            group by
                ji.kode_jenis_pesanan,
                jp.exclude,
                jp.include,
                jual_utama.branch,
                jual.kode_faktur_utama
        ";
        // $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->first();
        $d_jual = $m_jual->hydrateRaw( $sql );
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            foreach ($d_jual as $k_jual => $v_jual) {
                $ppn = 0;
                if ( $v_jual['ppn'] == 1 ) {
                    $m_ppn = new \Model\Storage\Ppn_model();
                    $d_ppn = $m_ppn->where('branch_kode', $v_jual['branch'])
                                   ->where('tgl_berlaku', '<=', $today)
                                   ->where('mstatus', 1)
                                   ->first();
                    if ( $d_ppn ) {
                        if ( $d_ppn->nilai > 0 ) {
                            $ppn = $d_ppn->nilai/100;
                        }
                    }
                }

                $sc = 0;
                if ( $v_jual['service_charge'] == 1 ) {
                    $m_sc = new \Model\Storage\ServiceCharge_model();
                    $d_sc = $m_sc->where('branch_kode', $v_jual['branch'])
                                   ->where('tgl_berlaku', '<=', $today)
                                   ->where('mstatus', 1)
                                   ->first();

                    if ( $d_sc ) {
                        if ( $d_sc->nilai > 0 ) {
                            $sc = $d_sc->nilai/100;
                        }
                    }
                }

                $tot_belanja += $v_jual['total'];
                $tot_diskon = 0;
                $tot_ppn = 0;
                $tot_sc = 0;
                $jenis_harga_exclude = $v_jual['exclude'];
                $jenis_harga_include = $v_jual['include'];

                if ( !empty($_data_diskon) ) {
                    foreach ($_data_diskon as $k_dd => $v_dd) {
                        $m_diskon = new \Model\Storage\Diskon_model();
                        $d_diskon = $m_diskon->where('kode', $v_dd['diskon_kode'])->first();

                        if ( $d_diskon->diskon_tipe == 1 ) {
                            $tot_diskon_by_kode = 0;

                            $hitung = 1;
                            // $hitung = 0;
                            // if ( !empty($data_metode_bayar) ) {
                            //     foreach ($data_metode_bayar as $k_dmb => $v_dmb) {
                            //         if ( !empty($v_dmb) ) {
                            //             $m_djk = new \Model\Storage\DiskonJenisKartu_model();
                            //             $d_djk = $m_djk->where('diskon_kode', $v_dd['diskon_kode'])->where('jenis_kartu_kode', $v_dmb['kode_jenis_kartu'])->first();

                            //             if ( $d_djk ) {
                            //                 $hitung = 1;

                            //                 break;
                            //             }
                            //         }
                            //     }
                            // }

                            if ( $hitung == 1 ) {
                                if ( $d_diskon->status_ppn == 1 ) {
                                    $ppn = ($d_diskon->ppn > 0) ? $d_diskon->ppn/100 : 0;
                                }

                                if ( $d_diskon->status_service_charge == 1 ) {
                                    $sc = ($d_diskon->service_charge > 0) ? $d_diskon->service_charge/100 : 0;
                                }

                                if ( $tot_belanja > $d_diskon->min_beli ) {
                                    if ( $d_diskon->diskon_jenis == 'persen' ) {
                                        $diskon = ($d_diskon->diskon > 0) ? ($tot_belanja * ($d_diskon->diskon/100)) : 0;
                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;
                                    } else {
                                        $diskon = $d_diskon->diskon;
                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;
                                    }
                                }

                                $_tot_belanja = $v_jual['total'];
                                if ( $v_jual['exclude'] == 1 ) {
                                    $tot_sc += $_tot_belanja*$sc;
                                    $tot_ppn += ($_tot_belanja + $tot_sc)*$ppn;
                                }

                                $data_diskon[ $v_dd['id'] ] = array(
                                    'id' => $v_dd['id'],
                                    'kode' => $v_dd['diskon_kode'],
                                    'nominal' => $tot_diskon_by_kode
                                );
                            }
                        }

                        if ( $d_diskon->diskon_tipe == 2 ) {
                            $tot_diskon_by_kode = 0;

                            $hitung = 1;
                            // $hitung = 0;
                            // if ( !empty($data_metode_bayar) ) {
                            //     foreach ($data_metode_bayar as $k_dmb => $v_dmb) {
                            //         if ( !empty($v_dmb) ) {
                            //             $m_djk = new \Model\Storage\DiskonJenisKartu_model();
                            //             $d_djk = $m_djk->where('diskon_kode', $v_dd['diskon_kode'])->where('jenis_kartu_kode', $v_dmb['kode_jenis_kartu'])->first();

                            //             if ( $d_djk ) {
                            //                 $hitung = 1;

                            //                 break;
                            //             }
                            //         }
                            //     }
                            // }

                            if ( $hitung == 1 ) {
                                if ( $d_diskon->status_ppn == 1 ) {
                                    $ppn = ($d_diskon->ppn > 0) ? $d_diskon->ppn/100 : 0;
                                }

                                if ( $d_diskon->status_service_charge == 1 ) {
                                    $sc = ($d_diskon->service_charge > 0) ? $d_diskon->service_charge/100 : 0;
                                }

                                $m_dm = new \Model\Storage\DiskonMenu_model();
                                $sql = "
                                    select 
                                        dm.menu_kode,
                                        case
                                            when ji.total > 0 and dm.diskon > 0 then
                                                case
                                                    when dm.diskon_jenis = 'persen' then
                                                        ji.total * (dm.diskon / 100)
                                                    else
                                                        ji.total - dm.diskon
                                                end
                                            else
                                                0
                                        end as diskon
                                    from diskon_menu dm
                                    right join
                                        (
                                            select 
                                                jm.id as id_jenis_menu,
                                                jm.nama as nama_jenis_menu,
                                                ji.menu_kode, 
                                                ji.menu_nama, 
                                                ji.kode_jenis_pesanan,
                                                jp.exclude,
                                                jp.include,
                                                sum(ji.jumlah) as jumlah, 
                                                sum(ji.total) as total
                                                /* case 
                                                    when jp.exclude = 1 then
                                                        sum(ji.total)
                                                    when jp.include = 1 then
                                                        sum(ji.total) + sum(ji.ppn) + sum(ji.service_charge)
                                                end as total */
                                            from jual_item ji
                                            right join
                                                (
                                                    select j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                                                    UNION ALL
                                                    select jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                                                ) jual
                                                on
                                                    jual.kode_faktur = ji.faktur_kode 
                                            right join
                                                menu m
                                                on
                                                    m.kode_menu = ji.menu_kode
                                            right join
                                                jenis_pesanan jp
                                                on
                                                    jp.kode = ji.kode_jenis_pesanan
                                            right join
                                                jenis_menu jm
                                                on
                                                    jm.id = m.jenis_menu_id
                                            where
                                                ji.jumlah > 0
                                            group by
                                                jm.id,
                                                jm.nama,
                                                ji.kode_jenis_pesanan,
                                                jp.exclude,
                                                jp.include,
                                                ji.menu_kode, 
                                                ji.menu_nama
                                        ) ji
                                        on
                                            (dm.jenis_menu_id = 'all' or dm.jenis_menu_id = ji.id_jenis_menu)
                                            and
                                            (dm.menu_kode = 'all' or dm.menu_kode = ji.menu_kode)
                                    where
                                        dm.diskon_kode = '".$v_dd['diskon_kode']."' and
                                        ji.jumlah >= dm.jml_min
                                ";
                                $d_dm = $m_dm->hydrateRaw( $sql );

                                if ( $d_dm->count() > 0 ) {
                                    $d_dm = $d_dm->toArray();

                                    $idx = 0;
                                    foreach ($d_dm as $k_dm => $v_dm) {
                                        $diskon = $v_dm['diskon'];

                                        $tot_diskon += $diskon;
                                        $tot_diskon_by_kode += $diskon;
                                        $tot_belanja -= $diskon;

                                        $_tot_belanja = $v_jual['total'];
                                        $idx++;
                                        if ( count($d_dm) == $idx ) {
                                            if ( $v_jual['exclude'] == 1 ) {
                                                $tot_sc += $_tot_belanja*$sc;
                                                $tot_ppn += ($_tot_belanja + $tot_sc)*$ppn;
                                            }
                                        }
                                    }

                                    $data_diskon[ $v_dd['id'] ] = array(
                                        'id' => $v_dd['id'],
                                        'kode' => $v_dd['diskon_kode'],
                                        'nominal' => $tot_diskon_by_kode
                                    );
                                }
                            }
                        }

                        if ( $d_diskon->diskon_tipe == 3 ) {
                            $tot_diskon_by_kode = 0;

                            $hitung = 1;
                            // $hitung = 0;
                            // if ( !empty($data_metode_bayar) ) {
                            //     foreach ($data_metode_bayar as $k_dmb => $v_dmb) {
                            //         if ( !empty($v_dmb) ) {
                            //             $m_djk = new \Model\Storage\DiskonJenisKartu_model();
                            //             $d_djk = $m_djk->where('diskon_kode', $v_dd['diskon_kode'])->where('jenis_kartu_kode', $v_dmb['kode_jenis_kartu'])->first();

                            //             if ( $d_djk ) {
                            //                 $hitung = 1;

                            //                 break;
                            //             }
                            //         }
                            //     }
                            // }

                            if ( $v_jual['exclude'] == 1 ) {
                                $tot_sc += $v_jual['nilai_service_charge'];
                                $tot_ppn += $v_jual['nilai_ppn'];
                            }

                            if ( $k_jual == count($d_jual)-1 ) {
                                if ( $hitung == 1 ) {
                                    if ( $d_diskon->status_ppn == 1 ) {
                                        $ppn = ($d_diskon->ppn > 0) ? $d_diskon->ppn/100 : 0;
                                    }

                                    if ( $d_diskon->status_service_charge == 1 ) {
                                        $sc = ($d_diskon->service_charge > 0) ? $d_diskon->service_charge/100 : 0;
                                    }

                                    $m_dm = new \Model\Storage\DiskonMenu_model();
                                    $sql = "
                                        select 
                                            dbd.jumlah_beli as jumlah_min_beli,
                                            ji_beli.jumlah as jumlah_beli,
                                            ji_beli.jumlah / dbd.jumlah_beli as jumlah_kelipatan,
                                            dbd.jumlah_dapat as jumlah_dapat,
                                            ((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) as jumlah_dapat_diskon,
                                            ji_dapat.harga,
                                            ji_dapat.total,
                                            case
                                                when dbd.menu_kode_beli = dbd.menu_kode_dapat then
                                                    case
                                                        when (ji_beli.jumlah % dbd.jumlah_beli) > ((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) then
                                                            case
                                                                when dbd.diskon_jenis_dapat = 'persen' then
                                                                    (((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) * ji_dapat.harga) * (dbd.diskon_dapat / 100)
                                                                else
                                                                    (((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) * ji_dapat.harga) - dbd.diskon_dapat
                                                            end
                                                        else
                                                            case
                                                                when dbd.diskon_jenis_dapat = 'persen' then
                                                                    ((ji_beli.jumlah % dbd.jumlah_beli) * ji_dapat.harga) * (dbd.diskon_dapat / 100)
                                                                else
                                                                    ((ji_beli.jumlah % dbd.jumlah_beli) * ji_dapat.harga) - dbd.diskon_dapat
                                                            end
                                                    end
                                                else
                                                    case
                                                        when ji_dapat.jumlah > ((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) then
                                                            case
                                                                when dbd.diskon_jenis_dapat = 'persen' then
                                                                    (((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) * ji_dapat.harga) * (dbd.diskon_dapat / 100)
                                                                else
                                                                    (((ji_beli.jumlah / dbd.jumlah_beli) * dbd.jumlah_dapat) * ji_dapat.harga) - dbd.diskon_dapat
                                                            end
                                                        else
                                                            case
                                                                when dbd.diskon_jenis_dapat = 'persen' then
                                                                    (ji_dapat.jumlah * ji_dapat.harga) * (dbd.diskon_dapat / 100)
                                                                else
                                                                    (ji_dapat.jumlah * ji_dapat.harga) - dbd.diskon_dapat
                                                            end
                                                    end
                                            end as diskon,
                                            ji_dapat.exclude,
                                            ji_dapat.include
                                        from diskon_beli_dapat dbd 
                                        right join
                                            (
                                                select 
                                                    jm.id as id_jenis_menu,
                                                    jm.nama as nama_jenis_menu,
                                                    ji.menu_kode, 
                                                    ji.menu_nama, 
                                                    ji.kode_jenis_pesanan,
                                                    jp.exclude,
                                                    jp.include,
                                                    ji.harga,
                                                    sum(ji.jumlah) as jumlah, 
                                                    sum(ji.total) as total,
                                                    case
                                                        when jp.exclude = 1 then
                                                            0
                                                        when jp.include = 1 then
                                                            sum(ji.service_charge)
                                                    end as service_charge,
                                                    case
                                                        when jp.exclude = 1 then
                                                            0
                                                        when jp.include = 1 then
                                                            sum(ji.ppn)
                                                    end as ppn
                                                from jual_item ji
                                                right join
                                                    (
                                                        select j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                                                        UNION ALL
                                                        select jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                                                    ) jual
                                                    on
                                                        jual.kode_faktur = ji.faktur_kode 
                                                right join
                                                    menu m
                                                    on
                                                        m.kode_menu = ji.menu_kode
                                                right join
                                                    jenis_pesanan jp
                                                    on
                                                        jp.kode = ji.kode_jenis_pesanan
                                                right join
                                                    jenis_menu jm
                                                    on
                                                        jm.id = m.jenis_menu_id
                                                where
                                                    ji.jumlah > 0
                                                group by
                                                    jm.id,
                                                    jm.nama,
                                                    ji.kode_jenis_pesanan,
                                                    jp.exclude,
                                                    jp.include,
                                                    ji.harga,
                                                    ji.menu_kode, 
                                                    ji.menu_nama
                                            ) ji_beli
                                            on
                                                (dbd.jenis_menu_id_beli = 'all' or dbd.jenis_menu_id_beli = ji_beli.id_jenis_menu)
                                                and
                                                (dbd.menu_kode_beli = 'all' or dbd.menu_kode_beli = ji_beli.menu_kode)
                                        right join
                                            (
                                                select 
                                                    jm.id as id_jenis_menu,
                                                    jm.nama as nama_jenis_menu,
                                                    ji.menu_kode, 
                                                    ji.menu_nama, 
                                                    ji.kode_jenis_pesanan,
                                                    jp.exclude,
                                                    jp.include,
                                                    ji.harga,
                                                    sum(ji.jumlah) as jumlah, 
                                                    sum(ji.total) as total,
                                                    case
                                                        when jp.exclude = 1 then
                                                            0
                                                        when jp.include = 1 then
                                                            sum(ji.service_charge)
                                                    end as service_charge,
                                                    case
                                                        when jp.exclude = 1 then
                                                            0
                                                        when jp.include = 1 then
                                                            sum(ji.ppn)
                                                    end as ppn
                                                from jual_item ji
                                                right join
                                                    (
                                                        select j.kode_faktur as kode_faktur from jual j where j.kode_faktur = '".$kode_faktur."'
                                                        UNION ALL
                                                        select jg.faktur_kode_gabungan as kode_faktur from jual_gabungan jg where jg.faktur_kode = '".$kode_faktur."'
                                                    ) jual
                                                    on
                                                        jual.kode_faktur = ji.faktur_kode 
                                                right join
                                                    menu m
                                                    on
                                                        m.kode_menu = ji.menu_kode
                                                right join
                                                    jenis_pesanan jp
                                                    on
                                                        jp.kode = ji.kode_jenis_pesanan
                                                right join
                                                    jenis_menu jm
                                                    on
                                                        jm.id = m.jenis_menu_id
                                                where
                                                    ji.jumlah > 0
                                                group by
                                                    jm.id,
                                                    jm.nama,
                                                    ji.kode_jenis_pesanan,
                                                    jp.exclude,
                                                    jp.include,
                                                    ji.harga,
                                                    ji.menu_kode, 
                                                    ji.menu_nama
                                            ) ji_dapat
                                            on
                                                (dbd.jenis_menu_id_dapat = 'all' or dbd.jenis_menu_id_dapat = ji_dapat.id_jenis_menu)
                                                and
                                                (dbd.menu_kode_dapat = 'all' or dbd.menu_kode_dapat = ji_dapat.menu_kode)
                                        where
                                            dbd.diskon_kode = '".$v_dd['diskon_kode']."' and
                                            ji_beli.jumlah >= dbd.jumlah_beli
                                    ";
                                    $d_dm = $m_dm->hydrateRaw( $sql );

                                    if ( $d_dm->count() > 0 ) {
                                        $d_dm = $d_dm->toArray();

                                        $idx = 0;
                                        foreach ($d_dm as $k_dm => $v_dm) {
                                            $diskon = $v_dm['diskon'];

                                            $tot_diskon += $diskon;
                                            $tot_diskon_by_kode += $diskon;
                                            $tot_belanja -= $diskon;
                                        }

                                        $data_diskon[ $v_dd['id'] ] = array(
                                            'id' => $v_dd['id'],
                                            'kode' => $v_dd['diskon_kode'],
                                            'nominal' => $tot_diskon_by_kode
                                        );
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ( $v_jual['exclude'] == 1 ) {
                        $tot_ppn = $v_jual['nilai_ppn'];
                        $tot_sc = $v_jual['nilai_service_charge'];
                    }
                }
            }
        }

        $_data_diskon = array(
            'data_diskon' => $data_diskon,
            'total_belanja' => ($tot_belanja > 0) ? $tot_belanja : 0,
            'total_diskon' => $tot_diskon,
            'total_service_charge' => $tot_sc,
            'total_ppn' => $tot_ppn,
            'jenis_harga_exclude' => $jenis_harga_exclude,
            'jenis_harga_include' => $jenis_harga_include
        );

        return $_data_diskon;
    }

    public function prosesUpdatePenjualan($kode_faktur, $harga_hpp)
    {
        try {
            $jual = null;
            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select 
                    j.*
                from jual j 
                where 
                    j.kode_faktur = '".$kode_faktur."' and
                    j.mstatus = 1
            ";
            $d_jual = $m_conf->hydrateRaw( $sql );

            if ( $d_jual->count() > 0 ) {
                $jual = $d_jual->toArray()[0];

                $service_charge = 0;
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select * from service_charge sc
                    where
                        sc.mstatus = 1 and
                        sc.branch_kode = '".$jual['branch']."'
                ";
                $d_sc = $m_conf->hydrateRaw( $sql );

                if ( $d_sc->count() > 0 ) {
                    $d_sc = $d_sc->toArray()[0];

                    $service_charge = $d_sc['nilai'];
                }

                $ppn = 0;
                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select * from ppn p
                    where
                        p.mstatus = 1 and
                        p.branch_kode = '".$jual['branch']."'
                ";
                $d_pp = $m_conf->hydrateRaw( $sql );

                if ( $d_pp->count() > 0 ) {
                    $d_pp = $d_pp->toArray()[0];

                    $ppn = $d_pp['nilai'];
                }

                $m_conf = new \Model\Storage\Conf();
                $sql = "
                    select 
                        ji.*,
                        jp.exclude,
                        jp.include,
                        m.service_charge as status_service_charge,
                        m.ppn as status_ppn,
                        hm.harga as harga_jual
                    from jual_item ji
                    right join
                        jenis_pesanan jp
                        on
                            jp.kode = ji.kode_jenis_pesanan
                    right join
                        menu m
                        on
                            m.kode_menu = ji.menu_kode
                    right join
                        harga_menu hm
                        on
                            m.kode_menu = hm.menu_kode and
                            jp.kode = hm.jenis_pesanan_kode and
                            hm.tgl_mulai <= GETDATE()
                    right join
                        (
                            select * from (
                                select 
                                    j.kode_faktur as kode_faktur,
                                    j.kode_faktur as kode_faktur_utama,
                                    j.tgl_trans
                                from jual j 
                                where 
                                    j.kode_faktur = '".$kode_faktur."' and
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
                                            j.kode_faktur = '".$kode_faktur."' and
                                            j.mstatus = 1
                                        group by
                                            j.kode_faktur,
                                            j.tgl_trans
                                    ) j
                                    on
                                        j.kode_faktur = jg.faktur_kode
                                where
                                    j1.mstatus = 1
                                group by
                                    jg.faktur_kode_gabungan,
                                    jg.faktur_kode,
                                    j.tgl_trans
                            ) jl1
                            where
                                jl1.kode_faktur is not null
                        ) j
                        on
                            ji.faktur_kode = j.kode_faktur
                ";
                $d_ji = $m_conf->hydrateRaw( $sql );

                $data_faktur = null;

                if ( $d_ji->count() > 0 ) {
                    $d_ji = $d_ji->toArray();

                    foreach ($d_ji as $k_ji => $v_ji) {
                        $jumlah = $v_ji['jumlah'];
                        $harga = $v_ji['harga'];
                        $total = $v_ji['total'];
                        $total_service_charge = $v_ji['service_charge'];
                        $total_ppn = $v_ji['ppn'];
                        $exclude = $v_ji['exclude'];
                        $include = $v_ji['include'];
                        $status_service_charge = $v_ji['status_service_charge'];
                        $status_ppn = $v_ji['status_ppn'];
                        $harga_jual = $v_ji['harga_jual'];
                        $grand_total = 0;
                        if ( $harga_hpp == 1 ) {
                            $_service_charge = ($total_service_charge > 0) ? $total_service_charge / $jumlah : 0;
                            $_ppn = ($total_ppn > 0) ? $total_ppn / $jumlah : 0;

                            $total_service_charge = 0;
                            $total_ppn = 0;

                            $harga = $harga - ($_service_charge + $_ppn);
                            $total = $harga * $jumlah;

                            $grand_total = $total;
                        } else {
                            $harga = $harga_jual;
                            if ( $include == 1 ) {
                                $total = $harga * $jumlah;

                                $grand_total = $total;

                                $pembagi = (100 + $service_charge) + ((100 + $service_charge) * ($ppn/100));

                                $_total = $total / ($pembagi / 100);

                                $total_service_charge = $_total * ($service_charge/100);
                                $total_ppn = ($_total + $total_service_charge) * ($ppn/100);
                            } else if ( $exclude == 1 ) {
                                $total = $harga * $jumlah;
                                $total_service_charge = ($status_service_charge == 1 && $service_charge > 0) ? round($total * ($service_charge / 100), 2) : 0;
                                $total_ppn = ($status_ppn == 1 && $ppn > 0) ? round(($total + $total_service_charge) * ($ppn / 100), 2) : 0;

                                $grand_total = $total + $total_service_charge + $total_ppn;
                            }
                        }

                        $m_ji = new \Model\Storage\JualItem_model();
                        $m_ji->where('kode_faktur_item', $v_ji['kode_faktur_item'])->update(
                            array(
                                'harga' => $harga,
                                'total' => $grand_total,
                                'service_charge' => $total_service_charge,
                                'ppn' => $total_ppn,
                            )
                        );

                        if ( !isset($data_faktur[ $v_ji['faktur_kode'] ]) ) {
                            $data_faktur[ $v_ji['faktur_kode'] ] = array(
                                'kode_faktur' => $v_ji['faktur_kode'],
                                'total' => $total,
                                'service_charge' => $total_service_charge,
                                'ppn' => $total_ppn,
                                'grand_total' => $grand_total,
                            );
                        } else {
                            $data_faktur[ $v_ji['faktur_kode'] ]['total'] += $total;
                            $data_faktur[ $v_ji['faktur_kode'] ]['service_charge'] += $total_service_charge;
                            $data_faktur[ $v_ji['faktur_kode'] ]['ppn'] += $total_ppn;
                            $data_faktur[ $v_ji['faktur_kode'] ]['grand_total'] += $grand_total;
                        }
                    }
                }

                if ( !empty($data_faktur) ) {
                    foreach ($data_faktur as $k_df => $v_df) {
                        $m_jual = new \Model\Storage\Jual_model();
                        $m_jual->where('kode_faktur', $v_df['kode_faktur'])->update(
                            array(
                                'total' => $v_df['total'],
                                'service_charge' => $v_df['service_charge'],
                                'ppn' => $v_df['ppn'],
                                'grand_total' => $v_df['grand_total'],
                            )
                        );
                    }
                }
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        return $this->result;
    }

    public function cekPinOtorisasi()
    {
        $pin = $this->input->post('pin');

        try {
            $idFitur = getIdFitur( $this->current_base_uri );

            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po = $m_po->where('pin', $pin)->where('id_detfitur', $idFitur)->where('status', 1)->first();

            if ( $d_po ) {
                $this->result['status'] = 1;
                $this->result['content'] = array(
                    'id_verifikasi' => $d_po->user_id
                );
            } else {
                $this->result['message'] = "PIN Otorisasi yang anda masukkan tidak di temukan.";
            }
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $data_diskon = $this->hitDiskon('FAK-2309090292', 458621);
        // $data_diskon = $this->hitDiskon('FAK-2302260182', 400867);

        cetak_r( $data_diskon );

        // $idFitur = getIdFitur( $this->current_base_uri );

        // cetak_r( substr($this->current_base_uri, 1) );
        // cetak_r( $idFitur );

        // $id_bayar_diskon = array(1942, 1941);

        // $m_bd = new \Model\Storage\BayarDiskon_model();
        // $d_bd = $m_bd->whereIn('id', $id_bayar_diskon)->get();

        // cetak_r( $d_bd->toJson() );
    }
}
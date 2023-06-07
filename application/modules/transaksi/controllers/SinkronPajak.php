<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SinkronPajak extends Public_Controller {

    private $pathView = 'transaksi/sinkron_pajak/';
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
    public function index($segment=0)
    {
        if ( $this->hakAkses['a_view'] == 1 ) {
            $this->add_external_js(array(
                "assets/select2/js/select2.min.js",
                "assets/transaksi/sinkron_pajak/js/sinkron-pajak.js"
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/transaksi/sinkron_pajak/css/sinkron-pajak.css"
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['branch'] = $this->getBranch();
            $content['title_panel'] = 'Sinkron Pajak';

            // Load Indexx
            $data['title_menu'] = 'Sinkron Pajak';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
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

    public function getData($kode_branch, $start_date, $end_date)
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                min(data.tgl_trans) as tgl_trans,
                data.kode_faktur_utama as kode_faktur,
                sum(data.grand_total) as grand_total,
                -- sum(data.grand_total_gabungan) as grand_total_gabungan,
                sum(data.ppn) as ppn
            from (
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
                    max(_data.status_gabungan) as status_gabungan,
                    _data.ppn as ppn
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
                        sum(ji.ppn) as ppn
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
                    left join
                        jual_item ji
                        on
                            ji.faktur_kode = j.kode_faktur
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
                        1 as status_gabungan,
                        sum(ji.ppn) as ppn
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
                    left join
                        jual_item ji
                        on
                            ji.faktur_kode = j.kode_faktur
                    group by
                        j.tgl_trans,
                        j.mstatus,
                        _jg.member,
                        p.kode_pesanan,
                        j.kode_faktur,
                        _jg.faktur_kode,
                        p.nama_user,
                        _jg.nama_kasir,
                        j.grand_total,
                        jg.id
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
                    _data.grand_total,
                    jg.grand_total_gabungan,
                    _data.ppn
            ) data
        where
            data.ppn > 0
        group by
            data.kode_faktur_utama
        order by
            data.kode_faktur_utama asc
        ";
        $d_real = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_real->count() > 0 ) {
            $data = $d_real->toArray();
        }

        return $data;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $kode_branch = $params['branch'];
            $start_date = $params['start_date'].' 00:00:00.000';
            $end_date = $params['end_date'].' 23:59:59.999';

            $data_real = $this->getData($kode_branch, $start_date, $end_date);

            if ( stristr($kode_branch, 'gtr') ) {
                $m_jual = new \Model\Storage\Pajak\JualGtr_model();
            } else {
                $m_jual = new \Model\Storage\Pajak\JualTr_model();
            }
            $d_pajak = $m_jual->whereBetween('TGL_TRANSAKSI', [$start_date, $end_date])->where('TAX_PAD', '>', 0)->orderBy('NO_BILL', 'asc')->get();

            $data_pajak = null;
            if ( $d_pajak->count() > 0 ) {
                $data_pajak = $d_pajak->toArray();
            }

            $content_real['data'] = $data_real;
            $content_pajak['data'] = $data_pajak;

            $html_real = $this->load->view($this->pathView . 'list_real', $content_real, TRUE);
            $html_pajak = $this->load->view($this->pathView . 'list_pajak', $content_pajak, TRUE);

            $this->result['status'] = 1;
            $this->result['html'] = array(
                'real' => $html_real,
                'pajak' => $html_pajak
            );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function sinkron()
    {
        $params = $this->input->post('params');

        try {
            $kode_branch = $params['branch'];
            $start_date = $params['start_date'].' 00:00:00.000';
            $end_date = $params['end_date'].' 23:59:59.999';

            $data_real = $this->getData($kode_branch, $start_date, $end_date);

            if ( stristr($kode_branch, 'gtr') ) {
                $m_jual = new \Model\Storage\Pajak\JualGtr_model();
            } else {
                $m_jual = new \Model\Storage\Pajak\JualTr_model();
            }

            $m_jual->whereBetween('TGL_TRANSAKSI', [$start_date, $end_date])->delete();

            if ( !empty($data_real) ) {
                foreach ($data_real as $key => $value) {
                    if ( stristr($kode_branch, 'gtr') ) {
                        $m_jual = new \Model\Storage\Pajak\JualGtr_model();
                    } else {
                        $m_jual = new \Model\Storage\Pajak\JualTr_model();
                    }
                    if ( $value['ppn'] > 0 ) {
                        $m_jual->NO_BILL = $value['kode_faktur'];
                        $m_jual->TGL_TRANSAKSI = $value['tgl_trans'];
                        $m_jual->TOTAL_TRANSAKSI = $value['grand_total'];
                        $m_jual->TAX_PAD = $value['ppn'];
                        $m_jual->save();
                    }
                }

                $this->result['status'] = 1;
                $this->result['message'] = 'Data berhasil di sinkron.';
            } else {
                $this->result['message'] = 'Tidak ada data penjualan yg akan di sinkron.';
            }

        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HistoryTransaksi extends Public_Controller {

    private $pathView = 'report/history_transaksi/';
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
                    'assets/report/history_transaksi/js/history-transaksi.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/history_transaksi/css/history-transaksi.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['branch'] = $this->getBranch();
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan History Transaksi';
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
            $branch = $params['branch'];
            $tanggal = $params['tanggal'];

            $start_date = $tanggal.' 00:00:00.001';
            $end_date = $tanggal.' 23:59:59.999';

            $m_conf = new \Model\Storage\Conf();
            // $sql = "
            //     select
            //         lt.*,
            //         tbl.kode_faktur,
            //         j.pesanan_kode
            //     from log_tables lt
            //     left join
            //         (
            //             select
            //                 jl1.kode_faktur,
            //                 jl1.tbl_id,
            //                 'jual' as tbl_name
            //             from (
            //                 select 
            //                     j.kode_faktur as tbl_id,
            //                     j.kode_faktur as kode_faktur,
            //                     j.kode_faktur as kode_faktur_utama,
            //                     j.tgl_trans
            //                 from jual j 
            //                 where 
            //                     j.tgl_trans between '".$start_date."' and '".$end_date."' and
            //                     j.branch = '".$branch."' and
            //                     j.mstatus = 1
            //                 group by
            //                     j.kode_faktur,
            //                     j.tgl_trans

            //                 UNION ALL

            //                 select 
            //                     jg.faktur_kode_gabungan as tbl_id,
            //                     jg.faktur_kode_gabungan as kode_faktur,
            //                     jg.faktur_kode as kode_faktur_utama,
            //                     j.tgl_trans
            //                 from jual_gabungan jg
            //                 right join
            //                     jual j1
            //                     on
            //                         j1.kode_faktur = jg.faktur_kode_gabungan
            //                 right join
            //                     (
            //                         select 
            //                             j.kode_faktur as kode_faktur,
            //                             j.tgl_trans
            //                         from jual j 
            //                         where 
            //                             j.tgl_trans between '".$start_date."' and '".$end_date."' and
            //                             j.branch = '".$branch."' and
            //                             j.mstatus = 1
            //                         group by
            //                             j.kode_faktur,
            //                             j.tgl_trans
            //                     ) j
            //                     on
            //                         j.kode_faktur = jg.faktur_kode
            //                 where
            //                     j1.mstatus = 1
            //                 group by
            //                     jg.faktur_kode_gabungan,
            //                     jg.faktur_kode,
            //                     j.tgl_trans
            //             ) jl1
            //             where
            //                 jl1.tbl_id is not null

            //             union all

            //             select
            //                 b.faktur_kode as kode_faktur,
            //                 cast(b.id as varchar(15)) as tbl_id,
            //                 'bayar' as tbl_name
            //             from bayar b
            //             right join
            //                 jual j
            //                 on
            //                     b.faktur_kode = j.kode_faktur
            //             where
            //                 b.mstatus = 1 and
            //                 j.tgl_trans between '".$start_date."' and '".$end_date."'

            //             union all

            //             select
            //                 j.kode_faktur,
            //                 p.kode_pesanan as tbl_id,
            //                 'pesanan' as tbl_name
            //             from pesanan p
            //             right join
            //                 jual j
            //                 on
            //                     p.kode_pesanan = j.pesanan_kode
            //             where
            //                 p.mstatus = 0 and
            //                 j.tgl_trans between '".$start_date."' and '".$end_date."'
            //         ) tbl
            //         on
            //             tbl.tbl_id = lt.tbl_id and
            //             tbl.tbl_name = lt.tbl_name
            //     left join
            //         jual j
            //         on
            //             j.kode_faktur = tbl.kode_faktur
            //     where
            //         lt.tbl_id is not null and
            //         lt.kode_faktur is not null and
            //         lt.waktu between '".$start_date."' and '".$end_date."'
            //     order by
            //         tbl.kode_faktur desc,
            //         lt.waktu desc
            // ";
            $sql = "
                select
                    lt.*,
                    j.kode_faktur,
                    j.pesanan_kode
                from log_tables lt
                right join
                    jual j
                    on
                        j.kode_faktur = lt.tbl_id
                where
                    lt.waktu between '".$start_date."' and '".$end_date."' and
                    j.branch = '".$branch."'
                order by
                    lt.tbl_id desc,
                    lt.waktu desc
            ";
            $d_conf = $m_conf->hydrateRaw( $sql );

            $data = null;
            if ( $d_conf->count() > 0 ) {
                $data = $d_conf->toArray();
            }

            $content['data'] = $data;
            $html = $this->load->view($this->pathView . 'list', $content, TRUE);

            $this->result['status'] = 1;
            $this->result['content'] = array(
                'list_report' => $html
            );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
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

            $content['report_hutang'] = $this->load->view($this->pathView . 'report_hutang', null, TRUE);
            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Hutang Pelanggan';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getDataHutang($start_date, $end_date)
    {
        $data = null;

        $m_jual = new \Model\Storage\Jual_model();
        $sql = "
            select j.*, p.tgl_pesan from jual j
            right join
                pesanan p
                on
                    j.pesanan_kode = p.kode_pesanan
            where
                j.tgl_trans between '".$start_date."' and '".$end_date."' and
                (j.hutang = 1 or j.lunas = 0) and
                j.mstatus = 1
        ";

        $d_jual_hutang = $m_jual->hydrateRaw( $sql );

        if ( $d_jual_hutang->count() > 0 ) {
            $d_jual_hutang = $d_jual_hutang->toArray();

            foreach ($d_jual_hutang as $key => $value) {
                $sql = "select sum(bayar) as total_bayar from bayar_hutang bh 
                    left join
                        bayar b 
                        on
                            bh.id_header = b.id
                    where
                        b.mstatus = 1 and
                        bh.faktur_kode = '".$value['kode_faktur']."'
                ";

                $m_bayar_hutang = new \Model\Storage\BayarHutang_model();
                $d_bayar_hutang = $m_bayar_hutang->hydrateRaw($sql);

                $total_bayar = 0;
                if ( $d_bayar_hutang->count() > 0 ) {
                    $total_bayar = $d_bayar_hutang->toArray()[0]['total_bayar'];
                }

                $tgl = !empty($value['tgl_pesan']) ? $value['tgl_pesan'] : $value['tgl_trans'];

                $key = str_replace('-', '', $tgl).' | '.$value['kode_faktur'].' | '.$value['member'];

                $member_group = null;

                if ( !empty($value['kode_member']) ) {
                    $m_member = new \Model\Storage\Member_model();
                    $d_member = $m_member->where('kode_member', $value['kode_member'])->with(['member_group'])->first()->toArray();

                    if ( !empty($d_member['member_group']) ) {
                        $member_group = $d_member['member_group']['nama'];
                    }
                }

                $data[ $key ] = array(
                    'member_group' => $member_group,
                    'member' => $value['member'],
                    'nama_kasir' => $value['nama_kasir'],
                    'tgl_pesan' => $tgl,
                    'faktur_kode' => $value['kode_faktur'],
                    'hutang' => $value['grand_total'],
                    'bayar' => $total_bayar,
                    'remark' => $value['remark']
                );
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

        $data = $this->getDataHutang( $start_date, $end_date );

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list_report_hutang', $content, TRUE);

        echo $html;
    }
}
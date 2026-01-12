<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PosisiStok extends Public_Controller {

    private $pathView = 'report/posisi_stok/';
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
                    'assets/report/posisi_stok/js/posisi-stok.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/report/posisi_stok/css/posisi-stok.css'
                )
            );
            $data = $this->includes;

            $content['report'] = $this->load->view($this->pathView . 'report', null, TRUE);
            $content['gudang'] = $this->getGudang();
            $content['item'] = $this->getItem();
            $content['group_item'] = $this->getGroupItem();

            $content['akses'] = $this->hakAkses;

            $data['title_menu'] = 'Laporan Posisi Stok';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getGudang()
    {
        $m_gdg = new \Model\Storage\Gudang_model();
        $d_gdg = $m_gdg->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_gdg->count() > 0 ) {
            $data = $d_gdg->toArray();
        }

        return $data;
    }

    public function getItem()
    {
        $m_item = new \Model\Storage\Item_model();
        $d_item = $m_item->with(['group'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $data_item = $d_item->toArray();
        }

        return $data_item;
    }

    public function getGroupItem()
    {
        $m_gi = new \Model\Storage\GroupItem_model();
        $d_gi = $m_gi->orderBy('nama', 'asc')->get();

        $data_gi = null;
        if ( $d_gi->count() > 0 ) {
            $data_gi = $d_gi->toArray();
        }

        return $data_gi;
    }

    public function getLists()
    {
        $params = $this->input->post('params');

        try {
            $tgl_stok_opname = $this->config->item('tgl_stok_opname');

            $start_date = $params['start_date'];
            $end_date = $params['end_date'];
            $gudang = $params['gudang'];
            $item = $params['item'];
            $group_item = $params['group_item'];

            $m_stokt = new \Model\Storage\StokTanggal_model();
            $d_stokt = $m_stokt->whereBetween('tanggal', [$start_date, $end_date])->where('gudang_kode', $gudang)->with(['gudang'])->orderBy('tanggal', 'asc')->get();

            $data = null;
            if ( $d_stokt->count() > 0 ) {
                $data = $d_stokt->toArray();
            }

            $mappingDataReport = $this->mappingDataReport( $data, $item, $gudang, $group_item );

            $content_report['data'] = $mappingDataReport;
            $html_report = $this->load->view($this->pathView . 'list', $content_report, TRUE);

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

    public function mappingDataReport($_data, $_item, $_gudang, $_group_item)
    {
        $data = null;
        if ( !empty($_data) ) {
            foreach ($_data as $k_data => $v_data) {
                $data[ $v_data['gudang_kode'] ]['kode'] = $v_data['gudang_kode'];
                $data[ $v_data['gudang_kode'] ]['nama'] = $v_data['gudang']['nama'];

                $sql_item = null;
                if ( !in_array('all', $_item) ) {
                    $sql_item = "and s.item_kode in ('".implode("', '", $_item)."')";
                }

                $sql_group_item = null;
                if ( !in_array('all', $_group_item) ) {
                    $sql_group_item = "and gi.kode in ('".implode("', '", $_group_item)."')";
                }

                $id_stok_tanggal = $v_data['id'];
                $kode_gudang = $v_data['gudang_kode'];

                $conf = new \Model\Storage\Conf();
                $sql = "
                    select 
                        s.id_header,
                        s.item_kode,
                        st.tanggal,
                        sum(s.sisa_stok) as sisa_stok,
                        i.nama as nama,
                        i.group_kode,
                        gi.nama as nama_group,
                        isatuan.satuan
                    from stok s
                    right join
                        stok_tanggal st
                        on
                            s.id_header = st.id
                    right join
                        item i
                        on
                            s.item_kode = i.kode
                    right join
                        group_item gi
                        on
                            i.group_kode = gi.kode
                    right join
                        (
                            select is1.* from item_satuan is1
                            right join
                                (
                                    select max(id) as id, item_kode from item_satuan where pengali = 1 group by item_kode
                                ) is2
                                on
                                    is1.id = is2.id

                        ) isatuan
                        on
                            i.kode = isatuan.item_kode
                    where
                        s.id_header = $id_stok_tanggal and
                        s.gudang_kode = '$kode_gudang'
                        ".$sql_item."
                        ".$sql_group_item."
                    group by
                        s.id_header,
                        s.item_kode,
                        st.tanggal,
                        i.nama,
                        i.group_kode,
                        gi.nama,
                        isatuan.satuan
                ";

                $d_conf_stok = $conf->hydrateRaw($sql);

                if ( $d_conf_stok->count() > 0 ) {
                    $d_conf_stok = $d_conf_stok->toArray();

                    foreach ($d_conf_stok as $k_det => $v_det) {
                        $id_header = $v_det['id_header'];
                        $item_kode = $v_det['item_kode'];
                        $tanggal = substr($v_det['tanggal'], 0, 10);

                        $conf = new \Model\Storage\Conf();
                        $sql = "
                            select * from stok_harga sh
                            where
                                sh.id_header = $id_header and
                                sh.item_kode = '$item_kode'
                        ";
                        $d_harga = $conf->hydrateRaw($sql);

                        $harga_beli = 0;
                        if ( $d_harga->count() > 0 ) {
                            $d_harga = $d_harga->toArray();

                            $harga_beli = $d_harga[0]['harga'];
                        }

                        $key_item = $v_det['nama'].' | '.$v_det['item_kode'];

                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['kode'] = $v_det['group_kode'];
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['nama'] = $v_det['nama_group'];

                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['kode'] = $v_det['item_kode'];
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['nama'] = $v_det['nama'];
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['satuan'] = $v_det['satuan'];

                        $key_tanggal = str_replace('-', '', substr($v_data['tanggal'], 0, 10));

                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['tanggal'] = $v_data['tanggal'];
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['jumlah'] = $v_det['sisa_stok'];
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['harga'] = $harga_beli;
                        $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['nilai_stok'] = $v_det['sisa_stok'] * $harga_beli;

                        ksort( $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'] );
                        ksort( $data[ $v_data['gudang_kode'] ]['group_item'][ $v_det['group_kode'] ]['detail'][ $key_item ]['detail_tanggal'] );
                    }
                }
                
                $conf = new \Model\Storage\Conf();
                $sql = "
                    select top 1
                        i.kode,
                        i.nama,
                        i.group_kode,
                        gi.nama as group_nama,
                        st.satuan
                    from item i
                    right join
                        group_item gi
                        on
                            i.group_kode = gi.kode
                    left join
                        item_satuan st
                        on
                            st.item_kode = i.kode
                    where
                        i.kode in ('".implode("', '", $_item)."') and
                        st.pengali = 1
                ";
                $d_item = $conf->hydrateRaw($sql);

                if ( $d_item->count() > 0 ) {
                    $d_item = $d_item->toArray();

                    foreach ($d_item as $k_item => $v_item) {
                        $nama_item = $v_item['nama'];
                        $satuan = $v_item['satuan'];
                        $group_kode = $v_item['group_kode'];
                        $group_nama = $v_item['group_nama'];

                        $conf = new \Model\Storage\Conf();
                        $sql = "
                            select top 1
                                * 
                            from stok_harga sh
                            where
                                sh.id_header = ".$id_stok_tanggal." and
                                sh.item_kode = '".$v_item['kode']."'
                        ";
                        $d_harga = $conf->hydrateRaw($sql);

                        $harga = 0;
                        if ( $d_harga->count() > 0 ) {
                            $harga = $d_harga->toArray()[0]['harga'];
                        }

                        $key_item = $nama_item.' | '.$v_item['kode'];

                        if ( !isset($data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]) ) {
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['kode'] = $group_kode;
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['nama'] = $group_nama;

                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['kode'] = $v_item['kode'];
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['nama'] = $nama_item;
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['satuan'] = $satuan;

                            $key_tanggal = str_replace('-', '', substr($v_data['tanggal'], 0, 10));

                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['tanggal'] = $v_data['tanggal'];
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['jumlah'] = 0;
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['harga'] = $harga;
                            $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['detail_tanggal'][ $key_tanggal ]['nilai_stok'] = 0;

                            ksort( $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'] );
                            ksort( $data[ $v_data['gudang_kode'] ]['group_item'][ $group_kode ]['detail'][ $key_item ]['detail_tanggal'] );
                        }
                    }
                }
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

        $tgl_stok_opname = $this->config->item('tgl_stok_opname');

        $start_date = $_data_params['start_date'];
        $end_date = $_data_params['end_date'];
        $gudang = $_data_params['gudang'];
        $item = $_data_params['item'];
        $group_item = $_data_params['group_item'];

        $m_stokt = new \Model\Storage\StokTanggal_model();
        $d_stokt = $m_stokt->whereBetween('tanggal', [$start_date, $end_date])->where('gudang_kode', $gudang)->with(['gudang'])->orderBy('tanggal', 'asc')->get();

        $data = null;
        if ( $d_stokt->count() > 0 ) {
            $data = $d_stokt->toArray();
        }

        $mappingDataReport = $this->mappingDataReport( $data, $item, $gudang, $group_item );

        $content['data'] = $mappingDataReport;
        $res_view_html = $this->load->view('report/posisi_stok/export_excel', $content, true);

        $filename = 'export-posisi-stok-'.str_replace('-', '', $_data_params['start_date']).str_replace('-', '', $_data_params['end_date']).'.xls';

        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=".$filename."");
        echo $res_view_html;
    }
}
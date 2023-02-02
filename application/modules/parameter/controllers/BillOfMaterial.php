<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BillOfMaterial extends Public_Controller {

    private $pathView = 'parameter/bom/';
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
                "assets/parameter/bom/js/bom.js",
            ));
            $this->add_external_css(array(
                "assets/select2/css/select2.min.css",
                "assets/parameter/bom/css/bom.css",
            ));

            $data = $this->includes;

            $content['akses'] = $this->hakAkses;
            $content['add_form'] = $this->addForm();
            $content['title_panel'] = 'Master Bill Of Material';

            $content['riwayat'] = $this->load->view($this->pathView . 'riwayat', NULL, TRUE);

            // Load Indexx
            $data['title_menu'] = 'Master Bill Of Material';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);
            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function getMenu()
    {
        $m_menu = new \Model\Storage\Menu_model();
        $d_menu = $m_menu->where('status', 1)->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        return $data;
    }

    public function getItem()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select * from (
                select items.satuan, items.pengali, cast(i.kode as varchar(20)) as kode, i.nama, 'item' as jenis from item i 
                right join
                    item_satuan items
                    on
                        i.kode = items.item_kode
                        
                union all
                
                select bs.satuan, bs.pengali, cast(b.id as varchar(20)) as kode, b.nama, 'bom' as jenis from bom b 
                right join
                    bom_satuan bs 
                    on
                        b.id = bs.id_header 
                where
                    b.additional = 1
            ) as data
            where
                data.satuan is not null
        ";
        $d_item = $m_conf->hydrateRaw( $sql );

        // $m_item = new \Model\Storage\Item_model();
        // $d_item = $m_item->with(['satuan'])->orderBy('nama', 'asc')->get();

        $data_item = null;
        if ( $d_item->count() > 0 ) {
            $d_item = $d_item->toArray();

            foreach ($d_item as $k_di => $v_di) {
                $data_item[ $v_di['kode'] ]['kode'] = $v_di['kode'];
                $data_item[ $v_di['kode'] ]['nama'] = $v_di['nama'];
                $data_item[ $v_di['kode'] ]['jenis'] = $v_di['jenis'];
                $data_item[ $v_di['kode'] ]['satuan'][] = array(
                    'satuan' => $v_di['satuan'],
                    'pengali' => $v_di['pengali']
                );
            }
        }

        return $data_item;
    }

    public function loadForm()
    {
        $id = $this->input->get('id');
        $resubmit = $this->input->get('resubmit');

        $html = null;
        if ( !empty($id) && empty($resubmit) ) {
            $html = $this->viewForm($id);
        } else if ( !empty($id) && !empty($resubmit) ) {
            $html = $this->editForm($id);
        } else {
            $html = $this->addForm();
        }

        echo $html;
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $menu_kode = $params['menu_kode'];

        $kondisi = '';
        if ( $menu_kode[0] != 'all' ) {
            $kondisi = "and m.kode_menu in ('".implode("', '", $menu_kode)."')";
        }

        $m_bom = new \Model\Storage\Bom_model();
        $sql = "
            select b.id, b.tgl_berlaku, m.nama as nama_menu, b.nama as nama_bom, br.nama as nama_branch from bom b
            left join
                menu m
                on
                    b.menu_kode = m.kode_menu
            left join
                branch br
                on
                    m.branch_kode = br.kode_branch
            where
                b.tgl_berlaku between '".$start_date."' and '".$end_date."'
                ".$kondisi."
            order by
                b.tgl_berlaku desc,
                m.nama asc
        ";
        $d_bom = $m_bom->hydrateRaw( $sql );

        $data = null;
        if ( $d_bom->count() > 0 ) {
            $data = $d_bom->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }

    public function addForm()
    {
        $content['item'] = $this->getItem();
        $content['menu'] = $this->getMenu();

        $html = $this->load->view($this->pathView . 'addForm', $content, TRUE);

        return $html;
    }

    public function viewForm($id)
    {
        // $m_bom = new \Model\Storage\Bom_model();
        // $d_bom = $m_bom->where('id', $id)->with(['menu', 'detail'])->first()->toArray();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                b.id,
                b.nama as nama_bom,
                m.nama as nama_menu,
                b.tgl_berlaku,
                b.additional,
                b.jml_porsi,
                bd.item_kode,
                i.nama,
                i.satuan,
                bd.jumlah
            from bom b
            left join
                menu m
                on
                    b.menu_kode = m.kode_menu
            right join
                bom_det bd
                on
                    b.id = bd.id_header
            right join
                (
                    select * from (
                        select items.satuan, items.pengali, cast(i.kode as varchar(20)) as kode, i.nama, 'item' as jenis from item i 
                        right join
                            item_satuan items
                            on
                                i.kode = items.item_kode
                                
                        union all
                        
                        select bs.satuan, bs.pengali, cast(b.id as varchar(20)) as kode, b.nama, 'bom' as jenis from bom b 
                        right join
                            bom_satuan bs 
                            on
                                b.id = bs.id_header 
                        where
                            b.additional = 1
                    ) as data
                    where
                        data.satuan is not null
                ) i
                on
                    bd.item_kode = i.kode
            where
                b.id = ".$id."
        ";
        $d_bom = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_bom->count() > 0 ) {
            $d_bom = $d_bom->toArray();

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    *
                from bom_satuan bs
                where
                    bs.id_header = ".$id."
            ";
            $d_bom_satuan = $m_conf->hydrateRaw( $sql );

            $data = array(
                'id' => $d_bom[0]['id'],
                'nama_bom' => $d_bom[0]['nama_bom'],
                'nama_menu' => $d_bom[0]['nama_menu'],
                'tgl_berlaku' => $d_bom[0]['tgl_berlaku'],
                'additional' => $d_bom[0]['additional'],
                'jml_porsi' => $d_bom[0]['jml_porsi'],
                'satuan' => ($d_bom_satuan->count() > 0) ? $d_bom_satuan->toArray() : null
            );

            foreach ($d_bom as $k_bom => $v_bom) {
                $data['detail'][ $k_bom ] = array(
                    'item_kode' => $v_bom['item_kode'],
                    'nama' => $v_bom['nama'],
                    'satuan' => $v_bom['satuan'],
                    'jumlah' => $v_bom['jumlah']
                );
            }
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'viewForm', $content, TRUE);

        return $html;
    }

    public function editForm($id)
    {
        // $m_bom = new \Model\Storage\Bom_model();
        // $d_bom = $m_bom->where('id', $id)->with(['menu', 'detail'])->first()->toArray();

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                b.id,
                b.nama as nama_bom,
                m.nama as nama_menu,
                b.tgl_berlaku,
                b.additional,
                b.jml_porsi,
                bd.item_kode,
                i.nama,
                i.satuan,
                bd.jumlah
            from bom b
            left join
                menu m
                on
                    b.menu_kode = m.kode_menu
            right join
                bom_det bd
                on
                    b.id = bd.id_header
            right join
                (
                    select * from (
                        select items.satuan, items.pengali, cast(i.kode as varchar(20)) as kode, i.nama, 'item' as jenis from item i 
                        right join
                            item_satuan items
                            on
                                i.kode = items.item_kode
                                
                        union all
                        
                        select bs.satuan, bs.pengali, cast(b.id as varchar(20)) as kode, b.nama, 'bom' as jenis from bom b 
                        right join
                            bom_satuan bs 
                            on
                                b.id = bs.id_header 
                        where
                            b.additional = 1
                    ) as data
                    where
                        data.satuan is not null
                ) i
                on
                    bd.item_kode = i.kode
            where
                b.id = ".$id."
        ";
        $d_bom = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_bom->count() > 0 ) {
            $d_bom = $d_bom->toArray();

            $m_conf = new \Model\Storage\Conf();
            $sql = "
                select
                    *
                from bom_satuan bs
                where
                    bs.id_header = ".$id."
            ";
            $d_bom_satuan = $m_conf->hydrateRaw( $sql );

            $data = array(
                'id' => $d_bom[0]['id'],
                'nama_bom' => $d_bom[0]['nama_bom'],
                'nama_menu' => $d_bom[0]['nama_menu'],
                'tgl_berlaku' => $d_bom[0]['tgl_berlaku'],
                'additional' => $d_bom[0]['additional'],
                'jml_porsi' => $d_bom[0]['jml_porsi'],
                'satuan' => ($d_bom_satuan->count() > 0) ? $d_bom_satuan->toArray() : null
            );

            foreach ($d_bom as $k_bom => $v_bom) {
                $data['detail'][ $k_bom ] = array(
                    'item_kode' => $v_bom['item_kode'],
                    'nama' => $v_bom['nama'],
                    'satuan' => $v_bom['satuan'],
                    'jumlah' => $v_bom['jumlah']
                );
            }
        }

        $content['data'] = $data;

        $content['item'] = $this->getItem();
        $content['menu'] = $this->getMenu();

        $html = $this->load->view($this->pathView . 'editForm', $content, TRUE);

        return $html;
    }

    public function save()
    {
        $params = $this->input->post('params');

        // cetak_r( $params, 1 );

        try {
            if ( isset($params['menu_kode']) && !empty($params['menu_kode']) ) {
                foreach ($params['menu_kode'] as $k_menu => $v_menu) {
                    $m_bom = new \Model\Storage\Bom_model();
                    $m_bom->tgl_berlaku = $params['tanggal'];
                    $m_bom->menu_kode = $v_menu;
                    $m_bom->additional = $params['additional'];
                    $m_bom->nama = $params['nama'];
                    $m_bom->jml_porsi = $params['jml_porsi'];
                    $m_bom->save();

                    foreach ($params['list_item'] as $k_lm => $v_lm) {
                        $m_bd = new \Model\Storage\BomDet_model();
                        $m_bd->id_header = $m_bom->id;
                        $m_bd->item_kode = $v_lm['item_kode'];
                        $m_bd->satuan = $v_lm['satuan'];
                        $m_bd->pengali = $v_lm['pengali'];
                        $m_bd->jumlah = $v_lm['jumlah'];
                        $m_bd->jenis = $v_lm['jenis'];
                        $m_bd->save();
                    }

                    if ( isset($params['bom_satuan']) && !empty($params['bom_satuan']) ) {
                        foreach ($params['bom_satuan'] as $k_bs => $v_bs) {
                            $m_bs = new \Model\Storage\BomSatuan_model();
                            $m_bs->id_header = $m_bom->id;
                            $m_bs->satuan = $v_bs['satuan'];
                            $m_bs->pengali = $v_bs['pengali'];
                            $m_bs->save();
                        }
                    }

                    $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $m_bom, $deskripsi_log );
                }
            } else {
                $m_bom = new \Model\Storage\Bom_model();
                $m_bom->tgl_berlaku = $params['tanggal'];
                $m_bom->menu_kode = null;
                $m_bom->additional = $params['additional'];
                $m_bom->nama = $params['nama'];
                $m_bom->jml_porsi = $params['jml_porsi'];
                $m_bom->save();

                foreach ($params['list_item'] as $k_lm => $v_lm) {
                    $m_bd = new \Model\Storage\BomDet_model();
                    $m_bd->id_header = $m_bom->id;
                    $m_bd->item_kode = $v_lm['item_kode'];
                    $m_bd->satuan = $v_lm['satuan'];
                    $m_bd->pengali = $v_lm['pengali'];
                    $m_bd->jumlah = $v_lm['jumlah'];
                    $m_bd->jenis = $v_lm['jenis'];
                    $m_bd->save();
                }

                if ( isset($params['bom_satuan']) && !empty($params['bom_satuan']) ) {
                    foreach ($params['bom_satuan'] as $k_bs => $v_bs) {
                        $m_bs = new \Model\Storage\BomSatuan_model();
                        $m_bs->id_header = $m_bom->id;
                        $m_bs->satuan = $v_bs['satuan'];
                        $m_bs->pengali = $v_bs['pengali'];
                        $m_bs->save();
                    }
                }

                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_bom, $deskripsi_log );
            }
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function edit()
    {
        $params = $this->input->post('params');

        try {
            $m_bom = new \Model\Storage\Bom_model();
            $m_bom->where('id', $params['id'])->update(
                array(
                    'tgl_berlaku' => $params['tanggal']
                )
            );

            $m_bd = new \Model\Storage\BomDet_model();
            $m_bd->where('id_header', $params['id'])->delete();

            foreach ($params['list_item'] as $k_lm => $v_lm) {
                $m_bd = new \Model\Storage\BomDet_model();
                $m_bd->id_header = $params['id'];
                $m_bd->item_kode = $v_lm['item_kode'];
                $m_bd->satuan = $v_lm['satuan'];
                $m_bd->pengali = $v_lm['pengali'];
                $m_bd->jumlah = $v_lm['jumlah'];
                $m_bd->save();
            }

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $m_bom, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function delete()
    {
        $params = $this->input->post('params');

        try {
            $m_bom = new \Model\Storage\Bom_model();
            $d_bom = $m_bom->where('id', $params['id'])->first();

            $m_bd = new \Model\Storage\BomDet_model();
            $m_bd->where('id_header', $params['id'])->delete();

            $m_bom->where('id', $params['id'])->delete();

            $deskripsi_log = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/delete', $d_bom, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}
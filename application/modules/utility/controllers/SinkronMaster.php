<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SinkronMaster extends Public_Controller {

    private $pathView = 'utility/sinkron_master/';
    private $url;
    private $hakAkses;

    private $list = array(
        array('table' => 'branch', 'keterangan' => 'BRANCH'),
        array('table' => 'meja', 'keterangan' => 'MEJA'),
        array('table' => 'lantai', 'keterangan' => 'LANTAI'),
        array('table' => 'jenis_menu', 'keterangan' => 'JENIS MENU'),
        array('table' => 'kategori_menu', 'keterangan' => 'KATEGORI MENU'),
        array('table' => 'menu', 'keterangan' => 'MENU'),
        array('table' => 'harga_menu', 'keterangan' => 'HARGA MENU')
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
                    'assets/utility/sinkron_master/js/sinkron-master.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/utility/sinkron_master/css/sinkron-master.css'
                )
            );
            $data = $this->includes;

            $content['branch'] = $this->branch();
            $content['menu'] = $this->list;
            $content['akses'] = $this->hakAkses;

            // $content['data'] = $this->getLists();

            $data['title_menu'] = 'Sinkron Master';
            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        } else {
            showErrorAkses();
        }
    }

    public function branch( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "where kode_branch = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select * from branch b 
            ".$sql_kode_branch."
        ";
        $d_branch = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_branch->count() > 0 ) {
            $data = $d_branch->toArray();
        }

        return $data;
    }

    public function meja( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "and l.branch_kode = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select m.* from meja m
            left join
                lantai l
                on
                    l.id = m.lantai_id
            where
                l.mstatus = 1
                ".$sql_kode_branch."
        ";
        $d_meja = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_meja->count() > 0 ) {
            $data = $d_meja->toArray();
        }

        return $data;
    }

    public function lantai( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "and l.branch_kode = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select l.* from lantai l
            where
                l.mstatus = 1
                ".$sql_kode_branch."
        ";
        $d_lantai = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_lantai->count() > 0 ) {
            $data = $d_lantai->toArray();
        }

        return $data;
    }

    public function menu( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "and m.branch_kode = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                m.*
            from menu m
            where
                m.nama like '%rokok%' and
                m.status = 1
                ".$sql_kode_branch."
        ";
        $d_jm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jm->count() > 0 ) {
            $data = $d_jm->toArray();
        }

        return $data;
    }

    public function paketMenu( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "and m.branch_kode = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select pm.* from paket_menu pm 
            left join
                menu m
                on
                    pm.menu_kode = m.kode_menu
            where
                m.id is not null and
                m.status = 1
                ".$sql_kode_branch."
        ";
        $d_jm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jm->count() > 0 ) {
            $data = $d_jm->toArray();
        }

        return $data;
    }

    public function jenisMenu()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select jm.* from jenis_menu jm
            where
                jm.status = 1
        ";
        $d_jm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_jm->count() > 0 ) {
            $data = $d_jm->toArray();
        }

        return $data;
    }

    public function kategoriMenu()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select km.* from kategori_menu km
            where
                km.status = 1
        ";
        $d_km = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_km->count() > 0 ) {
            $data = $d_km->toArray();
        }

        return $data;
    }

    public function hargaMenu( $kode_branch = null )
    {
        $sql_kode_branch = null;
        if ( !empty( $kode_branch ) ) {
            $sql_kode_branch = "and m.branch_kode = '".$kode_branch."'";
        }

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select
                hm.id,
                hm.tgl_mulai,
                hm.harga,
                hm.menu_kode,
                hm.jenis_pesanan_kode
            from harga_menu hm
            right join
                (
                    select 
                        max(id) as id, 
                        menu_kode, 
                        jenis_pesanan_kode 
                    from harga_menu
                    where
                        tgl_mulai <= GETDATE() 
                    group by 
                        menu_kode, 
                        jenis_pesanan_kode 
                ) _hm
                on
                    hm.id = _hm.id
            left join
                jenis_pesanan jp
                on
                    hm.jenis_pesanan_kode = jp.kode
            left join
                menu m
                on
                    hm.menu_kode = m.kode_menu
            where
                m.status = 1 and
                jp.pilih_meja = 1
                ".$sql_kode_branch."
        ";
        $d_hm = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_hm->count() > 0 ) {
            $data = $d_hm->toArray();
        }

        return $data;
    }

    public function sinkronList() {
        $params = $this->input->post('params');

        $branch = $params['branch'];
        $menu = $params['menu'];
        $table = $menu[ $params['index'] ];

        $index = null;
        foreach ($this->list as $key => $value) {
            if ( $value['table'] == $table ) {
                $index = $key;
            }
        }

        $this->result['status'] = 1;
        $this->result['content'] = array(
            'idx' => $params['index'],
            'jml_menu' => count($menu),
            'branch' => $branch,
            'data' => $this->list[ $index ]
        );

        display_json( $this->result );
    }

    public function sinkron() {
        $params = $this->input->post('params');

        try {
            $idx = $params['idx'];
            $jml_menu = $params['jml_menu'];
            $branch = $params['branch'];
            $table = $params['table'];
    
            $data = null;
            if ( $table == 'branch' ) {
                $data = $this->branch( $branch );
            }
            if ( $table == 'meja' ) {
                $data = $this->meja( $branch );
            }
            if ( $table == 'lantai' ) {
                $data = $this->lantai( $branch );
            }
            if ( $table == 'jenis_menu' ) {
                $data = $this->jenisMenu();
            }
            if ( $table == 'kategori_menu' ) {
                $data = $this->kategoriMenu();
            }
            if ( $table == 'menu' ) {
                $data_menu = $this->menu( $branch );
                $data = $this->paketMenu( $branch );

                $table = 'paket_menu';
            }
            if ( $table == 'harga_menu' ) {
                $data = $this->hargaMenu( $branch );
            }
    
            if ( !empty( $data ) && count( $data ) > 0 ) {
                $url = 'http://grafam-mobile.test/api/'.$table;
                $ch = curl_init();
                foreach ($data as $key => $value) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_URL, $url);
            
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($value));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
                    $data = curl_exec($ch);
                }
                curl_close($ch);
            }

            if ( !empty( $data_menu ) && count( $data_menu ) > 0 ) {
                $url = 'http://grafam-mobile.test/api/menu';
                $ch = curl_init();
                foreach ($data_menu as $key => $value) {
                    $_data = array(
                        'id' => $value['id'],
                        'kode_menu' => $value['kode_menu'],
                        'nama' => $value['nama'],
                        'deskripsi' => $value['deskripsi'],
                        'harga_jual' => $value['harga_jual'],
                        'jenis_menu_id' => $value['jenis_menu_id'],
                        'status' => $value['status'],
                        'induk_menu_id' => $value['induk_menu_id'],
                        'branch_kode' => $value['branch_kode'],
                        'additional' => $value['additional'],
                        'kategori_menu_id' => $value['kategori_menu_id'],
                        'ppn' => $value['ppn'],
                        'service_charge' => $value['service_charge'],
                        'image' => new CURLFile(realpath("uploads/".$value['path_name']))
                    );

                    curl_setopt_array($ch, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $_data,
                    ));

                    $data = curl_exec($ch);
                }
                curl_close($ch);
            }

            $new_idx = $idx+1;
    
            $this->result['status'] = 1;
            $this->result['content'] = array(
                'next' => ($new_idx < $jml_menu) ? 1 : 0,
                'new_idx' => $new_idx
            );
            $this->result['message'] = 'Data berhasil di sinkron';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    function tes() {
        cetak_r( realpath("uploads/images.png"), 1 );
    }
}
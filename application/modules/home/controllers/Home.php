<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$this->add_external_js(array(
            "assets/chart/chart.js",
            "assets/home/js/home.js",
        ));
        $this->add_external_css(array(
        ));

		$data = $this->includes;

		$data['title_menu'] = 'Dashboard';

		$content = null;
		$data['view'] = $this->load->view('home/dashboard', $content, true);

		$this->load->view($this->template, $data);
	}

	public function getDataPenjualan()
	{
		$today = date('Y-m-d');

		$m_branch = new \Model\Storage\Branch_model();
		$d_branch = $m_branch->get();

		$_list_hari = null;
		$_list_outlet = null;
		if ( $d_branch->count() > 0 ) {
			$d_branch = $d_branch->toArray();

			$idx = 0;
			foreach ($d_branch as $k_branch => $v_branch) {
				$prev_date = prev_date($today, 13);

				$list_total = [];

				$_list_outlet[ $v_branch['nama'] ]['nama'] = $v_branch['nama'];
				if ( $idx == 0 ) {
					$_list_outlet[ $v_branch['nama'] ]['warna']['r'] = 255;
					$_list_outlet[ $v_branch['nama'] ]['warna']['g'] = 0;
					$_list_outlet[ $v_branch['nama'] ]['warna']['b'] = 0;
				} else {
					$_list_outlet[ $v_branch['nama'] ]['warna']['r'] = 0;
					$_list_outlet[ $v_branch['nama'] ]['warna']['g'] = 0;
					$_list_outlet[ $v_branch['nama'] ]['warna']['b'] = 255;
				}

				$idx++;

				for ($i=0; $i < 14; $i++) {
					$_list_hari[strtoupper(tglIndonesia($prev_date, '-', ' '))] = substr(strtoupper(tglIndonesia($prev_date, '-', ' ')), 0, 6);

					$start_date = $prev_date.' 00:00:00';
					$end_date = $prev_date.' 23:59:29';

					$sql = "
						select sum(j.grand_total) as total from jual j
						where
							j.mstatus = 1 and
							j.tgl_trans between '".$start_date."' and '".$end_date."' and
							j.branch = '".$v_branch['kode_branch']."'
						group by
							j.branch
					";

					$m_jual = new \Model\Storage\Jual_model();
					$d_jual = $m_jual->hydrateRaw($sql);

					if ( $d_jual->count() > 0 ) {
						$d_jual = $d_jual->toArray();

						foreach ($d_jual as $k_jual => $v_jual) {
							$list_total[] = $v_jual['total'];
						}
					} else {
						$list_total[] = 0;
					}

					$_list_outlet[ $v_branch['nama'] ]['list_total'] = $list_total;

					$prev_date = next_date( $prev_date );
				}
			}
		}

		if ( !empty($_list_hari) && !empty($_list_outlet) ) {
			$list_hari = null;
			foreach ($_list_hari as $k_lh => $v_lh) {
				$list_hari[] = $v_lh;
			}

			$list_outlet = null;
			foreach ($_list_outlet as $k_lo => $v_lo) {
				$list_outlet[] = $v_lo;
			}

			$this->result['status'] = 1;
			$this->result['content'] = array(
				'list_hari' => $list_hari,
				'list_outlet' => $list_outlet
			);
		} else {
			$this->result['status'] = 0;
		}

		display_json( $this->result );
	}

	public function list_notif()
	{
		$notif = null;
		$arr_fitur = $this->session->userdata()['Fitur']; 
		foreach ($arr_fitur as $key => $v_fitur) {
			foreach ($v_fitur['detail'] as $key => $v_mdetail) {
				$akses = hakAkses('/'.$v_mdetail['path_detfitur']);
				if ( $akses['a_ack'] == 1 ) {
					$status = getStatus('submit');

					$data = Modules::run( $v_mdetail['path_detfitur'].'/model', $status)->first();

					$notif[$v_mdetail['path_detfitur']] = $data->toArray();
					$notif[$v_mdetail['path_detfitur']]['path'] = $v_mdetail['path_detfitur'];
					$notif[$v_mdetail['path_detfitur']]['nama_fitur'] = $v_mdetail['nama_detfitur'];

				} else if ( $akses['a_approve'] == 1 ) {
					$status = getStatus('ack');

					$data = Modules::run( $v_mdetail['path_detfitur'].'/model', $status)->first();

					$notif[$v_mdetail['path_detfitur']] = $data->toArray();
					$notif[$v_mdetail['path_detfitur']]['path'] = $v_mdetail['path_detfitur'];
					$notif[$v_mdetail['path_detfitur']]['nama_fitur'] = $v_mdetail['nama_detfitur'];
				}
			}
        }

        return $notif;
	}

	public function excelToArray(){
		$file = 'order_voadip_mgb.xlsx';
 
		//load the excel library
		$this->load->library('excel');
		 
		//read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		 
		//get only the Cell Collection
		$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
		$sheet_collection = $objPHPExcel->getSheetNames();

		/* INJEK ORDER VOADIP */
		$_data_header = null;
		foreach ($sheet_collection as $sheet) {
			$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
			$cell_collection = $sheet_active->getCellCollection();

			foreach ($cell_collection as $cell) {
				$column = $sheet_active->getCell($cell)->getColumn();
				$row = $sheet_active->getCell($cell)->getRow();
				$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

				if ( !empty($data_value) ) {
					if ($row == 1) {
				        $_data_header['header'][$row][$column] = strtoupper($data_value);
				    } else {
				    	if ( isset( $_data_header['header'][1][$column] ) ) {
					    	$_column_val = $_data_header['header'][1][$column];

					    	$val = $data_value;

					    	if ( $_column_val == 'NAMA ITEM' ) {
					    		$m_brg = new \Model\Storage\Barang_model();
								$d_brg = $m_brg->where('nama', trim(strtoupper($val)))->orderBy('id', 'desc')->first();

					    		$_data['value'][$row][$_column_val] = $d_brg->kode;
					    		$_data['value'][$row]['KATEGORI'] = $d_brg->kategori;
					    		$_data['value'][$row]['KEMASAN'] = 'PLASTIK';
					    	} else if ( $_column_val == 'PERUSAHAAN' ) {
					    		$m_perusahaan = new \Model\Storage\Perusahaan_model();
								$d_perusahaan = $m_perusahaan->where('perusahaan', 'like', '%'.trim(strtoupper($val)).'%')->orderBy('version', 'desc')->first();

								if ( empty($d_perusahaan) ) {
									cetak_r( $val );
								} else {
					    			$_data['value'][$row][$_column_val] = $d_perusahaan->kode;
								}
							} else if ( $_column_val == 'SUPPLIER' ) {
					    		$m_supl = new \Model\Storage\Supplier_model();
								$d_supl = $m_supl->where('nama', 'like', '%'.trim(strtoupper($val)).'%')->where('tipe', 'supplier')->orderBy('version', 'desc')->first();

								if ( empty($d_supl) ) {
									cetak_r( $val );
								} else {
					    			$_data['value'][$row][$_column_val] = $d_supl->nomor;
								}
							} else if ( $_column_val == 'GUDANG' ) {
					    		$m_gdg = new \Model\Storage\Gudang_model();
								$d_gdg = $m_gdg->where('nama', 'like', '%'.trim(strtoupper($val)).'%')->where('jenis', 'OBAT')->orderBy('id', 'desc')->first();

				    			$_data['value'][$row][$_column_val] = !empty($d_gdg) ? $d_gdg->id : null;
				    			$_data['value'][$row]['ALAMAT'] = !empty($d_gdg) ? $d_gdg->alamat : null;
				    			$_data['value'][$row]['KIRIM KE'] = 'GUDANG';
				    		} else if ( $_column_val == 'TGL ORDER' || $_column_val == 'TGL KIRIM' ) {
					    		$split = explode('/', $val);
					    		$year = $split[2]; 
					    		$month = (strlen($split[0]) < 2) ? '0'.$split[0] : $split[0];
					    		$day = (strlen($split[1]) < 2) ? '0'.$split[1] : $split[1];
					    		$tgl = $year.'-'.$month.'-'.$day;

					    		$_data['value'][$row][$_column_val] = $tgl;
					    	} else {
					    		$_data['value'][$row][$_column_val] = $val;
					    	}
				    	}
				    }
			    }
			}
		}

		if ( !empty($_data) ) {
			$data = null;
			foreach ($_data['value'] as $k_val => $val) {
				$key = $val['SUPPLIER'].' - '.str_replace('-', '', $val['TGL ORDER']).' - '.$val['GUDANG'];
				$data[ $key ]['SUPPLIER'] = $val['SUPPLIER'];
				$data[ $key ]['TGL ORDER'] = $val['TGL ORDER'];
				$data[ $key ]['GUDANG'] = $val['GUDANG'];
				$data[ $key ]['DETAIL'][] = $val;
			}

			if ( !empty($data) ) {
				foreach ($data as $k_data => $v_data) {
					$m_order_voadip = new \Model\Storage\OrderVoadip_model();
		            $now = $m_order_voadip->getDate();

		            $kode_unit = null;
	                $id_kirim = $v_data['GUDANG'];
	                $jenis_kirim = 'gudang';

		            if ( stristr($jenis_kirim, 'gudang') !== FALSE ) {
		                $m_gdg = new \Model\Storage\Gudang_model();
		                $d_gdg = $m_gdg->where('id', $id_kirim)->with(['dUnit'])->first();

		                if ( $d_gdg ) {
		                    $d_gdg = $d_gdg->toArray();
		                    $kode_unit = $d_gdg['d_unit']['kode'];
		                }
		            }

		            $nomor = $m_order_voadip->getNextNomor('OVO/'.$kode_unit);

		            $id_order = $m_order_voadip->getNextIdentity();

		            $m_order_voadip->id = $id_order;
		            $m_order_voadip->no_order = $nomor;
		            $m_order_voadip->supplier = $v_data['SUPPLIER'];
		            $m_order_voadip->tanggal = $v_data['TGL ORDER'];
		            $m_order_voadip->user_submit = $this->userid;
		            $m_order_voadip->tgl_submit = $now['waktu'];
		            $m_order_voadip->version = 1;
		            $m_order_voadip->save();

		            foreach ($v_data['DETAIL'] as $k_detail => $v_detail) {
		                $m_order_voadip_detail = new \Model\Storage\OrderVoadipDetail_model();

		                $m_order_voadip_detail->id = $m_order_voadip_detail->getNextIdentity();
		                $m_order_voadip_detail->id_order = $m_order_voadip->id;
		                $m_order_voadip_detail->kode_barang = $v_detail['NAMA ITEM'];
		                $m_order_voadip_detail->kemasan = $v_detail['KEMASAN'];
		                $m_order_voadip_detail->harga = $v_detail['HARGA BELI'];
		                $m_order_voadip_detail->harga_jual = isset($v_detail['HARGA JUAL']) ? $v_detail['HARGA JUAL'] : 0;
		                $m_order_voadip_detail->jumlah = $v_detail['JUMLAH'];
		                $m_order_voadip_detail->total = $v_detail['HARGA BELI'] * $v_detail['JUMLAH'];
		                $m_order_voadip_detail->kirim_ke = strtolower($v_detail['KIRIM KE']);
		                $m_order_voadip_detail->alamat = $v_detail['ALAMAT'];
		                $m_order_voadip_detail->kirim = $v_detail['GUDANG'];
		                $m_order_voadip_detail->perusahaan = $v_detail['PERUSAHAAN'];
		                $m_order_voadip_detail->tgl_kirim = $v_detail['TGL KIRIM'];
		                $m_order_voadip_detail->save();
		            }

		            $d_order_voadip = $m_order_voadip->where('id', $id_order)->with(['detail'])->first();

		            $deskripsi_log_order_voadip = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		            Modules::run( 'base/event/save', $d_order_voadip, $deskripsi_log_order_voadip);
				}
			}
		}
	}

	public function insert_terima_pakan()
	{
		$m_kirim_pakan = new \Model\Storage\KirimPakan_model();
		$d_kirim_pakan = $m_kirim_pakan->whereBetween('tgl_kirim', ['2021-12-20', '2021-12-28'])->with(['detail'])->get();

		if ( $d_kirim_pakan->count() > 0 ) {
			$d_kirim_pakan = $d_kirim_pakan->toArray();
			foreach ($d_kirim_pakan as $k_kp => $v_kp) {
				$m_terima_pakan = new \Model\Storage\TerimaPakan_model();
				$d_terima_pakan = $m_terima_pakan->where('id_kirim_pakan', $v_kp['id'])->first();

				if ( !$d_terima_pakan ) {
					$m_terima_pakan = new \Model\Storage\TerimaPakan_model();
                    $now = $m_terima_pakan->getDate();

                    $m_terima_pakan->id_kirim_pakan = $v_kp['id'];
                    $m_terima_pakan->tgl_trans = $now['waktu'];
                    $m_terima_pakan->tgl_terima = $v_kp['tgl_kirim'];
                    $m_terima_pakan->path = null;
                    $m_terima_pakan->save();

                    $id_header = $m_terima_pakan->id;

                    foreach ($v_kp['detail'] as $k_detail => $v_detail) {
                        $m_terima_pakan_detail = new \Model\Storage\TerimaPakanDetail_model();
                        $m_terima_pakan_detail->id_header = $id_header;
                        $m_terima_pakan_detail->item = $v_detail['item'];
                        $m_terima_pakan_detail->jumlah = $v_detail['jumlah'];
                        $m_terima_pakan_detail->kondisi = $v_detail['kondisi'];
                        $m_terima_pakan_detail->save();
                    }

                    $d_terima_pakan = $m_terima_pakan->where('id', $id_header)->first();

                    $deskripsi_log_terima_pakan = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $d_terima_pakan, $deskripsi_log_terima_pakan);
				}
			}
		}
	}

	public function insert_saldo_pelanggan()
	{
		$arr = array(
			array('Slamet Wahyudi', 'BANYUWANGI', '22930000'),
			array('Suhardi', 'BANYUWANGI', '36540900'),
			array('Mohamad Eko Faris Azhar', 'BANYUWANGI', '29419300'),
			array('Stefanus Tomy Kurniawan', 'BANYUWANGI', '11910000'),
			array('Siti Aisah', 'JEMBER', '29542750'),
			array('Umi Lutfiani Masithah', 'BONDOWOSO', '1950'),
			array('Chotimah', 'JEMBER', '2650'),
			array('Kasmu', 'JEMBER', '18559050'),
			array('Rani Sanjaya', 'LUMAJANG', '354216350'),
			array('Suwito', 'JEMBER', '16500'),
			array('Ahmadi', 'JEMBER', '26692950'),
			array('Abi Abdillah', 'JEMBER', '500'),
			array('Khoirul Anam', 'LUMAJANG', '28960750'),
			array('Yeni Astria Ningsih', 'JEMBER', '6650'),
			array('Susiyanah', 'JEMBER', '13800'),
			array('Tonny Kiantara', 'JEMBER', '1550'),
			array('Umi Ajijah', 'JEMBER', '6250'),
			array('Imam Mustofa', 'JEMBER', '250'),
			array('Yaman Didik Hariyanto', 'JEMBER', '24345'),
			array('Nanok Sismianto', 'JEMBER', '17454650'),
			array('Tedy Kumala', 'BONDOWOSO', '500'),
			array('Rismidarliah', 'LUMAJANG', '1000'),
			array('Ruyantoh', 'JEMBER', '50'),
			array('M.Munir Adi Prayoga', 'JEMBER', '30864000'),
			array('Didik Sutrisno', 'JEMBER', '450'),
			array('Wawan Hidayatulloh', 'JEMBER', '11982250'),
			array('M.Irsyadul Ibat', 'JEMBER', '43230000'),
			array('Sumarni B Yeni', 'JEMBER', '41097250'),
			array('Muhammad Munir', 'PASURUAN', '30000'),
			array('Ngateno', 'LUMAJANG', '25088150'),
			array('M. Mansur', 'JEMBER', '100'),
			array('Karyono', 'LUMAJANG', '1000'),
			array('Johannes', 'JEMBER', '2000'),
			array('Miswanto', 'JEMBER', '45497000'),
			array('Achmad Faizal', 'BANYUWANGI', '32190000'),
			array('Muhammad Alim Muslim', 'LUMAJANG', '4260750'),
			array('Dewi Nur Kamila', 'SITUBONDO', '6790'),
			array('Bambang Brontoyono', 'PROBOLINGGO', '33719400'),
			array('Sahid', 'PROBOLINGGO', '6250'),
			array('Muhammad Fadloli', 'PASURUAN', '9690000'),
			array('Muhammad Hadar', 'PASURUAN', '11184100'),
			array('Ragil Alfan Hidayat', 'PASURUAN', '29100000'),
			array('Arif Budianto', 'MALANG', '2029500'),
			array('Muhammad Kurdi', 'MALANG', '800'),
			array('Sia Kok Ing', 'KOTA MALANG', '30000000'),
			array('Yulianto', 'MALANG', '500'),
			array('Ferdiansyah Deny Hartono', 'MALANG', '50'),
			array('Surono', 'MALANG', '57480250'),
			array('Ryan Andib Prayogo', 'MALANG', '250'),
			array('Achmad Zamzuri', 'JOMBANG', '37710'),
			array("Syafa'at", 'MOJOKERTO', '6330'),
			array('M. Yusufi', 'MOJOKERTO', '27454540'),
			array('Amelia Romadhini', 'MOJOKERTO', '11300270'),
			array('Miftahul Ulum', 'JOMBANG', '22480'),
			array('Qodirin', 'MOJOKERTO', '8660'),
			array('Wildhan Mubaroq', 'PASURUAN', '1800'),
			array('Samsul Huda', 'MOJOKERTO', '600'),
			array('Mohammad Imam Buchori', 'SIDOARJO', '250'),
			array('Agus Eko Saputro', 'MOJOKERTO', '100'),
			array('Indra Astutik', 'JOMBANG', '50'),
			array('Solikan', 'MOJOKERTO', '77200000'),
			array('Nur Arifin', 'KEDIRI', '900'),
			array('Mujiati', 'KEDIRI', '10400'),
			array('Sugiarto', 'KEDIRI', '50'),
			array('Agung Abdul Wachid', 'KOTA SURABAYA', '90533500'),
			array('Achmad Luluk Fathurahman', 'BLITAR', '100'),
			array('Anik', 'TULUNGAGUNG', '750'),
			array('Abu Sujak', 'TULUNGAGUNG', '1740'),
			array('Arik Herwanto', 'TULUNGAGUNG', '2150'),
			array('Panut Prasetyo', 'TULUNGAGUNG', '11700'),
			array('Yon Haryono', 'TULUNGAGUNG', '500'),
			array('Fatchur Roziq Mustofa Naim', 'TULUNGAGUNG', '50'),
			array('Darminto', 'TULUNGAGUNG', '9910'),
			array('Rochmad', 'KEDIRI', '600'),
			array('Joko Puji Kuswoyo', 'TULUNGAGUNG', '830'),
			array('Mat Suryan', 'KEDIRI', '50'),
			array('Nickoeris Setiawan', 'TULUNGAGUNG', '200'),
			array('Musiaman', 'TULUNGAGUNG', '3960'),
			array('Winarko', 'KEDIRI', '49804800'),
			array('Surip Abdul Qohar', 'KEDIRI', '22233600'),
			array('Anton Pratomo', 'KEDIRI', '10556200'),
			array('Suriyat, Drs', 'LAMONGAN', '54320410'),
			array('Abdul Munif', 'GRESIK', '900'),
			array('Syolikan Arif, ST', 'GRESIK', '23250'),
			array('Imam Thohari', 'LAMONGAN', '92269030'),
			array('Mudlofir', 'LAMONGAN', '2000'),
			array('Yudi Yuswanto', 'MOJOKERTO', '42478710'),
			array('Nur Komari', 'GRESIK', '9000'),
			array('Frans Pitrajaya', 'KOTA SURABAYA', '80'),
			array('Akhyatul Munir', 'GRESIK', '30649420'),
			array('Sumadi Maryanto', 'LAMONGAN', '250'),
			array('Sukri', 'LAMONGAN', '60'),
			array('Abdul Yusuf Faisal', 'KOTA SURABAYA', '71225000'),
			array('Istianah', 'LAMONGAN', '25584350'),
			array('Musri', 'MAGETAN', '1550'),
			array('Sarmi', 'MAGETAN', '100'),
			array('Edi Susanto', 'NGANJUK', '900'),
			array('Hariyanto', 'MAGETAN', '3000'),
			array('Sumadi', 'GARUM', '600'),
			array('Liem Tjhioe Giok', 'KOTA MADIUN', '200'),
			array('Ibnu Masngut', 'NGANJUK', '900'),
			array('Andi Setiawan', 'MALANG', '200'),
			array('M Irsyadul Munib', 'MAGETAN', '2300'),
			array('Arini Julaika', 'MAGETAN', '750'),
			array('Gunariyanto', 'NGAWI', '600'),
			array('Basuki', 'MAGETAN', '600'),
			array('Hasdi', 'NGANJUK', '15680'),
			array('Edo Maryoto', 'DAWARBLANDONG', '9637200'),
			array('Bejo Suroto', 'BOYOLALI', '1520'),
			array('Selvy Setyawati', 'BOYOLALI', '520'),
			array('Buniyati', 'KLATEN', '306020'),
			array('Sartono', 'KARANGANYAR', '400'),
			array('Yuhanus Dwi Sunaryo', 'DAWARBLANDONG', '540'),
			array('Suhadi', 'PROBOLINGGO', '1740'),
			array('Dedy Harnanto', 'BOYOLALI', '40'),
			array('Diah Ayu Ratna Wulandari', 'BOYOLALI', '400'),
			array('Widodo', 'SRAGEN', '20800'),
			array('Asih Yuniati', 'KOTA SURAKARTA', '800'),
			array('Suwadi', 'SEMARANG', '19156720'),
			array('Sri Darini DRA MSC', 'SUKOHARJO', '600'),
			array('Ika Sri Wahyuningsih', 'BOYOLALI', '2220'),
			array('Mukholis Nugroho', 'BOYOLALI', '380'),
			array('Seno Hantoro', 'BOYOLALI', '160'),
			array('Dwi Santoso', 'SUKOHARJO', '200 ')
		);

		foreach ($arr as $k_arr => $v_arr) {
			$m_plg = new \Model\Storage\Pelanggan_model();
			$d_plg = $m_plg->where('nama', 'like', '%'.strtoupper($v_arr[0]).'%')->where('tipe', 'pelanggan')->orderBy('version', 'desc')->first();

			$m_sld_plg = new \Model\Storage\SaldoPelanggan_model();
			$m_sld_plg->jenis_saldo = 'D';
			$m_sld_plg->no_pelanggan = $d_plg->nomor;
			$m_sld_plg->id_trans = null;
			$m_sld_plg->tgl_trans = '2021-11-01';
			$m_sld_plg->jenis_trans = 'pembayaran_pelanggan';
			$m_sld_plg->nominal = 0;
			$m_sld_plg->saldo = $v_arr[2];
			$m_sld_plg->save();
		}
	}
}
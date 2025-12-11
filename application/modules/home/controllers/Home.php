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
		$prev_date = prev_date($today, 13);
		
		$m_branch = new \Model\Storage\Branch_model();
		$d_branch = $m_branch->get();

		$_list_outlet = null;
		$idx = 0;
		foreach ($d_branch as $k_branch => $v_branch) {
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
		}

		$start_date = $prev_date.' 00:00:00.001';
		$end_date = $today.' 23:59:59.999';

		$m_conf = new \Model\Storage\Conf();
		$sql = "
			select 
				convert(varchar(10), j.tgl_trans, 120) as tgl_trans, 
				j.branch, 
				b.nama as nama_branch,
				sum(j.grand_total) as total 
			from jual j
			left join
				branch b
				on
					j.branch = b.kode_branch
			where
				j.mstatus = 1 and
				j.tgl_trans between '".$start_date."' and '".$end_date."'
			group by
				j.branch,
				b.nama,
				convert(varchar(10), j.tgl_trans, 120)
		";
		$d_conf = $m_conf->hydrateRaw( $sql );

		$list_total = [];
		$_list_hari = null;
		if ( $d_conf->count() > 0 ) {
			$d_conf = $d_conf->toArray();

			$idx_branch = 0;
			foreach ($d_conf as $key => $value) {
				$_list_hari[strtoupper(tglIndonesia($value['tgl_trans'], '-', ' '))] = substr(strtoupper(tglIndonesia($value['tgl_trans'], '-', ' ')), 0, 6);

				$_list_outlet[ $value['nama_branch'] ]['list_total'][] = $value['total'];
			}
		}

		// cetak_r($_list_hari);
		// cetak_r($_list_outlet, 1);

		// $m_branch = new \Model\Storage\Branch_model();
		// $d_branch = $m_branch->get();

		// $_list_hari = null;
		// $_list_outlet = null;
		// if ( $d_branch->count() > 0 ) {
		// 	$d_branch = $d_branch->toArray();

		// 	$idx = 0;
		// 	foreach ($d_branch as $k_branch => $v_branch) {
		// 		$prev_date = prev_date($today, 13);

		// 		$list_total = [];

		// 		$_list_outlet[ $v_branch['nama'] ]['nama'] = $v_branch['nama'];
		// 		if ( $idx == 0 ) {
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['r'] = 255;
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['g'] = 0;
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['b'] = 0;
		// 		} else {
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['r'] = 0;
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['g'] = 0;
		// 			$_list_outlet[ $v_branch['nama'] ]['warna']['b'] = 255;
		// 		}

		// 		$idx++;

		// 		for ($i=0; $i < 14; $i++) {
		// 			$_list_hari[strtoupper(tglIndonesia($prev_date, '-', ' '))] = substr(strtoupper(tglIndonesia($prev_date, '-', ' ')), 0, 6);

		// 			$start_date = $prev_date.' 00:00:00';
		// 			$end_date = $prev_date.' 23:59:29';

		// 			$sql = "
		// 				select sum(j.grand_total) as total from jual j
		// 				where
		// 					j.mstatus = 1 and
		// 					j.tgl_trans between '".$start_date."' and '".$end_date."' and
		// 					j.branch = '".$v_branch['kode_branch']."'
		// 				group by
		// 					j.branch
		// 			";

		// 			$m_jual = new \Model\Storage\Jual_model();
		// 			$d_jual = $m_jual->hydrateRaw($sql);

		// 			if ( $d_jual->count() > 0 ) {
		// 				$d_jual = $d_jual->toArray();

		// 				foreach ($d_jual as $k_jual => $v_jual) {
		// 					$list_total[] = $v_jual['total'];
		// 				}
		// 			} else {
		// 				$list_total[] = 0;
		// 			}

		// 			$_list_outlet[ $v_branch['nama'] ]['list_total'] = $list_total;

		// 			$prev_date = next_date( $prev_date );
		// 		}
		// 	}
		// }

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
}
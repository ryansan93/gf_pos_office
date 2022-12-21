<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Jual_model extends Conf{
	protected $table = 'jual';
	protected $primaryKey = 'kode_faktur';
	public $timestamps = false;

	public function detail()
	{
		return $this->hasMany('\Model\Storage\JualItem_model', 'faktur_kode', 'kode_faktur')->with(['menu', 'detail']);
	}

	public function bayar()
	{
		return $this->hasMany('\Model\Storage\Bayar_model', 'faktur_kode', 'kode_faktur')->with(['jenis_kartu', 'bayar_det']);
	}

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch');
	}

	public function pesanan()
	{
		return $this->hasOne('\Model\Storage\Pesanan_model', 'kode_pesanan', 'pesanan_kode');
	}
}

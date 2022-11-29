<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokTanggal_model extends Conf{
	protected $table = 'stok_tanggal';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\Stok_model', 'id_header', 'id')->with(['detail', 'branch', 'item']);
	}
}
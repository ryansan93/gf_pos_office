<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Stok_model extends Conf{
	protected $table = 'stok';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function item()
	{
		return $this->hasOne('\Model\Storage\Item_model', 'kode', 'item_kode')->with(['group']);
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\StokTrans_model', 'id_header', 'id');
	}
}

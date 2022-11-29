<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Beli_model extends Conf{
	protected $table = 'beli';
	protected $primaryKey = 'kode_beli';
	protected $kodeTable = 'BL';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function supplier()
	{
		return $this->hasOne('\Model\Storage\Supplier_model', 'kode', 'supplier_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\BeliItem_model', 'beli_kode', 'kode_beli')->with(['item']);
	}
}

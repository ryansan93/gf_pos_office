<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Gudang_model extends Conf{
	protected $table = 'gudang';
	protected $primaryKey = 'kode_gudang';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}
}

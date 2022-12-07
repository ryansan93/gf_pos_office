<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Ppn_model extends Conf{
	protected $table = 'ppn';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}
}

<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Adjout_model extends Conf{
	protected $table = 'adjout';
	protected $primaryKey = 'kode_adjout';
	protected $kodeTable = 'AO';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\AdjoutItem_model', 'adjout_kode', 'kode_adjout')->with(['item']);
	}

	public function log_tables()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'kode_adjout')->where('tbl_name', $this->tbl_name);
	}
}

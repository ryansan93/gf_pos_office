<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Adjin_model extends Conf{
	protected $table = 'adjin';
	protected $primaryKey = 'kode_adjin';
	protected $kodeTable = 'AI';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\AdjinItem_model', 'adjin_kode', 'kode_adjin')->with(['item']);
	}
}

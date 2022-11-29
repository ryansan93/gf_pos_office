<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Mutasi_model extends Conf{
	protected $table = 'mutasi';
	protected $primaryKey = 'kode_mutasi';
	protected $kodeTable = 'MT';
	public $timestamps = false;

	public function branch_asal()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'asal');
	}

	public function branch_tujuan()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'tujuan');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\MutasiItem_model', 'mutasi_kode', 'kode_mutasi')->with(['item']);
	}
}

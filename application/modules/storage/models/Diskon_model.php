<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Diskon_model extends Conf{
	protected $table = 'diskon';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'DSK';
	public $timestamps = false;

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function diskon_jenis_kartu()
	{
		return $this->hasMany('\Model\Storage\DiskonJenisKartu_model', 'diskon_kode', 'kode');
	}

	public function diskon_menu()
	{
		return $this->hasMany('\Model\Storage\DiskonMenu_model', 'diskon_kode', 'kode');
	}

	public function diskon_beli_dapat()
	{
		return $this->hasMany('\Model\Storage\DiskonBeliDapat_model', 'diskon_kode', 'kode');
	}
}

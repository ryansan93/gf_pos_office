<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Mutasi_model extends Conf{
	protected $table = 'mutasi';
	protected $primaryKey = 'kode_mutasi';
	protected $kodeTable = 'MT';
	public $timestamps = false;

	public function gudang_asal()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'asal');
	}

	public function gudang_tujuan()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'tujuan');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\MutasiItem_model', 'mutasi_kode', 'kode_mutasi')->with(['item']);
	}
}

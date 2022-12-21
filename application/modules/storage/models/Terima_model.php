<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Terima_model extends Conf{
	protected $table = 'terima';
	protected $primaryKey = 'kode_terima';
	protected $kodeTable = 'TR';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\TerimaItem_model', 'terima_kode', 'kode_terima')->with(['item']);
	}
}

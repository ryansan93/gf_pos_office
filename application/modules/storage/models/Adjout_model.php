<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Adjout_model extends Conf{
	protected $table = 'adjout';
	protected $primaryKey = 'kode_adjout';
	protected $kodeTable = 'AO';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\AdjoutItem_model', 'adjout_kode', 'kode_adjout')->with(['item']);
	}

	public function logs()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'kode_adjout')->where('tbl_name', $this->table)->orderBy('waktu', 'asc');
	}
}

<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Adjin_model extends Conf{
	protected $table = 'adjin';
	protected $primaryKey = 'kode_adjin';
	protected $kodeTable = 'AI';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\AdjinItem_model', 'adjin_kode', 'kode_adjin')->with(['item']);
	}

	public function logs()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'kode_adjin')->where('tbl_name', $this->table)->orderBy('waktu', 'asc');
	}
}

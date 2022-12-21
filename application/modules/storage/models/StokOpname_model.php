<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokOpname_model extends Conf{
	protected $table = 'stok_opname';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function detail()
	{
		return $this->hasMany('\Model\Storage\StokOpnameDet_model', 'id_header', 'id')->with(['item']);
	}

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}
}

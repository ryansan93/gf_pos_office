<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Stok_model extends Conf{
	protected $table = 'stok';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}

	public function item()
	{
		return $this->hasOne('\Model\Storage\Item_model', 'kode', 'item_kode')->with(['group']);
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\StokTrans_model', 'id_header', 'id');
	}
}

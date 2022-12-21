<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokOpnameDet_model extends Conf{
	protected $table = 'stok_opname_det';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function item()
	{
		return $this->hasOne('\Model\Storage\Item_model', 'kode', 'item_kode')->with(['satuan']);
	}
}

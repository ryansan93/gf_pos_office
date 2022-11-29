<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class MutasiItem_model extends Conf{
	protected $table = 'mutasi_item';
	public $timestamps = false;

	public function item()
	{
		return $this->hasOne('\Model\Storage\Item_model', 'kode', 'item_kode')->with(['group']);
	}
}

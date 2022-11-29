<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Item_model extends Conf {
	protected $table = 'item';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'ITM';
    public $timestamps = false;

    public function group()
	{
		return $this->hasOne('\Model\Storage\GroupItem_model', 'kode', 'group_kode');
	}
}
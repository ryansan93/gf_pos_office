<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Item_model extends Conf {
	protected $table = 'item';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'BRG';
    public $timestamps = false;

	public function getNextId_agustus(){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.",4,4) = cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)")
								->selectRaw("'".$this->kodeTable."'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(substring(coalesce(max(".$this->primaryKey."),'000'),8,3)+1,3), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}

    public function group()
	{
		return $this->hasOne('\Model\Storage\GroupItem_model', 'kode', 'group_kode');
	}

	public function satuan()
	{
		return $this->hasMany('\Model\Storage\ItemSatuan_model', 'item_kode', 'kode');
	}
}
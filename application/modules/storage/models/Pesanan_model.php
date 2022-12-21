<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Pesanan_model extends Conf {
	protected $table = 'pesanan';
	protected $primaryKey = 'kode_pesanan';
	public $timestamps = false;

	public function getNextKode($kode){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.", 0, ".(((strlen($kode)+1)+6)+1).") = '".$kode."'+'-'+cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)")
								->selectRaw("'".$kode."'+'-'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)+replace(str(substring(coalesce(max(".$this->primaryKey."),'0000'),".(((strlen($kode)+1)+6)+1).",4)+1,4), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}

    public function pesanan_item()
	{
		return $this->hasMany('\Model\Storage\PesananItem_model', 'pesanan_kode', 'kode_pesanan')->with(['pesanan_item_detail', 'jenis_pesanan']);
	}

	public function meja()
	{
		return $this->hasOne('\Model\Storage\Meja_model', 'id', 'meja_id')->with(['lantai']);
	}
}
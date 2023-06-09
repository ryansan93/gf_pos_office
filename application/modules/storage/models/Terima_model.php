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

	public function getNextNoInvoice(){
		$id = $this->whereRaw("SUBSTRING(no_faktur, 4, 6) = cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)")
								->selectRaw("'INV'+cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)+replace(str(substring(coalesce(max(no_faktur),'0000'), 10, 4)+1, 4), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}
}

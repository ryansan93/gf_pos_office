<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Po_model extends Conf{
	protected $table = 'po';
	protected $primaryKey = 'no_po';
	protected $kodeTable = 'PO';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode')->with(['branch']);
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\PoItem_model', 'po_no', 'no_po')->with(['item']);
	}

	public function getNextNoPo(){
		$id = $this->whereRaw("SUBSTRING(no_po, 3, 6) = cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)")
								->selectRaw("'PO'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)+replace(str(substring(coalesce(max(no_po),'0000'), 9, 4)+1, 4), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}
}

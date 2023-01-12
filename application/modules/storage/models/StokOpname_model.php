<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokOpname_model extends Conf{
	protected $table = 'stok_opname';
	protected $primaryKey = 'id';
	protected $kodeTable = 'SO';
	public $timestamps = false;

	public function getNextIdRibuan(){
		$id = $this->whereRaw("SUBSTRING(kode_stok_opname,".(strlen($this->kodeTable)+1).",4) = cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)")
								->selectRaw("'".$this->kodeTable."'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(substring(coalesce(max(kode_stok_opname),'0000'),".((strlen($this->kodeTable)+1)+4).",4)+1,4), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\StokOpnameDet_model', 'id_header', 'id')->with(['item']);
	}

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}
}

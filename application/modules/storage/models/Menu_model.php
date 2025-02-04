<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Menu_model extends Conf{
	protected $table = 'menu';
	protected $primaryKey = 'kode_menu';
	protected $kodeTable = 'MNU';
	public $timestamps = false;

	// public function getNextKodeMenu(){
	// 	$id = $this->whereRaw("SUBSTRING(kode_menu,4,4) = cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)")
	// 							->selectRaw("'".$this->kodeTable."'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(substring(coalesce(max(kode_menu),'000'),8,3)+1,3), ' ', '0') as nextId")
	// 							->first();
	// 	return $id->nextId;
	// }

	public function kategori()
	{
		return $this->hasOne('\Model\Storage\KategoriMenu_model', 'id', 'kategori_menu_id');
	}

	public function jenis()
	{
		return $this->hasOne('\Model\Storage\JenisMenu_model', 'id', 'jenis_menu_id');
	}

	public function induk_menu()
	{
		return $this->hasOne('\Model\Storage\IndukMenu_model', 'id', 'induk_menu_id');
	}

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}
}

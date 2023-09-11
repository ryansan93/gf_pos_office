<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class WasteMenu_model extends Conf {
	protected $table = 'waste_menu';
	protected $primaryKey = 'id';

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function waste_menu_item()
	{
		return $this->hasMany('\Model\Storage\WasteMenuItem_model', 'id_header', 'id')->with(['menu']);
	}
}
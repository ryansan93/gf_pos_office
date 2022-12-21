<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Bom_model extends Conf{
	protected $table = 'bom';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function detail()
	{
		return $this->hasMany('\Model\Storage\BomDet_model', 'id_header', 'id')->with(['item']);
	}

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode')->with(['branch']);
	}
}

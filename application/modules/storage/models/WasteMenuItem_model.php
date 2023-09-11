<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class WasteMenuItem_model extends Conf {
	protected $table = 'waste_menu_item';

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode');
	}
}
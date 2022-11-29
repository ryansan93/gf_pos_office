<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualItemDetail_model extends Conf{
	protected $table = 'jual_item_detail';
	public $timestamps = false;

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode')->with(['kategori', 'induk_menu']);
	}
}

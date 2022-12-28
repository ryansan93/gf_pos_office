<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualItem_model extends Conf{
	protected $table = 'jual_item';
	protected $primaryKey = 'kode_faktur_item';
	public $timestamps = false;

	public function detail()
	{
		return $this->hasMany('\Model\Storage\JualItemDetail_model', 'faktur_item_kode', 'kode_faktur_item')->with(['menu']);
	}

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode')->with(['kategori', 'jenis', 'induk_menu']);
	}
}

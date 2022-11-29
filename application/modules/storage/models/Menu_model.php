<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Menu_model extends Conf{
	protected $table = 'menu';
	protected $primaryKey = 'kode_menu';
	protected $kodeTable = 'MNU';
	public $timestamps = false;

	public function kategori()
	{
		return $this->hasOne('\Model\Storage\KategoriMenu_model', 'id', 'kategori_menu_id');
	}

	public function induk_menu()
	{
		return $this->hasOne('\Model\Storage\IndukMenu_model', 'id', 'induk_menu_id');
	}
}

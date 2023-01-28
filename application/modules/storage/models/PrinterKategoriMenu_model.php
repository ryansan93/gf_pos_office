<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PrinterKategoriMenu_model extends Conf {
	protected $table = 'printer_kategori_menu';

	public function kategori_menu()
	{
		return $this->hasOne('\Model\Storage\KategoriMenu_model', 'id', 'kategori_menu_id');
	}
}
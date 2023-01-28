<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Printer_model extends Conf {
	protected $table = 'printer';
	protected $primaryKey = 'id';

	public function branch()
	{
		return $this->hasOne('\Model\Storage\Branch_model', 'kode_branch', 'branch_kode');
	}

	public function printer_kategori_menu()
	{
		return $this->hasMany('\Model\Storage\PrinterKategoriMenu_model', 'id_header', 'id')->wiht(['kategori_menu']);
	}
}
<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PaketMenu_model extends Conf{
	protected $table = 'paket_menu';
	protected $primaryKey = 'kode_paket_menu';
	protected $kodeTable = 'KPM';
	public $timestamps = false;

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode');
	}

	public function isi_paket_menu()
	{
		return $this->hasMany('\Model\Storage\IsiPaketMenu_model', 'paket_menu_kode', 'kode_paket_menu')->with(['menu']);
	}
}

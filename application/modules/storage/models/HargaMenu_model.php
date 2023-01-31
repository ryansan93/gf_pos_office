<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HargaMenu_model extends Conf{
	protected $table = 'harga_menu';
	public $timestamps = false;

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode')->with(['branch']);
	}

	public function jenis_pesanan()
	{
		return $this->hasOne('\Model\Storage\JenisPesanan_model', 'kode', 'jenis_pesanan_kode');
	}
}

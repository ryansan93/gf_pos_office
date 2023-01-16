<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokTanggal_model extends Conf{
	protected $table = 'stok_tanggal';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function gudang()
	{
		return $this->hasOne('\Model\Storage\Gudang_model', 'kode_gudang', 'gudang_kode');
	}

	public function detail()
	{
		return $this->hasMany('\Model\Storage\Stok_model', 'id_header', 'id')->with(['detail', 'gudang', 'item']);
	}
}
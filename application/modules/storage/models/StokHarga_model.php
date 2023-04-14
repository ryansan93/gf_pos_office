<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokHarga_model extends Conf{
	protected $table = 'stok_harga';
	protected $primaryKey = 'id';
	public $timestamps = false;
}

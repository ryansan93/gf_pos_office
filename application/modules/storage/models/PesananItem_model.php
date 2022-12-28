<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PesananItem_model extends Conf {
	protected $table = 'pesanan_item';
	protected $primaryKey = 'kode_pesanan_item';
	public $timestamps = false;
}
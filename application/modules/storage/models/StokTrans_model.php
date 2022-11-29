<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class StokTrans_model extends Conf{
	protected $table = 'stok_trans';
	protected $primaryKey = 'id';
	public $timestamps = false;
}

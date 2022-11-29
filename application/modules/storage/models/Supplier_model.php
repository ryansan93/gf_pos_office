<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Supplier_model extends Conf {
	protected $table = 'supplier';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'SUP';
    public $timestamps = false;
}
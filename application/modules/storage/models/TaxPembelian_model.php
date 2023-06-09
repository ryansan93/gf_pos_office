<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class TaxPembelian_model extends Conf{
	protected $table = 'tax_pembelian';
	protected $primaryKey = 'id';
	public $timestamps = false;
}

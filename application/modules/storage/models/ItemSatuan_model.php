<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class ItemSatuan_model extends Conf {
	protected $table = 'item_satuan';
	protected $primaryKey = 'id';
    public $timestamps = false;
}
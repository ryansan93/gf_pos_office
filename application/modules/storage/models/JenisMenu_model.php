<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JenisMenu_model extends Conf {
	protected $table = 'jenis_menu';
	protected $primaryKey = 'id';
	public $timestamps = false;
}
<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Shift_model extends Conf{
	protected $table = 'shift';
	protected $primaryKey = 'id';
	public $timestamps = false;
}

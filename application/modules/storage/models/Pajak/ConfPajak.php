<?php
namespace Model\Storage\Pajak;
use \Illuminate\Database\Eloquent\Model as Eloquent;

class ConfPajak extends Eloquent
{
	public $timestamps = false;
	public function __construct(){
		$this->setConnection('pajak');
	}
}
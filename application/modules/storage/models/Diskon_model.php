<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Diskon_model extends Conf{
	protected $table = 'diskon';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'DSK';
	public $timestamps = false;

	public function detail()
	{
		return $this->hasMany('\Model\Storage\DiskonDet_model', 'diskon_kode', 'kode');
	}
}

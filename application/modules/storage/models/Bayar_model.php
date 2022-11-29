<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Bayar_model extends Conf {
	protected $table = 'bayar';

	public function jual()
	{
		return $this->hasOne('\Model\Storage\Jual_model', 'kode_faktur', 'faktur_kode');
	}

	public function jenis_kartu()
	{
		return $this->hasOne('\Model\Storage\JenisKartu_model', 'kode_jenis_kartu', 'jenis_kartu_kode');
	}
}
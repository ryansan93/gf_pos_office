<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class BayarNonKasir_model extends Conf{
	protected $table = 'bayar_non_kasir';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'BNK';
	public $timestamps = false;

	public function bayar()
	{
		return $this->hasMany('\Model\Storage\Bayar_model', 'kode_bayar_non_kasir', 'kode')->with(['jual']);
	}

	public function jenis_kartu()
	{
		return $this->hasOne('\Model\Storage\JenisKartu_model', 'kode_jenis_kartu', 'jenis_kartu_kode');
	}
}

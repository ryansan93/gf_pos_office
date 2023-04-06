<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class PinOtorisasi_model extends Conf {
	protected $table = 'pin_otorisasi';
    public $timestamps = false;

    public function user()
	{
		return $this->hasOne('\Model\Storage\User_model', 'id_user', 'user_id')->with(['detail_user']);
	}

	public function det_fitur()
	{
		return $this->hasOne('\Model\Storage\DetFitur_model', 'id_detfitur', 'id_detfitur');
	}
}
<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Member_model extends Conf{
	protected $table = 'member';
	protected $primaryKey = 'kode_member';
	protected $kodeTable = 'MEM';

	public function member_group()
	{
		return $this->hasOne('\Model\Storage\MemberGroup_model', 'id', 'member_group_id');
	}
}

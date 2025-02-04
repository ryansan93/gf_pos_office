<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JenisPesanan_model extends Conf{
	protected $table = 'jenis_pesanan';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'JPE';
	public $timestamps = false;

	public function getData($id = null, $column = 'nama') {
		$data = null;

        $sql_id = "";
        if ( !empty($id) ) {
			if ( !empty($sql_status) ) {
				$sql_id = "and jp.".$column." in ('".implode("', '", $id)."')";
			} else {
				$sql_id = "where jp.".$column." in ('".implode("', '", $id)."')";
			}
        }

		$sql = "
			select 
				jp.*
			from jenis_pesanan jp
			".$sql_id."
			order by
				jp.nama asc
		";
		$d_jp = $this->hydrateRaw($sql);

        if ( !empty($d_jp) && $d_jp->count() > 0 ) {
            $data = $d_jp->toArray();
        }

		return $data;
	}
}

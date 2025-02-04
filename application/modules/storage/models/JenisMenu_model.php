<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JenisMenu_model extends Conf {
	protected $table = 'jenis_menu';
	protected $primaryKey = 'id';
	public $timestamps = false;

	public function getData($id = null, $column = 'nama', $status = 1, $date = null) {
		$data = null;
        
		$sql_status = "";
		if ( !empty($status) ) {
			$sql_status = "where jm.status = ".$status;
		}

        $sql_id = "";
        if ( !empty($id) ) {
			if ( !empty($sql_status) ) {
				$sql_id = "and jm.".$column." in ('".implode("', '", $id)."')";
			} else {
				$sql_id = "where jm.".$column." in ('".implode("', '", $id)."')";
			}
        }

		$sql = "
			select 
				jm.*
			from jenis_menu jm
			".$sql_status."
			".$sql_id."
			order by
				jm.nama asc
		";
		$d_jm = $this->hydrateRaw($sql);

        if ( !empty($d_jm) && $d_jm->count() > 0 ) {
            $data = $d_jm->toArray();
        }

		return $data;
	}
}
<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class KategoriMenu_model extends Conf {
	protected $table = 'kategori_menu';
	protected $primaryKey = 'id';

	public function getData($id = null, $column = 'nama', $status = 1, $date = null) {
		$data = null;
        
		$sql_status = "";
		if ( !empty($status) ) {
			$sql_status = "where km.status = ".$status;
		}

        $sql_id = "";
        if ( !empty($id) ) {
			if ( !empty($sql_status) ) {
				$sql_id = "and km.".$column." in ('".implode("', '", $id)."')";
			} else {
				$sql_id = "where km.".$column." in ('".implode("', '", $id)."')";
			}
        }

		$sql = "
			select 
				km.*
			from kategori_menu km
			".$sql_status."
			".$sql_id."
			order by
				km.nama asc
		";
		$d_km = $this->hydrateRaw($sql);

        if ( !empty($d_km) && $d_km->count() > 0 ) {
            $data = $d_km->toArray();
        }

		return $data;
	}
}
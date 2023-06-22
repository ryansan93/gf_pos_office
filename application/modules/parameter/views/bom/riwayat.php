<div class="col-xs-12 no-padding">
	<button type="button" class="col-xs-12 btn btn-success pull-right" onclick="bom.changeTabActive(this)" data-href="action" data-edit=""><i class="fa fa-plus"></i> ADD</button>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<!-- <div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Awal</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="startDate" id="StartDate">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Akhir</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="endDate" id="EndDate">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div> -->

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Menu</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control menu_riwayat" multiple="multiple" data-required="1">
			<option value="all">ALL</option>
			<?php foreach ($menu as $k_menu => $v_menu): ?>
				<option value="<?php echo $v_menu['kode_menu']; ?>"><?php echo strtoupper($v_menu['branch_kode'].' | '.$v_menu['nama']); ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="bom.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 search left-inner-addon pull-right no-padding" style="padding-bottom: 10px;">
	<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_riwayat" placeholder="Search" onkeyup="filter_all(this)">
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<span>* Klik pada baris untuk melihat detail</span>
	<small>
		<table class="table table-bordered tbl_riwayat">
			<thead>
				<tr>
					<th class="col-xs-2">Tanggal</th>
					<th class="col-xs-4">Nama BOM</th>
					<th class="col-xs-2">Branch</th>
					<th class="col-xs-4">Menu</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="4">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>
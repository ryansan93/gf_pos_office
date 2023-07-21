<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<button type="button" class="col-xs-12 btn btn-success pull-right" onclick="terima.changeTabActive(this)" data-href="action" data-edit=""><i class="fa fa-plus"></i> ADD</button>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
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
</div>

<div class="col-xs-12 no-padding">
	<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="terima.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 search left-inner-addon pull-right no-padding" style="padding-bottom: 10px;">
	<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_riwayat" placeholder="Search" onkeyup="filter_all(this)">
</div>

<div class="col-xs-12 no-padding">
	<span>* Klik pada baris untuk melihat detail</span>
	<small>
		<table class="table table-bordered tbl_riwayat">
			<thead>
				<tr>
					<th class="col-xs-1">No. PO</th>
					<th class="col-xs-1">Tanggal Terima</th>
					<th class="col-xs-1">Kode Terima</th>
					<th class="col-xs-2">Gudang</th>
					<th class="col-xs-2">Supplier</th>
					<th class="col-xs-1">NPWP</th>
					<th class="col-xs-1">COA SAP</th>
					<th class="col-xs-2">Keterangan COA SAP</th>
					<th class="col-xs-1">Grand Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="9">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-default pull-right" onclick="terima.exportExcel(this)"><i class="fa fa-file-excel-o"></i> Export</button>
</div>
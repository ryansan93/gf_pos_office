<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
	<div class="col-md-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
		<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_report_hutang" placeholder="Search" onkeyup="filter_all(this)">
	</div>
	<small>
		<table class="table table-bordered tbl_report_hutang" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Tgl Faktur</th>
					<th class="col-xs-1">Kasir</th>
					<th class="col-xs-1">Kode Faktur</th>
					<th class="col-xs-1">Group Member</th>
					<th class="col-xs-1">Member</th>
					<th class="col-xs-1">Hutang</th>
					<th class="col-xs-1">Total Bayar</th>
					<th class="col-xs-2">Remark</th>
					<th class="col-xs-1">Jenis Bayar</th>
					<th class="col-xs-1">Tanggal Bayar</th>
					<th class="col-xs-1">Bayar</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="11">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>
<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-default pull-right" onclick="hutang.exportExcel()"><label class="control-lable" style="margin-bottom: 0px;"><i class="fa fa-file-excel-o"></i> Export Excel</label></button>
</div>
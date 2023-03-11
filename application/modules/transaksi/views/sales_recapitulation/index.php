<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding" style="padding: 10px; height: 100%;">
				<div class="col-xs-12 no-padding" style="height: 10%;">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-2 no-padding" style="padding-right: 5px;">
							<div class="input-group date datetimepicker" name="startDate" id="StartDate">
						        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</div>
						<div class="col-xs-2 no-padding" style="padding-left: 5px;">
							<div class="input-group date datetimepicker" name="endDate" id="EndDate">
						        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</div>
						<div class="col-xs-4 no-padding" style="padding-left: 5px;">
							<select class="form-control branch" data-required="1">
								<option value="">-- Pilih Branch --</option>
								<?php foreach ($branch as $key => $value): ?>
									<option value="<?php echo $value['kode_branch']; ?>"><?php echo $value['kode_branch'].' | '.$value['nama']; ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<div class="col-xs-2 no-padding" style="padding-left: 10px;">
							<button type="button" class="btn btn-primary" onclick="sr.getLists();"><i class="fa fa-search"></i> Tampilkan</button>
						</div>
					</div>
					<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				</div>
				<div class="col-xs-12 no-padding" style="height: 90%; overflow-y: auto;">
					<div class="col-xs-12 no-padding">
						<div class="col-lg-12 search right-inner-addon no-padding">
							<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-target="tbl_list_data" placeholder="Search" onkeyup="filter_all(this)">
						</div>
					</div>
					<small>
						<table class="table table-bordered tbl_list_data" style="margin-bottom: 0px;">
							<thead>
								<tr>
									<th class="col-xs-1">Tgl Trans</th>
									<th class="col-xs-2">Member</th>
									<th class="col-xs-1">Kode Pesanan</th>
									<th class="col-xs-1">Kode Faktur</th>
									<th class="col-xs-1">Kode Faktur Utama</th>
									<th class="col-xs-1">Waitress</th>
									<th class="col-xs-1">Kasir</th>
									<th class="col-xs-1">Total Nota</th>
									<th class="col-xs-1">Total Nota Gabungan</th>
									<th class="col-xs-1">Grand Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="10">Data tidak ditemukan.</td>
								</tr>
							</tbody>
						</table>
					</small>
				</div>
			</div>
		</form>
	</div>
</div>
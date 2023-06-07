<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding head">
					<div class="col-xs-12 no-padding">
						<div class="col-xs-12 no-padding">
							<label class="control-label">Branch</label>
						</div>
						<div class="col-xs-12 no-padding">
							<select class="form-control branch" data-required="1">
								<?php foreach ($branch as $k_branch => $v_branch): ?>
									<option value="<?php echo $v_branch['kode_branch']; ?>"><?php echo $v_branch['nama']; ?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>
					<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
						<div class="col-xs-12 no-padding">
							<label class="control-label">Tgl Awal</label>
						</div>
						<div class="col-xs-12 no-padding">
							<div class="input-group date datetimepicker" name="startDate" id="StartDate">
						        <input type="text" class="form-control text-center" placeholder="Tanggal Penjualan" data-required="1" />
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
						        <input type="text" class="form-control text-center" placeholder="Tanggal Penjualan" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</div>
					</div>

					<div class="col-xs-12 no-padding">
						<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="sp.getLists()"><i class="fa fa-search"></i> Tampilkan</button>
					</div>
				</div>
				<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding foot">
					<div class="col-xs-6 no-padding" style="padding-right: 5px;">
						<div class="col-xs-12 no-padding">
							<label class="control-label" style="padding-top: 0px; padding-bottom: 5px;">Data Real</label>
						</div>
						<div class="col-xs-12 no-padding">
							<small>
								<table class="table table-bordered tbl_real" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-xs-3">NO. BILL</th>
											<th class="col-xs-2">TANGGAL</th>
											<th class="col-xs-4">TOTAL</th>
											<th class="col-xs-3">PAJAK</th>
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
					</div>
					<div class="col-xs-6 no-padding" style="padding-left: 5px;">
						<div class="col-xs-12 no-padding">
							<label class="control-label" style="padding-top: 0px; padding-bottom: 5px;">Data Pajak</label>
						</div>
						<div class="col-xs-12 no-padding">
							<small>
								<table class="table table-bordered tbl_pajak" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-xs-3">NO. BILL</th>
											<th class="col-xs-2">TANGGAL</th>
											<th class="col-xs-4">TOTAL</th>
											<th class="col-xs-3">PAJAK</th>
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
					</div>
				</div>
				<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-xs-12 no-padding">
					<button type="button" class="btn btn-primary pull-right" onclick="sp.sinkron()"><i class="fa fa-check"></i> Sinkron Penjualan</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Branch</label>
					</div>
					<div class="col-xs-12 no-padding">
						<select class="form-control branch" data-required="1">
							<option value="">-- Pilih Branch --</option>
							<?php foreach ($branch as $k_branch => $v_branch): ?>
								<option value="<?php echo $v_branch['kode_branch']; ?>"><?php echo strtoupper($v_branch['kode_branch'].' | '.$v_branch['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>

				<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Supplier</label>
					</div>
					<div class="col-xs-12 no-padding">
						<select class="form-control supplier" data-required="1" multiple="multiple">
							<option value="all">ALL</option>
							<?php foreach ($supplier as $k_supl => $v_supl): ?>
								<option value="<?php echo $v_supl['kode']; ?>"><?php echo strtoupper($v_supl['nama']); ?></option>
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

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="beli.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 5px;"></div>
			<div class="col-xs-12 no-padding">
				<div id="penjualan_produk" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">
					<?php echo $report; ?>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Branch</label>
					</div>
					<div class="col-xs-12 no-padding">
						<select class="form-control branch" data-required="1">
							<option value="">-- Pilih Branch --</option>
							<?php foreach ($branch as $k_db => $v_db): ?>
								<option value="<?php echo $v_db['kode_branch']; ?>"><?php echo strtoupper($v_db['kode_branch'].' | '.$v_db['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Kasir</label>
					</div>
					<div class="col-xs-12 no-padding">
						<select class="form-control kasir" data-required="1">
							<option value="all">ALL</option>
							<?php foreach ($kasir as $k_kasir => $v_kasir): ?>
								<option value="<?php echo $v_kasir['id_user']; ?>"><?php echo strtoupper($v_kasir['nama_user']); ?></option>
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

				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="sh.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr></div>
			<div class="col-xs-12 no-padding">
				<?php echo $report; ?>
			</div>
		</form>
	</div>
</div>
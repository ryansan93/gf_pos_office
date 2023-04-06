<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Tanggal Transaksi</label>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date datetimepicker" name="tanggal" id="Tanggal">
					        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>

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

				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="ht.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
				<div class="col-xs-12 search left-inner-addon no-padding">
					<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_report" placeholder="Search" onkeyup="filter_all(this)">
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<?php echo $report; ?>
			</div>
		</form>
	</div>
</div>
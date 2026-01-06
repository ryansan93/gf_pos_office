<div class="row content-panel detailed">
	<div class="col-xs-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12" id="penerimaan-pakan">
				<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
					<div class="col-xs-12 no-padding"><label class="label-control">Tanggal Mulai Hitung</label></div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date" id="Tanggal" name="tanggal">
							<input type="text" class="form-control text-center" placeholder="Start" name="tgl_proses_awal" data-required="1" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
					<div class="col-xs-12 no-padding"><label class="label-control">Gudang</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control gudang" data-required="1">
							<?php foreach ($gudang as $key => $value) { ?>
								<option value="<?php echo $value['kode_gudang']; ?>"><?php echo strtoupper($value['nama']); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-12 no-padding"><label class="label-control">Item</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control item" data-required="1">
							<option value="all">ALL</option>
							<?php foreach ($item as $key => $value) { ?>
								<option value="<?php echo $value['kode']; ?>"><?php echo strtoupper($value['nama']); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-xs-12 no-padding"><hr style="margin: 10px 0px;"></div>
				<div class="col-xs-12 no-padding">
					<button type="button" class="col-xs-12 btn btn-primary" onclick="hs.hitungStok()">Proses</button>
				</div>
			</div>
		</form>
	</div>
</div>
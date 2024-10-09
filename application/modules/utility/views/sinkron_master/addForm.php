<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Printer</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Branch</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control branch" data-required="1">
					<option value="">-- Pilih Branch --</option>
					<?php if ( !empty($branch) ): ?>
						<?php foreach ($branch as $key => $value): ?>
							<option value="<?php echo $value['kode_branch']; ?>"><?php echo $value['nama']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Station</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control printer_station" data-required="1">
					<option value="">-- Pilih Station --</option>
					<?php if ( !empty($printer_station) ): ?>
						<?php foreach ($printer_station as $key => $value): ?>
							<option value="<?php echo $value['id']; ?>"><?php echo strtoupper($value['nama']); ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Sharing Name</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control sharing_name" placeholder="Sharing Name" data-required="1">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Lokasi</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control lokasi" placeholder="Lokasi" data-required="1">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Kategori Menu</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control kategori_menu" multiple="multiple">
					<?php if ( !empty($kategori_menu) ): ?>
						<?php foreach ($kategori_menu as $k_km => $v_km): ?>
							<option value="<?php echo $v_km['id']; ?>"><?php echo $v_km['nama']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Jumlah Print</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right jml_print" placeholder="Jumlah" data-tipe="integer" data-required="1">
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="printer.save()">
				<i class="fa fa-save"></i>
				Simpan
			</button>
		</div>
	</div>
</div>
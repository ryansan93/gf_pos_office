<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tanggal</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglInput" id="tglInput">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $data['tanggal']; ?>" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<!-- <select class="form-control branch" data-required="1"> -->
		<select class="branch" name="branch" width="100%" data-required="1">
			<option value="">Pilih Branch</option>
			<?php if ( !empty($branch) ): ?>
				<?php foreach ($branch as $key => $value): ?>
					<?php
						$selected = null;
						if ( $value['kode_branch'] == $data['branch_kode'] ) {
							$selected = 'selected';
						}
					?>
					<option value="<?php echo $value['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_menu" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-6">Menu</th>
					<th class="col-xs-4">Jumlah</th>
					<th class="col-xs-2"></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['waste_menu_item'] as $key => $value): ?>
					<tr>
						<td>
							<select class="form-control menu" data-required="1" data-val="<?php echo $value['menu_kode']; ?>">
								<option value="">Pilih Menu</option>
							</select>
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah" data-tipe="integer" data-required="1" placeholder="Jumlah" value="<?php echo angkaRibuan($value['jumlah']); ?>">
						</td>
						<td>
							<div class="col-xs-12 no-padding">
								<div class="col-xs-6 no-padding" style="padding-right: 5px;">
									<button class="col-xs-12 btn btn-primary" onclick="mg.addRow(this)"><i class="fa fa-plus"></i></button>
								</div>
								<div class="col-xs-6 no-padding" style="padding-left: 5px;">
									<button class="col-xs-12 btn btn-danger" onclick="mg.removeRow(this)"><i class="fa fa-times"></i></button>
								</div>
							</div>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding">
	<button class="col-xs-12 btn btn-primary" onclick="mg.edit(this)" data-id="<?php echo $data['id']; ?>"><i class="fa fa-save"></i> Simpan Perubahan</button>
</div>
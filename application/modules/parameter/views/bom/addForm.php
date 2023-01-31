<div class="col-xs-6 no-padding menu" style="margin-bottom: 5px; padding-right:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Menu</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control menu" multiple="multiple" data-required="1">
			<?php foreach ($menu as $k_menu => $v_menu): ?>
				<option value="<?php echo $v_menu['kode_menu']; ?>"><?php echo strtoupper($v_menu['branch_kode'].' | '.$v_menu['nama']); ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-6 no-padding additional_form hide" style="margin-bottom: 5px; padding-right:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control text-center nama" placeholder="Nama" data-required="1" maxlength="50" />
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Berlaku</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglBerlaku" id="TglBerlaku">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

	<div class="col-xs-12 no-padding">
		<div class="col-xs-1 no-padding">
			<input type="checkbox" class="form-check-input additional cursor-p" style="height: 20px; margin: 0px;" onchange="bom.additionalForm(this)">
		</div>
		<div class="col-xs-11 no-padding"><label class="control-label">Additional BOM</label></div>
	</div>

	<div class="col-xs-12 no-padding additional_form hide">
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

		<div class="col-xs-12 no-padding">
			<small>
				<table class="table table-bordered tbl_satuan" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th class="col-xs-5">Satuan</th>
							<th class="col-xs-5">Pengali</th>
							<th class="col-xs-2"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="text" class="form-control uppercase satuan" placeholder="SATUAN" data-required="1" maxlength="10">
							</td>
							<td>
								<input type="text" class="form-control uppercase text-right pengali" placeholder="PENGALI" data-tipe="decimal" data-required="1" maxlength="11">
							</td>
							<td>
								<div class="col-xs-12 no-padding">
									<div class="col-xs-6 no-padding" style="padding-right: 5px;">
										<button type="button" class="col-xs-12 btn btn-danger" onclick="bom.removeRowSatuan(this)"><i class="fa fa-times"></i></button>
									</div>
									<div class="col-xs-6 no-padding" style="padding-left: 5px;">
										<button type="button" class="col-xs-12 btn btn-primary" onclick="bom.addRowSatuan(this)"><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</small>
		</div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-4">Item</th>
					<th class="col-xs-3">Satuan</th>
					<th class="col-xs-3">Jumlah</th>
					<th class="col-xs-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr class="search v-center data">
					<td>
						<select class="form-control item" data-required="1">
							<option value="">Pilih Item</option>
							<?php foreach ($item as $k_item => $v_item): ?>
								<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>'><?php echo strtoupper($v_item['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</td>
					<td>
						<select class="form-control satuan" data-required="1" disabled>
							<option value="">Pilih Satuan</option>
						</select>
					</td>
					<td>
						<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal"  maxlength="10" data-required="1">
					</td>
					<td>
						<div class="col-xs-12 no-padding">
							<div class="col-xs-6 no-padding" style="padding-right: 5px;">
								<button type="button" class="col-xs-12 btn btn-danger" onclick="bom.removeRow(this)"><i class="fa fa-times"></i></button>
							</div>
							<div class="col-xs-6 no-padding" style="padding-left: 5px;">
								<button type="button" class="col-xs-12 btn btn-primary" onclick="bom.addRow(this)"><i class="fa fa-plus"></i></button>
							</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="bom.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
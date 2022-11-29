<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<!-- <input type="text" class="col-xs-12 form-control supplier uppercase" placeholder="Supplier" data-required="1"> -->
		<select class="form-control branch selectpicker" data-live-search="true" data-required="1">
			<?php foreach ($branch as $key => $value): ?>
				<option value="<?php echo $value['kode_branch']; ?>"><?php echo $value['nama']; ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Adjust</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglAdjust" id="TglAdjust">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<textarea class="form-control keterangan" data-required="1"></textarea>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Group</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" class="form-control group uppercase" placeholder="Group" data-required="1" readonly>
					</td>
					<td>
						<select class="form-control item" data-required="1">
							<option value="">-- Pilih Item --</option>
							<?php if ( !empty($item) ): ?>
								<?php foreach ($item as $k_item => $v_item): ?>
									<option value="<?php echo $v_item['kode']; ?>" data-namagroup="<?php echo $v_item['group']['nama']; ?>" data-satuan="<?php echo $v_item['satuan']; ?>"><?php echo strtoupper($v_item['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</td>
					<td>
						<input type="text" class="form-control satuan uppercase" placeholder="Satuan" data-required="1" readonly>
					</td>
					<td>
						<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" data-required="1" maxlength="10">
					</td>
					<td>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-danger" onclick="adjout.removeRow(this);"><i class="fa fa-minus"></i></button>
						</div>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-primary" onclick="adjout.addRow(this);"><i class="fa fa-plus"></i></button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="adjout.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
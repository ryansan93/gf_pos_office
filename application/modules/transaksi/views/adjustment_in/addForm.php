<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Gudang</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control gudang" data-required="1">
			<option value="">Pilih Gudang</option>
			<?php if ( !empty($gudang) ): ?>
				<?php foreach ($gudang as $key => $value): ?>
					<option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
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
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga Satuan (Rp.)</th>
					<th class="col-xs-1">Total</th>
					<th class="col-xs-1">Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select class="form-control item" data-required="1">
							<option value="">-- Pilih Item --</option>
							<?php if ( !empty($item) ): ?>
								<?php foreach ($item as $k_item => $v_item): ?>
									<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>'><?php echo strtoupper($v_item['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</td>
					<td>
						<select class="form-control satuan" data-required="1" disabled>
							<option value="">Pilih Satuan</option>
						</select>
					</td>
					<td>
						<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" data-required="1" maxlength="10" onblur="adjin.hitTotal(this)">
					</td>
					<td>
						<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="15" onblur="adjin.hitTotal(this)">
					</td>
					<td>
						<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" maxlength="15" disabled>
					</td>
					<td>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-danger" onclick="adjin.removeRow(this);"><i class="fa fa-minus"></i></button>
						</div>
						<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
							<button type="button" class="btn btn-primary" onclick="adjin.addRow(this);"><i class="fa fa-plus"></i></button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="adjin.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
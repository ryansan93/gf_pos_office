<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Gudang</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control gudang" data-required="1">
			<?php foreach ($gudang as $key => $value): ?>
				<option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama']; ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left:5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Stok Opname</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglStokOpname" id="TglStokOpname">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 search left-inner-addon pull-right no-padding" style="padding-bottom: 10px;">
	<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_item" placeholder="Search" onkeyup="filter_all(this)">
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Kode</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga (Rp.)</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($item as $k_item => $v_item): ?>
					<tr class="search v-center data">
						<td class="kode"><?php echo $v_item['kode']; ?></td>
						<td>
							<?php echo strtoupper($v_item['nama']); ?>
						</td>
						<td>
							<select class="form-control satuan" data-required="1">
								<?php foreach ($v_item['satuan'] as $k_satuan => $v_satuan): ?>									
									<option value="<?php echo $v_satuan['satuan']; ?>" data-pengali="<?php echo $v_satuan['pengali']; ?>"><?php echo $v_satuan['satuan']; ?></option>
								<?php endforeach ?>
							</select>
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal"  maxlength="10">
						</td>
						<td>
							<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal"  maxlength="10">
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="so.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
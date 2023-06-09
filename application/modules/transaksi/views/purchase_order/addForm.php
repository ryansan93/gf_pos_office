<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tanggal PO</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglPo" id="TglPo">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">No. PO</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control no_po uppercase" placeholder="No. PO (MAX : 50)" maxlength="50" readonly>
	</div>
</div>

<div class="col-xs-6 no-padding hide" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control nama_pic uppercase" placeholder="Nama PiC (MAX : 50)" maxlength="50">
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px;">
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

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Supplier</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control supplier uppercase" placeholder="Supplier (MAX : 50)" data-required="1" maxlength="50">
	</div>
</div>

<?php if ( !empty($tax) ): ?>
	<div class="col-xs-6 no-padding" style="margin-top: 5px;">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-2 no-padding"><label class="control-label">Tax <?php echo (is_numeric( $tax['nilai'] ) && floor( $tax['nilai'] ) != $tax['nilai']) ? angkaDecimal($tax['nilai']) : angkaRibuan($tax['nilai']).'%'; ?></label></div>
			<div class="col-xs-10 no-padding">
				<input type="checkbox" class="cursor-p tax" data-id="<?php echo $tax['id']; ?>" data-nilai="<?php echo $tax['nilai']; ?>">
			</div>
		</div>
	</div>
<?php endif ?>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_detail" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-3">Item</th>
					<th class="col-xs-2">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-2">Harga Satuan (Rp.)</th>
					<th class="col-xs-2">Total</th>
					<th class="col-xs-1"></th>
				</tr>
			</thead>
			<tbody>
				<tr>
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
						<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-required="1" data-tipe="decimal" maxlength="12" onblur="po.hitTotal(this)" disabled>
					</td>
					<td>
						<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="14" onblur="po.hitTotal(this)" disabled>
					</td>
					<td>
						<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" maxlength="14" disabled>
					</td>
					<td>
						<div class="col-xs-12 no-padding">
							<div class="col-xs-6 no-padding">
								<button type="button" class="btn btn-danger" onclick="po.removeRow(this)"><i class="fa fa-times"></i></button>
							</div>
							<div class="col-xs-6 no-padding">
								<button type="button" class="btn btn-primary" onclick="po.addRow(this)"><i class="fa fa-plus"></i></button>
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
	<button type="button" class="col-xs-12 btn btn-primary" onclick="po.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
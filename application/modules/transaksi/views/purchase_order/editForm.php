<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right: 5px">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tanggal PO</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglPo" id="TglPo">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" data-tgl="<?php echo $data['tgl_po'] ?>" />
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
		<input type="text" class="col-xs-12 form-control no_po uppercase" placeholder="No. PO (MAX : 50)" maxlength="50" value="<?php echo $data['no_po'] ?>" readonly>
	</div>
</div>

<div class="col-xs-6 no-padding hide" style="margin-bottom: 5px; padding-left: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="col-xs-12 form-control nama_pic uppercase" placeholder="Nama PiC (MAX : 50)" value="<?php echo $data['pic'] ?>" maxlength="50">
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
					<?php
						$selected = null;
						if ( $value['kode_gudang'] == $data['gudang_kode'] ) {
							$selected = 'selected';
						}
					?>
					<option value="<?php echo $value['kode_gudang']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
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
		<!-- <input type="text" class="col-xs-12 form-control supplier uppercase" placeholder="Supplier (MAX : 50)" data-required="1" value="<?php echo $data['supplier'] ?>" maxlength="50"> -->
		<select class="form-control supplier" data-required="1">
			<option value="">Pilih Supplier</option>
			<?php if ( !empty($supplier) ): ?>
				<?php foreach ($supplier as $key => $value): ?>
					<?php
						$selected = null;
						if ( $value['kode'] == $data['supplier_kode'] ) {
							$selected = 'selected';
						}
					?>
					<option value="<?php echo $value['kode']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>

<?php if ( !empty($tax) ): ?>
	<div class="col-xs-6 no-padding" style="margin-top: 5px;">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-2 no-padding"><label class="control-label">Tax <?php echo (is_numeric( $tax['nilai'] ) && floor( $tax['nilai'] ) != $tax['nilai']) ? angkaDecimal($tax['nilai']) : angkaRibuan($tax['nilai']).'%'; ?></label></div>
			<div class="col-xs-10 no-padding">
				<?php
					$checked = null;
					if ( $tax['nilai'] == $data['tax'] ) {
						$checked = 'checked';
					}
				?>
				<input type="checkbox" class="cursor-p tax" data-id="<?php echo $tax['id']; ?>" data-nilai="<?php echo $tax['nilai']; ?>" <?php echo $checked; ?> >
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
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td>
							<select class="form-control item" data-required="1">
								<option value="">Pilih Item</option>
								<?php $satuan = null; ?>
								<?php foreach ($item as $k_item => $v_item): ?>
									<?php
										$selected = null;
										if ( $v_item['kode'] == $v_det['item_kode'] ) {
											$selected = 'selected';
											$satuan = $v_item['satuan'];
										}
									?>
									<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>' <?php echo $selected; ?> ><?php echo strtoupper($v_item['nama']); ?></option>
								<?php endforeach ?>
							</select>
						</td>
						<td>
							<select class="form-control satuan" data-required="1">
								<option value="">Pilih Satuan</option>
								<?php foreach ($satuan as $k_satuan => $v_satuan): ?>
									<?php
										$selected = null;
										if ( $v_satuan['satuan'] == $v_det['satuan'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $v_satuan['satuan']; ?>" data-pengali="<?php echo $v_satuan['pengali']; ?>" <?php echo $selected; ?> ><?php echo $v_satuan['satuan']; ?></option>
								<?php endforeach ?>
							</select>
						</td>
						<td>
							<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-required="1" data-tipe="decimal" maxlength="12" onblur="po.hitTotal(this)" value="<?php echo (is_numeric( $v_det['jumlah'] ) && floor( $v_det['jumlah'] ) != $v_det['jumlah']) ? angkaDecimal($v_det['jumlah']) : angkaRibuan($v_det['jumlah']); ?>">
						</td>
						<td>
							<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="14" onblur="po.hitTotal(this)" value="<?php echo (is_numeric( $v_det['harga'] ) && floor( $v_det['harga'] ) != $v_det['harga']) ? angkaDecimal($v_det['harga']) : angkaRibuan($v_det['harga']); ?>">
						</td>
						<td>
							<?php $total = $v_det['jumlah'] * $v_det['harga']; ?>
							<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" maxlength="14" value="<?php echo (is_numeric( $total ) && floor( $total ) != $total) ? angkaDecimal($total) : angkaRibuan($total); ?>">
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
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="col-xs-12 btn btn-danger" onclick="po.changeTabActive(this)" data-href="action" data-edit="" data-id="<?php echo $data['no_po']; ?>"><i class="fa fa-times"></i> Batal</button>
	</div>
	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="col-xs-12 btn btn-primary" onclick="po.edit(this)" data-id="<?php echo $data['no_po']; ?>"><i class="fa fa-save"></i> Update</button>
	</div>
</div>
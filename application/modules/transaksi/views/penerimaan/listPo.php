<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr>
			<?php $satuan = null; ?>
			<td>
				<select class="form-control item" data-required="1">
					<option value="">Pilih Item</option>
					<?php foreach ($item as $k_item => $v_item): ?>
						<?php
							$selected = null;
							if ( $v_item['kode'] == $value['item_kode'] ) {
								$selected = 'selected';
								$satuan = $v_item['satuan'];
							}
						?>
						<option value="<?php echo $v_item['kode']; ?>" data-satuan='<?php echo json_encode($v_item['satuan']); ?>' <?php echo $selected; ?> ><?php echo strtoupper($v_item['nama']); ?></option>
					<?php endforeach ?>
				</select>
			</td>
			<td>
				<select class="form-control satuan" data-required="1" disabled>
					<option value="">Pilih Satuan</option>
					<?php if ( !empty($satuan) ): ?>
						<?php foreach ($satuan as $k_satuan => $v_satuan): ?>
							<?php
								$selected = null;
								if ( $v_satuan['satuan'] == $value['satuan'] ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $v_satuan['satuan']; ?>" data-pengali="<?php echo $v_satuan['pengali']; ?>" <?php echo $selected; ?> ><?php echo $v_satuan['satuan']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</td>
			<td>
				<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-required="1" data-tipe="decimal" maxlength="12" onblur="terima.hitTotal(this)" value="<?php echo (is_numeric( $value['jumlah'] ) && floor( $value['jumlah'] ) != $value['jumlah']) ? angkaDecimal($value['jumlah']) : angkaRibuan($value['jumlah']); ?>">
			</td>
			<td>
				<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="14" onblur="terima.hitTotal(this)" value="<?php echo (is_numeric( $value['harga'] ) && floor( $value['harga'] ) != $value['harga']) ? angkaDecimal($value['harga']) : angkaRibuan($value['harga']); ?>">
			</td>
			<td>
				<?php $total = $value['jumlah'] * $value['harga']; ?>
				<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" maxlength="14" value="<?php echo (is_numeric( $total ) && floor( $total ) != $total) ? angkaDecimal($total) : angkaRibuan($total); ?>">
			</td>
			<td>
				<div class="col-xs-12 no-padding">
					<div class="col-xs-6 no-padding">
						<button type="button" class="btn btn-danger" onclick="terima.removeRow(this)"><i class="fa fa-times"></i></button>
					</div>
					<div class="col-xs-6 no-padding">
						<button type="button" class="btn btn-primary" onclick="terima.addRow(this)"><i class="fa fa-plus"></i></button>
					</div>
				</div>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
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
			<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-required="1" data-tipe="decimal" maxlength="12" onblur="terima.hitTotal(this)" disabled>
		</td>
		<td>
			<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" data-required="1" maxlength="14" onblur="terima.hitTotal(this)" disabled>
		</td>
		<td>
			<input type="text" class="form-control text-right total uppercase" placeholder="Total" data-tipe="decimal" data-required="1" maxlength="14" disabled>
		</td>
		<td>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding">
					<button type="button" class="btn btn-danger" onclick="terima.removeRow(this)"><i class="fa fa-times"></i></button>
				</div>
				<div class="col-xs-6 no-padding">
					<button type="button" class="btn btn-primary" onclick="terima.addRow(this)"><i class="fa fa-plus"></i></button>
				</div>
			</div>
		</td>
	</tr>
<?php endif ?>
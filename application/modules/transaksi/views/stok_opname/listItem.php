<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_item => $v_item): ?>
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
				<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" maxlength="10" data-awal="<?php echo angkaDecimal($v_item['jumlah']); ?>" value="<?php echo angkaDecimal($v_item['jumlah']); ?>" onblur="so.hitTotal(this)">
			</td>
			<td>
				<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" maxlength="10" data-awal="<?php echo angkaDecimal($v_item['harga']); ?>" value="<?php echo angkaDecimal($v_item['harga']); ?>" onblur="so.hitTotal(this)">
			</td>
			<td class="total text-right">
				<?php echo angkaDecimal($v_item['harga'] * $v_item['jumlah']); ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
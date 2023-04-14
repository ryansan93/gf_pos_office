<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_item => $v_item): ?>
		<tr class="search v-center data">
			<td class="kode"><?php echo $v_item['kode']; ?></td>
			<td>
				<?php echo strtoupper($v_item['nama']); ?>
			</td>
			<td>
				<select class="form-control satuan" data-required="1" disabled>
					<?php foreach ($v_item['satuan'] as $k_satuan => $v_satuan): ?>									
						<option value="<?php echo $v_satuan['satuan']; ?>" data-pengali="<?php echo $v_satuan['pengali']; ?>"><?php echo $v_satuan['satuan']; ?></option>
					<?php endforeach ?>
				</select>
			</td>
			<td>
				<input type="text" class="form-control text-right jumlah uppercase" placeholder="Jumlah" data-tipe="decimal" maxlength="10" disabled>
			</td>
			<td>
				<input type="text" class="form-control text-right harga uppercase" placeholder="Harga" data-tipe="decimal" maxlength="10" disabled data-awal="<?php echo angkaDecimal($v_item['harga']); ?>" value="<?php echo angkaDecimal($v_item['harga']); ?>">
			</td>
			<td class="text-center">
				<input type="checkbox" onchange="so.choseItem(this)">
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
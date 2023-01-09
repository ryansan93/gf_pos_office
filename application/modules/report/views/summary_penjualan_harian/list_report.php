<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr>
			<td><?php echo isset($v_data['date']) ? tglIndonesia($v_data['date'], '-', ' ') : '-'; ?></td>
			<td><?php echo isset($v_data['kode_faktur']) ? $v_data['kode_faktur'] : '-'; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][1]) ? angkaRibuan($v_data['kategori_menu'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][2]) ? angkaRibuan($v_data['kategori_menu'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_menu'][3]) ? angkaRibuan($v_data['kategori_menu'][3]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['diskon'][1]) ? angkaRibuan($v_data['diskon'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['other_income']) ? angkaRibuan($v_data['other_income']) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['diskon'][2]) ? angkaRibuan($v_data['diskon'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['total']) ? angkaRibuan($v_data['total']) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][1]) ? angkaRibuan($v_data['kategori_pembayaran'][1]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][2]) ? angkaRibuan($v_data['kategori_pembayaran'][2]) : 0; ?></td>
			<td class="text-right"><?php echo isset($v_data['kategori_pembayaran'][3]) ? angkaRibuan($v_data['kategori_pembayaran'][3]) : 0; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="12">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
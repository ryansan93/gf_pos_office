<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $no = 1; ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search">
			<td><?php echo angkaRibuan($no); ?></td>
			<td><?php echo !empty($v_data['kategori']) ? $v_data['kategori'] : '-'; ?></td>
			<td><?php echo $v_data['jenis']; ?></td>
			<td><?php echo $v_data['menu_nama']; ?></td>
			<td class="text-right"><?php echo angkaRibuan($v_data['qty']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['total']); ?></td>
		</tr>
		<?php $no++; ?>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
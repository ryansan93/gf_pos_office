<?php if ( !empty($data) && count($data) ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p data" data-id="<?php echo $v_data['id']; ?>" onclick="so.changeTabActive(this)" data-href="action" data-edit="">
			<td><?php echo $v_data['kode_stok_opname']; ?></td>
			<td><?php echo tglIndonesia($v_data['tanggal'], '-', ' ', true); ?></td>
			<td><?php echo $v_data['nama']; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="3">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
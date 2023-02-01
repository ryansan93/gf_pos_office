<?php if ( !empty($data) && count($data) ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p data" data-id="<?php echo $v_data['id']; ?>" onclick="bom.changeTabActive(this)" data-href="action" data-edit="">
			<td><?php echo tglIndonesia($v_data['tgl_berlaku'], '-', ' ', true); ?></td>
			<td><?php echo !empty($v_data['nama_bom']) ? $v_data['nama_bom'] : '-'; ?></td>
			<td><?php echo !empty($v_data['nama_branch']) ? $v_data['nama_branch'] : '-'; ?></td>
			<td><?php echo !empty($v_data['nama_menu']) ? $v_data['nama_menu'] : '-'; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="4">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="cursor-p" onclick="adjin.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_adjin']; ?>" data-edit="" style="background-color: <?php echo $bg_color; ?>">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_adjin'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_adjin']; ?></td>
			<td><?php echo strtoupper($v_data['branch']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['keterangan']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="4">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
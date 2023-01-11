<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="cursor-p" onclick="adjout.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_adjout']; ?>" data-edit="" style="background-color: <?php echo $bg_color; ?>">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_adjout'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_adjout']; ?></td>
			<td><?php echo strtoupper($v_data['gudang']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['keterangan']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="4">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
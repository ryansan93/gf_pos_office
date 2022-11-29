<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<?php
			$bg_color = 'none';
			if ( $v_data['g_status'] == getStatus('submit') ) {
				$bg_color = '#9698ff';
			}
		?>

		<tr class="cursor-p" onclick="mutasi.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_mutasi']; ?>" data-edit="" style="background-color: <?php echo $bg_color; ?>">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_mutasi'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_mutasi']; ?></td>
			<td><?php echo strtoupper($v_data['nama_pic']); ?></td>
			<td><?php echo strtoupper($v_data['branch_asal']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['branch_tujuan']['nama']); ?></td>
			<td><?php echo ($v_data['g_status'] == getStatus('submit')) ? 'BELUM' : 'SUDAH'; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
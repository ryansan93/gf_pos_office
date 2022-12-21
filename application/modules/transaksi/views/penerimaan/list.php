<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p" onclick="terima.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_terima']; ?>" data-edit="">
			<td class="text-center"><?php echo $v_data['no_faktur']; ?></td>
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_terima'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_terima']; ?></td>
			<td><?php echo strtoupper($v_data['gudang']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['supplier']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="cursor-p" onclick="terima.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_terima']; ?>" data-edit="">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['beli']['tgl_beli'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['beli']['kode_beli']; ?></td>
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_terima'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_terima']; ?></td>
			<td><?php echo strtoupper($v_data['beli']['branch']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['beli']['supplier']['nama']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
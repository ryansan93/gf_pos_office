<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p" onclick="po.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['no_po']; ?>" data-edit="">
			<td class="text-center"><?php echo $v_data['no_po']; ?></td>
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_po'], '-', ' ')); ?></td>
			<td><?php echo strtoupper($v_data['gudang']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['supplier']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
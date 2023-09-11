<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr data-href="action" data-id="<?php echo $value['id']; ?>" onclick="mg.changeTabActive(this)">
			<td><?php echo strtoupper(tglIndonesia($value['tanggal'], '-', ' ')); ?></td>
			<td><?php echo strtoupper($value['branch']); ?></td>
			<td class="text-right"><?php echo angkaRibuan($value['jumlah']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="3">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
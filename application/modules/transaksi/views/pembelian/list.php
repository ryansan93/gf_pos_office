<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="cursor-p" onclick="beli.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_beli']; ?>" data-edit="">
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_beli'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_beli']; ?></td>
			<td><?php echo strtoupper($v_data['branch']['nama']); ?></td>
			<td><?php echo strtoupper($v_data['supplier']['nama']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['total']); ?></td>
			<td>
				<?php if ( !empty($v_data['lampiran']) ): ?>
					<a href="uploads/<?php echo $v_data['lampiran']; ?>" target="_blank"><?php echo $v_data['lampiran']; ?></a>
				<?php else: ?>
					-
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search">
			<td class="text-center"><?php echo $v_data['pesanan_kode'] ?></td>
			<td class="text-center"><?php echo $v_data['kode_faktur'] ?></td>
			<td>
				<?php echo $v_data['deskripsi'].' pada '.tglIndonesia($v_data['waktu'], '-', ' ').' '.substr($v_data['waktu'], 11, 5); ?>
			</td>
			<td>
				<?php echo !empty($v_data['keterangan']) ? $v_data['keterangan'] : '-'; ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="3">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
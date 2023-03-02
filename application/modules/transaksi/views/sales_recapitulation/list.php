<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<?php
			$bg_color = 'transparent';
			if ( isset($value['status_gabungan']) && $value['status_gabungan'] == 1 ) {
				$bg_color = '#ffb3b3';
			}
		?>
		<tr class="cursor-p search" onclick="sr.viewForm(this)" data-kode="<?php echo $value['kode_faktur']; ?>" style="background-color: <?php echo $bg_color; ?>;">
			<td><?php echo strtoupper(tglIndonesia($value['tgl_trans'], '-', ' ')).' '.substr($value['tgl_trans'], 11, 5); ?></td>
			<td><?php echo $value['member']; ?></td>
			<td><?php echo $value['kode_pesanan']; ?></td>
			<td><?php echo $value['kode_faktur']; ?></td>
			<td><?php echo (isset($value['status_gabungan']) && $value['status_gabungan'] == 1) ? $value['kode_faktur_utama'] : '-'; ?></td>
			<td><?php echo $value['nama_waitress']; ?></td>
			<td><?php echo $value['nama_kasir']; ?></td>
			<td class="text-right"><?php echo angkaDecimal($value['grand_total']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
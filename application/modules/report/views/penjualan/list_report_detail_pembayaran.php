<?php if ( !empty($data) ): ?>
	<?php foreach ($data as $k_tgl => $v_tgl): ?>
		<?php $idx_tgl = 0; ?>
		<?php foreach ($v_tgl['jenis_pembayaran'] as $k_jp => $v_jp): ?>
			<tr>
				<?php if ( $idx_tgl == 0 ): ?>
					<td rowspan="<?php echo count($v_tgl['jenis_pembayaran']); ?>"><?php echo strtoupper(tglIndonesia($v_tgl['tanggal'], '-', ' ')); ?></td>
				<?php endif ?>
				<td><?php echo strtoupper($v_jp['nama']); ?></td>
				<td class="text-right"><?php echo angkaDecimal($v_jp['total']); ?></td>
			</tr>
			<?php $idx_tgl++; ?>
		<?php endforeach ?>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="2">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
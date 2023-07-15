<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_total = 0; ?>
	<?php foreach ($data as $k_tanggal => $v_tanggal): ?>
		<?php $total_per_tanggal = 0; ?>
		<?php foreach ($v_tangga['detail'] as $k_kode => $v_kode): ?>
			<?php $total_per_kode = 0; ?>
			<?php foreach ($v_kode['detail'] as $k_det => $v_det): ?>
				<tr>
					<td class="text-center"><?php echo tglIndonesia($v_det['tgl_mutasi'], '-', ' '); ?></td>
					<td class="text-center"><?php echo $v_det['kode_mutasi']; ?></td>
					<td><?php echo $v_det['nama_gudang_asal']; ?></td>
					<td><?php echo $v_det['nama_gudang_tujuan']; ?></td>
					<td><?php echo $v_det['nama_item']; ?></td>
					<td><?php echo $v_det['coa']; ?></td>
					<td><?php echo $v_det['satuan']; ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
					<?php $total = $v_det['jumlah'] * $v_det['harga']; ?>
					<?php $grand_total += $total; ?>
					<?php $total_per_tanggal += $total; ?>
					<?php $total_per_kode += $total; ?>
					<td class="text-right"><?php echo angkaDecimal($total); ?></td>
				</tr>
			<?php endforeach ?>
			<tr>
				<td class="text-right" colspan="9"><b>TOTAL</b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_per_kode); ?></b></td>
			</tr>
		<?php endforeach ?>
		<tr>
			<td class="text-right" colspan="9"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_det['tgl_mutasi'], '-', ' '); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_per_tanggal); ?></b></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="9"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="10">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
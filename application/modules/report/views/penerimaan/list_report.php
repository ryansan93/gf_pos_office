<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_total = 0; ?>
	<?php foreach ($data as $k_tanggal => $v_tanggal): ?>
		<?php $total_per_tanggal = 0; ?>
		<?php foreach ($v_tanggal['detail'] as $k_kode => $v_kode): ?>
			<?php $total_per_kode = 0; ?>
			<?php foreach ($v_kode['detail'] as $k_det => $v_det): ?>
				<tr>
					<td class="text-center"><?php echo tglIndonesia($v_det['tgl_terima'], '-', ' '); ?></td>
					<td class="text-center"><?php echo $v_det['kode_terima']; ?></td>
					<td class="text-center"><?php echo $v_det['po_no']; ?></td>
					<td><?php echo $v_det['supplier']; ?></td>
					<td><?php echo $v_det['npwp_supplier']; ?></td>
					<td><?php echo $v_det['nama_gudang']; ?></td>
					<td><?php echo $v_det['nama_item']; ?></td>
					<td><?php echo $v_det['coa']; ?></td>
					<td><?php echo $v_det['satuan']; ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['jumlah_terima']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
					<?php $total = $v_det['jumlah_terima'] * $v_det['harga']; ?>
					<?php $grand_total += $total; ?>
					<?php $total_per_tanggal += $total; ?>
					<?php $total_per_kode += $total; ?>
					<td class="text-right"><?php echo angkaDecimal($total); ?></td>
				</tr>
			<?php endforeach ?>
			<tr>
				<td class="text-right" colspan="10"><b>TOTAL</b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_per_kode); ?></b></td>
			</tr>
		<?php endforeach ?>
		<tr>
			<td class="text-right" colspan="10"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_det['tgl_terima'], '-', ' '); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_per_tanggal); ?></b></td>
		</tr>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="10"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="11">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
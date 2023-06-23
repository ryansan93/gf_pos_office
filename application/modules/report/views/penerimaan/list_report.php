<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_jumlah = 0; $grand_total = 0; ?>
	<?php foreach ($data as $k_tanggal => $v_tanggal): ?>
		<?php $jumlah_per_tanggal = 0; $total_per_tanggal = 0; ?>
		<tr class="tanggal">
			<th colspan="7" style="background-color: #dedede;"><?php echo strtoupper(tglIndonesia($v_tanggal['tanggal'], '-', ' ')); ?></th>
		</tr>
		<?php foreach ($v_tanggal['group_item'] as $k_gi => $v_gi): ?>
			<?php $jumlah_per_group = 0; $total_per_group = 0; ?>
			<tr class="tanggal">
				<th colspan="7" style="background-color: #abacff;"><?php echo strtoupper($v_gi['nama']); ?></th>
			</tr>
			<?php foreach ($v_gi['beli'] as $k_beli => $v_beli): ?>
				<?php $idx_beli = 0; $jumlah_per_nota_beli = 0; $total_per_nota_beli = 0; ?>
				<?php foreach ($v_beli['detail'] as $k_det => $v_det): ?>
					<tr>
						<?php if ( $idx_beli == 0 ): ?>
							<td rowspan="<?php echo count($v_beli['detail']); ?>"><?php echo strtoupper($v_beli['kode_beli']); ?></td>
							<td rowspan="<?php echo count($v_beli['detail']); ?>"><?php echo strtoupper($v_beli['supplier']); ?></td>
						<?php endif ?>
						<td><?php echo strtoupper($v_det['nama_item']); ?></td>
						<td class="text-center"><?php echo strtoupper($v_det['satuan']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
						<td class="text-right"><?php echo angkaRibuan($v_det['jumlah']); ?></td>
						<?php // if ( $idx_beli == 0 ): ?>
							<td rowspan="<?php echo count($v_beli['detail']); ?>" class="text-right"><?php echo angkaDecimal($v_det['total']); ?></td>
						<?php // endif ?>
					</tr>
					<?php $idx_beli++; $jumlah_per_nota_beli += $v_det['jumlah']; $total_per_nota_beli += $v_det['total']; ?>
					<?php $jumlah_per_group += $v_det['jumlah']; $total_per_group += $v_det['total']; ?>
					<?php $jumlah_per_tanggal += $v_det['jumlah']; $total_per_tanggal += $v_det['total']; ?>
					<?php $grand_jumlah += $v_det['jumlah']; $grand_total += $v_det['total']; ?>
				<?php endforeach ?>
				<tr class="total">
					<td class="text-right" colspan="5"><b>TOTAL PER NOTA BELI</b></td>
					<td class="text-right"><b><?php echo angkaRibuan($jumlah_per_nota_beli); ?></b></td>
					<td class="text-right"><b><?php echo angkaDecimal($total_per_nota_beli); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr class="total">
				<td class="text-right" colspan="5"><b>TOTAL PER GROUP</b></td>
				<td class="text-right"><b><?php echo angkaRibuan($jumlah_per_group); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_per_group); ?></b></td>
			</tr>
		<?php endforeach ?>
		<tr class="total">
			<td class="text-right" colspan="5"><b>TOTAL PER TANGGAL</b></td>
			<td class="text-right"><b><?php echo angkaRibuan($jumlah_per_tanggal); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_per_tanggal); ?></b></td>
		</tr>
	<?php endforeach ?>
	<tr class="grand_total">
		<td class="text-right" colspan="5"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($grand_jumlah); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="7">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
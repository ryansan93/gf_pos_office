<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_jumlah = 0; ?>
	<?php $grand_total = 0; ?>
	<?php $grand_total_ppn = 0; ?>
	<?php $grand_total_after_ppn = 0; ?>
	<?php foreach ($data as $k_induk_menu => $v_induk_menu): ?>
		<tr class="induk_menu">
			<th colspan="6" style="background-color: #dedede;"><?php echo strtoupper($v_induk_menu['nama']); ?></th>
		</tr>
		<?php $jumlah = 0; ?>
		<?php $total = 0; ?>
		<?php $total_ppn = 0; ?>
		<?php $total_after_ppn = 0; ?>
		<?php foreach ($v_induk_menu['list_tanggal'] as $k_tanggal => $v_tanggal): ?>
			<?php $jml_by_tgl = 0; ?>
			<?php $total_by_tgl = 0; ?>
			<?php $total_ppn_by_tgl = 0; ?>
			<?php $total_after_ppn_by_tgl = 0; ?>
			<?php $idx_menu = 0; ?>
			<?php foreach ($v_tanggal['menu'] as $k_menu => $v_menu): ?>
				<tr>
					<?php if ( $idx_menu == 0 ): ?>
						<td rowspan="<?php echo count($v_tanggal['menu']); ?>"><?php echo strtoupper(tglIndonesia($v_tanggal['tanggal'], '-', ' ')); ?></td>
					<?php endif ?>
					<td>
						<div class="col-xs-12 no-padding"><?php echo strtoupper($v_menu['nama']); ?></div>
						<?php if ( !empty($v_menu['detail']) ): ?>
							<?php foreach ($v_menu['detail'] as $k_det => $v_det): ?>
								<div class="col-xs-12" style="font-size: 8pt;"><?php echo '- '.strtoupper($v_det['menu']['nama']); ?></div>
							<?php endforeach ?>
						<?php endif ?>
					</td>
					<td class="text-right"><?php echo angkaRibuan($v_menu['jumlah']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_menu['total']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_menu['ppn']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_menu['grand_total']); ?></td>
				</tr>
				<?php $idx_menu++; ?>
				<?php $jml_by_tgl += $v_menu['jumlah']; ?>
				<?php $total_by_tgl += $v_menu['total']; ?>
				<?php $total_ppn_by_tgl += $v_menu['ppn']; ?>
				<?php $total_after_ppn_by_tgl += $v_menu['grand_total']; ?>

				<?php $jumlah += $v_menu['jumlah']; ?>
				<?php $total += $v_menu['total']; ?>
				<?php $total_ppn += $v_menu['ppn']; ?>
				<?php $total_after_ppn += $v_menu['grand_total']; ?>

				<?php $grand_jumlah += $v_menu['jumlah']; ?>
				<?php $grand_total += $v_menu['total']; ?>
				<?php $grand_total_ppn += $v_menu['ppn']; ?>
				<?php $grand_total_after_ppn += $v_menu['grand_total']; ?>
			<?php endforeach ?>
			<tr class="total_by_tgl">
				<td class="text-right" colspan="2"><b>TOTAL PER TANGGAL</b></td>
				<td class="text-right"><b><?php echo angkaRibuan($jml_by_tgl); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_by_tgl); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_ppn_by_tgl); ?></b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_after_ppn_by_tgl); ?></b></td>
			</tr>
		<?php endforeach ?>
		<tr class="total">
			<td class="text-right" colspan="2"><b>TOTAL</b></td>
			<td class="text-right"><b><?php echo angkaRibuan($jumlah); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_ppn); ?></b></td>
			<td class="text-right"><b><?php echo angkaDecimal($total_after_ppn); ?></b></td>
		</tr>
	<?php endforeach ?>
	<tr class="grand_total">
		<td class="text-right" colspan="2"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($grand_jumlah); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_ppn); ?></b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_after_ppn); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
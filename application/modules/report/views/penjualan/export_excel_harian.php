<style type="text/css">
	td {
		vertical-align: top;
	}

	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>

<div style="width: 100%;">
	<h3>Laporan Penjualan Harian</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Branch</td>
			<td style="width: 3%;">: <?php echo strtoupper($data['branch']); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Shift</td>
			<td style="width: 3%;">: <?php echo strtoupper(implode(", ", $data['shift'])); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;" colspan="11">: <?php echo substr($data['start_date'], 0, 10).' s/d '.substr($data['end_date'], 0, 10); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th class="col-xs-1">Shift</th>
			<th class="col-xs-1">Tanggal</th>
			<th class="col-xs-1">Kasir</th>
			<th class="col-xs-1">Kode Faktur</th>
			<th class="col-xs-1">Pelanggan</th>
			<th class="col-xs-1">Menu</th>
			<th class="col-xs-1">Qty</th>
			<th class="col-xs-1">Price</th>
			<th class="col-xs-1">Sub Total</th>
			<th class="col-xs-1">PB1</th>
			<th class="col-xs-1">Service Charge</th>
			<th class="col-xs-1">Grand Total</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data['detail']) && count($data['detail']) > 0 ): ?>
			<?php $grand_total = 0; $grand_jumlah = 0; $grand_total_ppn = 0;  $grand_total_service_charge = 0; $grand_total_after_ppn = 0; ?>
			<?php foreach ($data['detail'] as $k_shift => $v_shift): ?>
				<!-- <tr class="shift">
					<th colspan="9" style="background-color: #abf5bf;"><?php echo strtoupper(tglIndonesia($v_shift['nama'], '-', ' ')); ?></th>
				</tr> -->
				<?php $idx_shift = 0; ?>
				<?php $total_shift = 0; ?>
				<?php $total_ppn_shift = 0; ?>
				<?php $total_service_charge_shift = 0; ?>
				<?php $total_after_ppn_shift = 0; ?>
				<?php $jumlah_shift = 0; ?>

				<?php
					$rowspan_shift = 1;
					foreach ($v_shift['detail'] as $k_tanggal => $v_tanggal) {
						$rowspan_shift += 1;
						foreach ($v_tanggal['kasir'] as $k_kasir => $v_kasir) {
							$rowspan_shift += 1;
							foreach ($v_kasir['faktur'] as $k_faktur => $v_faktur) {
								$rowspan_shift += count($v_faktur['menu']);
							}
						}
					}
				?>

				<?php foreach ($v_shift['detail'] as $k_tanggal => $v_tanggal): ?>
					<!-- <tr class="tanggal">
						<th colspan="9" style="background-color: #dedede;"><?php echo strtoupper(tglIndonesia($v_tanggal['tanggal'], '-', ' ')); ?></th>
					</tr> -->
					<?php $idx_tanggal = 0; ?>
					<?php $total = 0; ?>
					<?php $total_ppn = 0; ?>
					<?php $total_service_charge = 0; ?>
					<?php $total_after_ppn = 0; ?>
					<?php $jumlah = 0; ?>

					<?php
						$rowspan_tanggal = 1;
						foreach ($v_tanggal['kasir'] as $k_kasir => $v_kasir) {
							$rowspan_tanggal += 1;
							foreach ($v_kasir['faktur'] as $k_faktur => $v_faktur) {
								$rowspan_tanggal += count($v_faktur['menu']);
							}
						}
					?>

					<?php foreach ($v_tanggal['kasir'] as $k_kasir => $v_kasir): ?>
						<!-- <tr class="kasir">
							<th colspan="9" style="background-color: #99ccff;"><?php echo strtoupper($v_kasir['nama_kasir']); ?></th>
						</tr> -->
						<?php $idx_kasir = 0; ?>
						<?php $total_kasir_non_ppn = 0; ?>
						<?php $total_kasir_ppn = 0; ?>
						<?php $total_kasir_service_charge = 0; ?>
						<?php $total_kasir_after_ppn = 0; ?>
						<?php $jumlah_kasir = 0; ?>

						<?php
							$rowspan_kasir = 1;
							foreach ($v_kasir['faktur'] as $k_faktur => $v_faktur) {
								$rowspan_kasir += count($v_faktur['menu']);
							}
						?>

						<?php foreach ($v_kasir['faktur'] as $k_faktur => $v_faktur): ?>
							<?php $idx_faktur = 0; ?>
							<?php foreach ($v_faktur['menu'] as $k_menu => $v_menu): ?>
								<tr>
									<?php if ( $idx_shift == 0 ): ?>
										<td rowspan="<?php echo $rowspan_shift; ?>"><?php echo strtoupper($v_shift['nama']); ?></td>
									<?php endif ?>
									<?php if ( $idx_tanggal == 0 ): ?>
										<td rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo $v_tanggal['tanggal']; ?></td>
									<?php endif ?>
									<?php if ( $idx_kasir == 0 ): ?>
										<td rowspan="<?php echo $rowspan_kasir; ?>"><?php echo strtoupper($v_kasir['nama_kasir']); ?></td>
									<?php endif ?>
									<?php if ( $idx_faktur == 0 ): ?>
										<td rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo strtoupper($v_faktur['kode_faktur']); ?></td>
										<td rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo strtoupper($v_faktur['member']); ?></td>
									<?php endif ?>
									<td><?php echo strtoupper($v_menu['nama']); ?></td>
									<td class="number_format" align="right"><?php echo ($v_menu['jumlah']); ?></td>
									<td class="number_format" align="right"><?php echo ($v_menu['harga']); ?></td>
									<?php if ( $idx_faktur == 0 ): ?>
										<td class="decimal_number_format" align="right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo ($v_faktur['total']); ?></td>
										<td class="decimal_number_format" align="right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo ($v_faktur['ppn']); ?></td>
										<td class="decimal_number_format" align="right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo ($v_faktur['service_charge']); ?></td>
										<td class="decimal_number_format" align="right" rowspan="<?php echo count($v_faktur['menu']); ?>"><?php echo ($v_faktur['grand_total']); ?></td>
									<?php endif ?>

									<?php $jumlah_shift += $v_menu['jumlah']; $jumlah += $v_menu['jumlah']; $grand_jumlah += $v_menu['jumlah']; ?>
									<?php $jumlah_kasir += $v_menu['jumlah']; ?>
								</tr>
								<?php $idx_shift++; ?>
								<?php $idx_tanggal++; ?>
								<?php $idx_kasir++; ?>
								<?php $idx_faktur++; ?>
							<?php endforeach ?>
							<?php $total_shift += $v_faktur['total']; ?>
							<?php $total_ppn_shift += $v_faktur['ppn']; ?>
							<?php $total_service_charge_shift += $v_faktur['service_charge']; ?>
							<?php $total_after_ppn_shift += $v_faktur['grand_total']; ?>

							<?php $total += $v_faktur['total']; ?>
							<?php $total_ppn += $v_faktur['ppn']; ?>
							<?php $total_service_charge += $v_faktur['service_charge']; ?>
							<?php $total_after_ppn += $v_faktur['grand_total']; ?>

							<?php $total_kasir_non_ppn += $v_faktur['total']; ?>
							<?php $total_kasir_ppn += $v_faktur['ppn']; ?>
							<?php $total_kasir_service_charge += $v_faktur['service_charge']; ?>
							<?php $total_kasir_after_ppn += $v_faktur['grand_total']; ?>

							<?php $grand_total += $v_faktur['total']; ?>
							<?php $grand_total_ppn += $v_faktur['ppn']; ?>
							<?php $grand_total_service_charge += $v_faktur['service_charge']; ?>
							<?php $grand_total_after_ppn += $v_faktur['grand_total']; ?>
						<?php endforeach ?>
						<tr class="total">
							<td align="right" colspan="3"><b>TOTAL PER KASIR - <?php echo strtoupper($v_kasir['nama_kasir']); ?></b></td>
							<td class="number_format" align="right"><b><?php echo ($jumlah_kasir); ?></b></td>
							<td align="right"></td>
							<td class="decimal_number_format" align="right"><b><?php echo ($total_kasir_non_ppn); ?></b></td>
							<td class="decimal_number_format" align="right"><b><?php echo ($total_kasir_ppn); ?></b></td>
							<td class="decimal_number_format" align="right"><b><?php echo ($total_kasir_service_charge); ?></b></td>
							<td class="decimal_number_format" align="right"><b><?php echo ($total_kasir_after_ppn); ?></b></td>
						</tr>
					<?php endforeach ?>
					<tr class="total">
						<td align="right" colspan="4"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_tanggal['tanggal'], '-', ' '); ?></b></td>
						<td class="number_format" align="right"><b><?php echo ($jumlah); ?></b></td>
						<td align="right"></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total); ?></b></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total_ppn); ?></b></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total_service_charge); ?></b></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total_after_ppn); ?></b></td>
					</tr>
				<?php endforeach ?>
				<tr class="total">
					<td align="right" colspan="5"><b>TOTAL PER SHIFT - <?php echo strtoupper($v_shift['nama']); ?></b></td>
					<td class="number_format" align="right"><b><?php echo ($jumlah_shift); ?></b></td>
					<td align="right"></td>
					<td class="decimal_number_format" align="right"><b><?php echo ($total_shift); ?></b></td>
					<td class="decimal_number_format" align="right"><b><?php echo ($total_ppn_shift); ?></b></td>
					<td class="decimal_number_format" align="right"><b><?php echo ($total_service_charge_shift); ?></b></td>
					<td class="decimal_number_format" align="right"><b><?php echo ($total_after_ppn_shift); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr class="grand_total">
				<td align="right" colspan="6"><b>GRAND TOTAL</b></td>
				<td class="number_format" align="right"><b><?php echo ($grand_jumlah); ?></b></td>
				<td align="right"></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total); ?></b></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total_ppn); ?></b></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total_service_charge); ?></b></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total_after_ppn); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="12">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
<br>
<div style="width: 100%;">
	<h3>Jenis Pembayaran</h3>
</div>
<table border="1">
	<thead>
		<tr>
			<th class="col-xs-1">Tanggal</th>
			<th class="col-xs-7">Jenis Pembayaran</th>
			<th class="col-xs-4">Nilai</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data['pembayaran']) ): ?>
			<?php $grand_total = 0; ?>
			<?php foreach ($data['pembayaran'] as $k_tgl => $v_tgl): ?>
				<?php $idx_tgl = 0; ?>
				<?php $total_per_tanggal = 0; ?>
				<?php if ( !empty($v_tgl['jenis_pembayaran']) ): ?>
					<?php foreach ($v_tgl['jenis_pembayaran'] as $k_jp => $v_jp): ?>
						<tr>
							<?php if ( $idx_tgl == 0 ): ?>
								<td rowspan="<?php echo (count($v_tgl['jenis_pembayaran'])+1); ?>"><?php echo $v_tgl['tanggal']; ?></td>
							<?php endif ?>
							<td><?php echo strtoupper($v_jp['nama']); ?></td>
							<td class="decimal_number_format" align="right"><?php echo ($v_jp['total']); ?></td>
						</tr>
						<?php $idx_tgl++; ?>

						<?php $total_per_tanggal += $v_jp['total']; ?>
						<?php $grand_total += $v_jp['total']; ?>
					<?php endforeach ?>
					<tr>
						<td align="right"><b>TOTAL</b></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total_per_tanggal); ?></b></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
			<tr>
				<td colspan="2" align="right"><b>GRAND TOTAL</b></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="3">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
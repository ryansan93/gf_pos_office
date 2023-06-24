<style type="text/css">
	td {
		vertical-align: top;
	}
</style>

<div style="width: 100%;">
	<h3>Laporan Penjualan Produk</h3>
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
			<td style="width: 3%;" colspan="9">: <?php echo substr($data['start_date'], 0, 10).' s/d '.substr($data['end_date'], 0, 10); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th class="col-xs-1">Kategori</th>
			<th class="col-xs-1">Shift</th>
			<th class="col-xs-1">Tanggal</th>
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
			<?php $grand_jumlah = 0; ?>
			<?php $grand_total = 0; ?>
			<?php $grand_total_ppn = 0; ?>
			<?php $grand_total_service_charge = 0; ?>
			<?php $grand_total_after_ppn = 0; ?>
			<?php foreach ($data['detail'] as $k_shift => $v_shift): ?>
				<!-- <tr class="shift">
					<th colspan="8" style="background-color: #abf5bf;"><?php echo strtoupper(tglIndonesia($v_shift['nama'], '-', ' ')); ?></th>
				</tr> -->
				<?php $idx_shift = 0; ?>
				<?php $total_shift = 0; ?>
				<?php $total_ppn_shift = 0; ?>
				<?php $total_service_charge_shift = 0; ?>
				<?php $total_after_ppn_shift = 0; ?>
				<?php $jumlah_shift = 0; ?>

				<?php
					$rowspan_shift = 1;
					foreach ($v_shift['detail'] as $k_rjenis => $v_rjenis) {
						$rowspan_shift += 1;
						foreach ($v_rjenis['list_tanggal'] as $k_tanggal => $v_tanggal) {
							$rowspan_shift += (1+count($v_tanggal['menu']));
						}
					}
				?>

				<?php foreach ($v_shift['detail'] as $k_jenis => $v_jenis): ?>
					<!-- <tr class="jenis">
						<th colspan="8" style="background-color: #dedede;"><?php echo strtoupper($v_jenis['nama']); ?></th>
					</tr> -->
					<?php $idx_kategori = 0; ?>
					<?php $jumlah = 0; ?>
					<?php $total = 0; ?>
					<?php $total_ppn = 0; ?>
					<?php $total_service_charge = 0; ?>
					<?php $total_after_ppn = 0; ?>

					<?php
						$rowspan_kategori = 1;
						foreach ($v_jenis['list_tanggal'] as $k_rtanggal => $v_rtanggal) {
							$rowspan_kategori += (1+count($v_rtanggal['menu']));
						}
					?>

					<?php foreach ($v_jenis['list_tanggal'] as $k_tanggal => $v_tanggal): ?>
						<?php $idx_tanggal = 0; ?>
						<?php $jml_by_tgl = 0; ?>
						<?php $total_by_tgl = 0; ?>
						<?php $total_ppn_by_tgl = 0; ?>
						<?php $total_service_charge_by_tgl = 0; ?>
						<?php $total_after_ppn_by_tgl = 0; ?>

						<?php $rowspan_tanggal = (1 + count($v_tanggal['menu'])); ?>

						<?php foreach ($v_tanggal['menu'] as $k_menu => $v_menu): ?>
							<tr>
								<?php if ( $idx_shift == 0 ): ?>
									<td rowspan="<?php echo $rowspan_shift; ?>"><?php echo strtoupper($v_shift['nama']); ?></td>
								<?php endif ?>
								<?php if ( $idx_kategori == 0 ): ?>
									<td rowspan="<?php echo $rowspan_kategori; ?>"><?php echo strtoupper($v_jenis['nama']); ?></td>
								<?php endif ?>
								<?php if ( $idx_tanggal == 0 ): ?>
									<td rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo tglIndonesia($v_tanggal['tanggal'], '-', ' '); ?></td>
								<?php endif ?>
								<td>
									<div class="col-xs-12 no-padding"><?php echo strtoupper($v_menu['nama']); ?></div>
									<?php if ( !empty($v_menu['detail']) ): ?>
										<?php foreach ($v_menu['detail'] as $k_det => $v_det): ?>
											<div class="col-xs-12" style="font-size: 8pt;"><?php echo '- '.strtoupper($v_det['menu']['nama']); ?></div>
										<?php endforeach ?>
									<?php endif ?>
								</td>
								<td align="right"><?php echo angkaRibuan($v_menu['jumlah']); ?></td>
								<td align="right"><?php echo angkaRibuan($v_menu['harga']); ?></div></td>
								<td align="right"><?php echo angkaDecimal($v_menu['total']); ?></td>
								<td align="right"><?php echo angkaDecimal($v_menu['ppn']); ?></td>
								<td align="right"><?php echo angkaDecimal($v_menu['service_charge']); ?></td>
								<td align="right"><?php echo angkaDecimal($v_menu['grand_total']); ?></td>
							</tr>
							<?php $idx_shift++; ?>
							<?php $idx_kategori++; ?>
							<?php $idx_tanggal++; ?>

							<?php $total_by_tgl += $v_menu['total']; ?>
							<?php $jml_by_tgl += $v_menu['jumlah']; ?>
							<?php $total_ppn_by_tgl += $v_menu['ppn']; ?>
							<?php $total_service_charge_by_tgl += $v_menu['service_charge']; ?>
							<?php $total_after_ppn_by_tgl += $v_menu['grand_total']; ?>

							<?php $jumlah_shift += $v_menu['jumlah']; ?>
							<?php $total_shift += $v_menu['total']; ?>
							<?php $total_ppn_shift += $v_menu['ppn']; ?>
							<?php $total_service_charge_shift += $v_menu['service_charge']; ?>
							<?php $total_after_ppn_shift += $v_menu['grand_total']; ?>

							<?php $jumlah += $v_menu['jumlah']; ?>
							<?php $total += $v_menu['total']; ?>
							<?php $total_ppn += $v_menu['ppn']; ?>
							<?php $total_service_charge += $v_menu['service_charge']; ?>
							<?php $total_after_ppn += $v_menu['grand_total']; ?>

							<?php $grand_jumlah += $v_menu['jumlah']; ?>
							<?php $grand_total += $v_menu['total']; ?>
							<?php $grand_total_ppn += $v_menu['ppn']; ?>
							<?php $grand_total_service_charge += $v_menu['service_charge']; ?>
							<?php $grand_total_after_ppn += $v_menu['grand_total']; ?>
						<?php endforeach ?>
						<tr class="total_by_tgl">
							<td align="right" colspan="1"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_tanggal['tanggal'], '-', ' '); ?></b></td>
							<td align="right"><b><?php echo angkaRibuan($jml_by_tgl); ?></b></td>
							<td align="right"></td>
							<td align="right"><b><?php echo angkaDecimal($total_by_tgl); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($total_ppn_by_tgl); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($total_service_charge_by_tgl); ?></b></td>
							<td align="right"><b><?php echo angkaDecimal($total_after_ppn_by_tgl); ?></b></td>
						</tr>
					<?php endforeach ?>
					<tr class="total">
						<td align="right" colspan="2"><b>TOTAL PER KATEGORI - <?php echo strtoupper($v_jenis['nama']); ?></b></td>
						<td align="right"><b><?php echo angkaRibuan($jumlah); ?></b></td>
						<td align="right"></td>
						<td align="right"><b><?php echo angkaDecimal($total); ?></b></td>
						<td align="right"><b><?php echo angkaDecimal($total_ppn); ?></b></td>
						<td align="right"><b><?php echo angkaDecimal($total_service_charge); ?></b></td>
						<td align="right"><b><?php echo angkaDecimal($total_after_ppn); ?></b></td>
					</tr>
				<?php endforeach ?>
				<tr class="total">
					<td align="right" colspan="3"><b>TOTAL PER SHIFT - <?php echo strtoupper($v_shift['nama']); ?></b></td>
					<td align="right"><b><?php echo angkaRibuan($jumlah_shift); ?></b></td>
					<td align="right"></td>
					<td align="right"><b><?php echo angkaDecimal($total_shift); ?></b></td>
					<td align="right"><b><?php echo angkaDecimal($total_ppn_shift); ?></b></td>
					<td align="right"><b><?php echo angkaDecimal($total_service_charge_shift); ?></b></td>
					<td align="right"><b><?php echo angkaDecimal($total_after_ppn_shift); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr class="grand_total">
				<td align="right" colspan="4"><b>GRAND TOTAL</b></td>
				<td align="right"><b><?php echo angkaRibuan($grand_jumlah); ?></b></td>
				<td align="right"></td>
				<td align="right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				<td align="right"><b><?php echo angkaDecimal($grand_total_ppn); ?></b></td>
				<td align="right"><b><?php echo angkaDecimal($grand_total_service_charge); ?></b></td>
				<td align="right"><b><?php echo angkaDecimal($grand_total_after_ppn); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="10">Data tidak ditemukan.</td>
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
								<td rowspan="<?php echo (count($v_tgl['jenis_pembayaran'])+1); ?>"><?php echo tglIndonesia($v_tgl['tanggal'], '-', ' '); ?></td>
							<?php endif ?>
							<td><?php echo strtoupper($v_jp['nama']); ?></td>
							<td align="right"><?php echo angkaDecimal($v_jp['total']); ?></td>
						</tr>
						<?php $idx_tgl++; ?>

						<?php $total_per_tanggal += $v_jp['total']; ?>
						<?php $grand_total += $v_jp['total']; ?>
					<?php endforeach ?>
					<tr>
						<td align="right"><b>TOTAL</b></td>
						<td align="right"><b><?php echo angkaDecimal($total_per_tanggal); ?></b></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
			<tr>
				<td colspan="2" align="right"><b>GRAND TOTAL</b></td>
				<td align="right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="3">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
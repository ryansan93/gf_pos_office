<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<div style="width: 100%;">
	<h3>Laporan Mutasi Stok</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Gudang</td>
			<td style="width: 3%;">: <?php echo strtoupper($nama_gudang); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;">: <?php echo substr($start_date, 0, 10).' s/d '.substr($end_date, 0, 10); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th>Kode Brg</th>
			<th>Nama Brg</th>
			<th>Tanggal</th>
			<th>Transaksi</th>
			<th>Debit</th>
			<th>Kredit</th>
			<th>Satuan</th>
			<th>Hrg (Rp.)</th>
			<th>Sub Total (Rp.)</th>
			<th>Stok</th>
			<th>Nilai Stok (Rp.)</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data) && count($data) > 0 ): ?>
			<?php foreach ($data as $k_gudang => $v_gudang): ?>
				<tbody>
					<tr>
						<td colspan="11" style="background-color: #ededed;"><b><?php echo $v_gudang['nama']; ?></b></td>
					</tr>
				</tbody>
				<?php $urut_item = 0; ?>
				<?php foreach ($v_gudang['detail'] as $k_item => $v_item): ?>
					<?php $saldo = 0; ?>
					<?php $nilai_saldo = 0; ?>
					<?php $idx_item = 0; ?>
					<?php 
						$rowspan_item = 0;
						foreach ($v_item['detail'] as $k_tgl => $v_tgl) {
							$count_tgl_masuk = isset($v_tgl['masuk']) ? count($v_tgl['masuk']) : 0;
							$count_tgl_keluar = isset($v_tgl['keluar']) ? count($v_tgl['keluar']) : 0;
							$rowspan_item += $count_tgl_masuk + $count_tgl_keluar;
						} 
					?>
					<tbody class="row-wrapper">
						<?php foreach ($v_item['detail'] as $k_tgl => $v_tgl): ?>
							<?php $idx_tgl = 0; ?>
							<?php 
								$count_tgl_masuk = isset($v_tgl['masuk']) ? count($v_tgl['masuk']) : 0;
								$count_tgl_keluar = isset($v_tgl['keluar']) ? count($v_tgl['keluar']) : 0;
								$rowspan_tanggal = $count_tgl_masuk + $count_tgl_keluar; 
							?>
							<?php if ( isset($v_tgl['masuk']) ) { ?>
								<?php foreach ($v_tgl['masuk'] as $k_masuk => $v_masuk): ?>
									<?php $saldo += $v_masuk['masuk']; ?>
									<?php $nilai_saldo += ($v_masuk['masuk'] * $v_masuk['harga']); ?>
									<tr class="data">
										<?php if ( $idx_item == 0 ): ?>
											<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['kode']; ?></td>
											<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['nama']; ?></td>
	
											<?php $idx_item++; ?>
										<?php endif ?>
										<?php if ( $idx_tgl == 0 ): ?>
											<td rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo strtoupper($v_masuk['tgl_trans']); ?></td>
											<?php $idx_tgl++; ?>
										<?php endif ?>
										<td><?php echo $v_masuk['kode']; ?></td>
										<td class="decimal_number_format"><?php echo ($v_masuk['masuk']); ?></td>
										<td class="decimal_number_format"><?php echo (0); ?></td>
										<td><?php echo $v_item['satuan']; ?></td>
										<td class="decimal_number_format"><?php echo ($v_masuk['harga']); ?></td>
										<td class="decimal_number_format"><?php echo ($v_masuk['nilai']); ?></td>
										<td class="decimal_number_format"><?php echo ($saldo); ?></td>
										<td class="decimal_number_format"><?php echo ($nilai_saldo); ?></td>
									</tr>
								<?php endforeach ?>
							<?php } ?>
							<?php if ( isset($v_tgl['keluar']) ) { ?>
								<?php foreach ($v_tgl['keluar'] as $k_keluar => $v_keluar): ?>
									<?php $saldo -= $v_keluar['keluar']; ?>
									<?php $nilai_saldo -= ($v_keluar['keluar'] * $v_keluar['harga']); ?>
									<tr class="data">
										<?php if ( $idx_item == 0 ): ?>
											<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['kode']; ?></td>
											<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['nama']; ?></td>

											<?php $idx_item++; ?>
										<?php endif ?>
										<?php if ( $idx_tgl == 0 ): ?>
											<td rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo strtoupper($v_keluar['tgl_trans']); ?></td>
											<?php $idx_tgl++; ?>
										<?php endif ?>
										<td><?php echo $v_keluar['kode']; ?></td>
										<td class="decimal_number_format"><?php echo (0); ?></td>
										<td class="decimal_number_format"><?php echo ($v_keluar['keluar']); ?></td>
										<td><?php echo $v_item['satuan']; ?></td>
										<td class="decimal_number_format"><?php echo ($v_keluar['harga']); ?></td>
										<td class="decimal_number_format"><?php echo ($v_keluar['nilai']); ?></td>
										<td class="decimal_number_format"><?php echo ($saldo); ?></td>
										<td class="decimal_number_format"><?php echo ($nilai_saldo); ?></td>
									</tr>
								<?php endforeach ?>
							<?php } ?>
						<?php endforeach ?>
					</tbody>
				<?php endforeach ?>
			<?php endforeach ?>
		<?php else: ?>
			<tbody>
				<tr>
					<td colspan="12">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		<?php endif ?>
	</tbody>
</table>
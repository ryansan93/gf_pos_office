<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<div style="width: 100%;">
	<h3>Laporan Penerimaan Barang</h3>
</div>
<div style="width: 100%; font-size: 10pt;">
	<table>
		<tr>
			<td style="width: 5%;">Supplier</td>
			<td style="width: 3%;">: <?php echo strtoupper(implode(", ", $data['supplier'])); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Gudang</td>
			<td style="width: 3%;">: <?php echo strtoupper(implode(", ", $data['gudang'])); ?></td>
		</tr>
		<tr>
			<td style="width: 5%;">Periode</td>
			<td style="width: 3%;">: <?php echo substr($data['start_date'], 0, 10).' s/d '.substr($data['end_date'], 0, 10); ?></td>
		</tr>
	</table>
</div>
<table border="1">
	<thead>
		<tr>
			<th class="col-xs-1">Tanggal</th>
			<th class="col-xs-1">Kode Terima</th>
			<th class="col-xs-1">Kode PO</th>
			<th class="col-xs-1">Supplier</th>
			<th class="col-xs-1">NPWP</th>
			<th class="col-xs-1">Gudang</th>
			<th class="col-xs-1">Nama Item</th>
			<th class="col-xs-1">COA SAP</th>
			<th class="col-xs-1">Satuan</th>
			<th class="col-xs-1">Jumlah</th>
			<th class="col-xs-1">Harga (Rp.)</th>
			<th class="col-xs-1">Nilai</th>
		</tr>
	</thead>
	<tbody>
		<?php if ( !empty($data['detail']) && count($data['detail']) > 0 ): ?>
			<?php $grand_total = 0; ?>
			<?php foreach ($data['detail'] as $k_tanggal => $v_tanggal): ?>
				<?php $total_per_tanggal = 0; ?>
				<?php foreach ($v_tanggal['detail'] as $k_kode => $v_kode): ?>
					<?php $total_per_kode = 0; ?>
					<?php foreach ($v_kode['detail'] as $k_det => $v_det): ?>
						<tr>
							<td align="center"><?php echo $v_det['tgl_terima']; ?></td>
							<td align="center"><?php echo $v_det['kode_terima']; ?></td>
							<td align="center"><?php echo $v_det['po_no']; ?></td>
							<td><?php echo $v_det['supplier']; ?></td>
							<td><?php echo $v_det['npwp_supplier']; ?></td>
							<td><?php echo $v_det['nama_gudang']; ?></td>
							<td><?php echo $v_det['nama_item']; ?></td>
							<td><?php echo $v_det['coa']; ?></td>
							<td><?php echo $v_det['satuan']; ?></td>
							<td class="decimal_number_format" align="right"><?php echo number_format((float)$v_det['jumlah_terima'], 2, ',', ''); ?></td>
							<td class="decimal_number_format" align="right"><?php echo ($v_det['harga']); ?></td>
							<?php $total = $v_det['jumlah_terima'] * $v_det['harga']; ?>
							<?php $grand_total += $total; ?>
							<?php $total_per_tanggal += $total; ?>
							<?php $total_per_kode += $total; ?>
							<td class="decimal_number_format" align="right"><?php echo ($total); ?></td>
						</tr>
					<?php endforeach ?>
						<tr>
						<td align="right" colspan="11"><b>TOTAL</b></td>
						<td class="decimal_number_format" align="right"><b><?php echo ($total_per_kode); ?></b></td>
					</tr>
				<?php endforeach ?>
				<tr>
					<td align="right" colspan="11"><b>TOTAL PER TANGGAL - <?php echo tglIndonesia($v_det['tgl_terima'], '-', ' '); ?></b></td>
					<td class="decimal_number_format" align="right"><b><?php echo ($total_per_tanggal); ?></b></td>
				</tr>
			<?php endforeach ?>
			<tr>
				<td align="right" colspan="11"><b>GRAND TOTAL</b></td>
				<td class="decimal_number_format" align="right"><b><?php echo ($grand_total); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="12">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
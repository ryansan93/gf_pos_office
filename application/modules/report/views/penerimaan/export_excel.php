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
			<th class="col-xs-1">Gudang</th>
			<th class="col-xs-2">Nama Item</th>
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
			<?php foreach ($data['detail'] as $k_data => $v_data): ?>
				<tr>
					<td class="text-center"><?php echo tglIndonesia($v_data['tgl_terima'], '-', ' '); ?></td>
					<td class="text-center"><?php echo $v_data['kode_terima']; ?></td>
					<td class="text-center"><?php echo $v_data['po_no']; ?></td>
					<td><?php echo $v_data['supplier']; ?></td>
					<td><?php echo $v_data['nama_gudang']; ?></td>
					<td><?php echo $v_data['nama_item']; ?></td>
					<td><?php echo $v_data['coa']; ?></td>
					<td><?php echo $v_data['satuan']; ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_data['jumlah_terima']); ?></td>
					<td class="text-right"><?php echo angkaDecimal($v_data['harga']); ?></td>
					<?php $total = $v_data['jumlah_terima'] * $v_data['harga']; ?>
					<?php $grand_total += $total; ?>
					<td class="text-right"><?php echo angkaDecimal($total); ?></td>
				</tr>
			<?php endforeach ?>
			<tr>
				<td class="text-right" colspan="10" align="right"><b>TOTAL</b></td>
				<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
			</tr>
		<?php else: ?>
			<tr>
				<td colspan="11">Data tidak ditemukan.</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>
<style type="text/css">
	table tbody td {
		vertical-align: top;
	}
</style>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<table>
		<tbody>
			<tr>
				<td>Kode</td>
				<td>: <?php echo $data['kode_mutasi']; ?></td>
			</tr>
			<tr>
				<td>Nama PiC</td>
				<td>: <?php echo $data['nama_pic']; ?></td>
			</tr>
			<tr>
				<td>Asal</td>
				<td>: <?php echo $data['nama_gudang_asal']; ?></td>
			</tr>
			<tr>
				<td>Tujuan</td>
				<td>: <?php echo $data['nama_gudang_tujuan']; ?></td>
			</tr>
			<tr>
				<td>Tgl Mutasi</td>
				<td>: <?php echo strtoupper(tglIndonesia($data['tgl_mutasi'], '-', ' ')); ?></td>
			</tr>
			<tr>
				<td>No. SJ</td>
				<td>: <?php echo !empty($data['no_sj']) ? $data['no_sj'] : '-'; ?></td>
			</tr>
		</tbody>
	</table>
</div>

<br>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" border="1">
			<thead>
				<tr>
					<th class="col-xs-1">Group</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-2">COA SAP</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga (Rp.)</th>
					<th class="col-xs-2">Total (Rp.)</th>
				</tr>
			</thead>
			<tbody>
				<?php $grand_total = 0; ?>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td align="center"><?php echo $v_det['nama_group_item']; ?></td>
						<td><?php echo $v_det['nama_item']; ?></td>
						<td align="left"><?php echo $v_det['coa'].'<br>'.$v_det['ket_coa']; ?></td>
						<td align="center"><?php echo $v_det['satuan']; ?></td>
						<td align="right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
						<td align="right"><?php echo angkaDecimal($v_det['harga']); ?></td>
						<td align="right"><?php echo angkaDecimal($v_det['total']); ?></td>
						<?php $grand_total += $v_det['total']; ?>
					</tr>
				<?php endforeach ?>
				<tr>
					<td align="right" colspan="6"><b>TOTAL</b></td>
					<td align="right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<br>

<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label"><b>Keterangan</b></label>
	</div>
	<div class="col-xs-12 no-padding">
		<?php echo !empty($data['keterangan']) ? $data['keterangan'] : '-'; ?>
	</div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">No. Adjin</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['kode_adjin']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Gudang</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['gudang']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tgl Adjust</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tgl_adjin'], '-', ' ')); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['keterangan']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Group</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td class="text-center"><?php echo $v_det['item']['group']['nama']; ?></td>
						<td><?php echo $v_det['item']['nama']; ?></td>
						<td class="text-center"><?php echo $v_det['item']['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">History :</label>
	</div>
</div>
<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<ul>
			<?php foreach ($data['logs'] as $k_log => $v_log): ?>
				<li><?php echo $v_log['deskripsi'].' '.tglIndonesia($v_log['waktu'], '-', ' ').' '.substr($v_log['waktu'], 11, 5); ?></li>
			<?php endforeach ?>
		</ul>
	</div>
</div>
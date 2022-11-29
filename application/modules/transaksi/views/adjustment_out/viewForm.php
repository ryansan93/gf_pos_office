<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['branch']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tgl Adjust</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tgl_adjout'], '-', ' ')); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<?php echo $data['keterangan']; ?>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Group</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td class="text-center"><?php echo $v_det['item']['group']['nama']; ?></td>
						<td><?php echo $v_det['item']['nama']; ?></td>
						<td class="text-center"><?php echo $v_det['item']['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">History :</label>
	</div>
	<div class="col-xs-12 no-padding">
		<?php foreach ($data['log_tables'] as $k_lt => $v_lt): ?>
			<label class="control-label">- <?php echo $v_lt['deskripsi'].' '.$v_lt['waktu']; ?></label>
		<?php endforeach ?>
	</div>
</div>
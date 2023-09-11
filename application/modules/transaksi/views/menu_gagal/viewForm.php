<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tanggal</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tanggal'], '-', ' ')); ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['branch']['nama']); ?></label>
	</div>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_menu" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-4">Menu</th>
					<th class="col-xs-2">Jumlah</th>
					<th class="col-xs-6">Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['waste_menu_item'] as $key => $value): ?>
					<tr>
						<td>
							<?php echo $value['menu']['nama']; ?>
						</td>
						<td class="text-right">
							<?php echo angkaRibuan($value['jumlah']); ?>
						</td>
						<td>
							<?php echo !empty($value['keterangan']) ? $value['keterangan'] : '-'; ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-6 no-padding" style="padding-right: 5px;">
	<button class="col-xs-12 btn btn-danger" onclick="mg.delete(this)" data-id="<?php echo $data['id']; ?>"><i class="fa fa-trash"></i> Hapus</button>
</div>
<div class="col-xs-6 no-padding" style="padding-left: 5px;">
	<button class="col-xs-12 btn btn-primary" onclick="mg.changeTabActive(this)" data-id="<?php echo $data['id']; ?>" data-edit="edit" data-href="action"><i class="fa fa-edit"></i> Edit</button>
</div>
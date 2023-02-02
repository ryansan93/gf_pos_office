<?php if ( !empty($data['nama_menu']) ): ?>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-2 no-padding">
			<label class="control-label">Menu</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo strtoupper( $data['nama_menu'] ); ?></label>
		</div>
	</div>
<?php else: ?>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-2 no-padding">
			<label class="control-label">Nama BOM</label>
		</div>
		<div class="col-xs-10 no-padding">
			<label class="control-label">: <?php echo strtoupper( $data['nama_bom'] ); ?></label>
		</div>
	</div>
<?php endif ?>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tgl Berlaku</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper( tglIndonesia( $data['tgl_berlaku'], '-', ' ', true ) ); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Jumlah Porsi</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo angkaRibuan($data['jml_porsi']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Additional</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo ($data['additional'] == 1) ? 'YA' : 'TIDAK'; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding additional_form <?php echo ($data['additional'] == 0) ? 'hide' : null; ?>">
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

	<div class="col-xs-12 no-padding">
		<small>
			<table class="table table-bordered tbl_satuan" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-7">Satuan</th>
						<th class="col-xs-5">Pengali</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data['satuan']) ): ?>
						<?php foreach ($data['satuan'] as $k_satuan => $v_satuan): ?>
							<tr>
								<td><?php echo $v_satuan['satuan']; ?></td>
								<td class="text-right"><?php echo angkaRibuan($v_satuan['pengali']); ?></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Kode</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr class="search v-center data">
						<td><?php echo strtoupper($v_det['item_kode']); ?></td>
						<td><?php echo strtoupper($v_det['nama']); ?></td>
						<td class="text-center"><?php echo $v_det['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding" style="padding-right: 5px;">
		<button type="button" class="col-xs-12 btn btn-danger" data-id="<?php echo $data['id']; ?>" onclick="bom.delete(this)"><i class="fa fa-trash"></i> Hapus</button>
	</div>
	<div class="col-xs-6 no-padding" style="padding-left: 5px;">
		<button type="button" class="col-xs-12 btn btn-primary" data-id="<?php echo $data['id']; ?>" onclick="bom.changeTabActive(this)" data-href="action" data-edit="edit"><i class="fa fa-edit"></i> Edit</button>
	</div>
</div>
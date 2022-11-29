<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['nama_pic']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Asal</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['branch_asal']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tujuan</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['branch_tujuan']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tgl Mutasi</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tgl_mutasi'], '-', ' ')); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">No. SJ</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: 
			<?php if ( !empty($data['lampiran']) ): ?>
				<a href="uploads/<?php echo $data['lampiran']; ?>" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i><?php echo !empty($data['no_sj']) ? $data['no_sj'] : '-'; ?></a>
			<?php else: ?>
				<?php echo !empty($data['no_sj']) ? $data['no_sj'] : '-'; ?>
			<?php endif ?>
		</label>
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

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<?php echo $data['keterangan']; ?>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<?php if ( $data['g_status'] == getStatus('submit') ): ?>
	<?php if ( $akses['a_edit'] == 1 || $akses['a_delete'] == 1 ): ?>
		<?php if ( $akses['a_edit'] == 1 ): ?>
			<div class="col-xs-12 no-padding" style="padding-bottom: 5px;">
				<button type="button" class="col-xs-12 btn btn-primary pull-right" onclick="mutasi.changeTabActive(this)" data-id="<?php echo $data['kode_mutasi']; ?>" data-href="action" data-edit="edit"><i class="fa fa-edit"></i> Edit</button>
			</div>
		<?php endif ?>
		<?php if ( $akses['a_delete'] == 1 ): ?>
			<div class="col-xs-12 no-padding" style="padding-top: 5px;">
				<button type="button" class="col-xs-12 btn btn-danger pull-right" onclick="mutasi.delete(this)" data-kode="<?php echo $data['kode_mutasi']; ?>"><i class="fa fa-trash"></i> Hapus</button>
			</div>
		<?php endif ?>
		<div class="col-xs-12 no-padding"><hr></div>
	<?php endif ?>
<?php endif ?>
<?php if ( $akses['a_approve'] == 1 ): ?>
	<?php if ( $data['g_status'] == getStatus('submit') ): ?>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="mutasi.approve(this)" data-kode="<?php echo $data['kode_mutasi']; ?>"><i class="fa fa-check"></i> Terima</button>
		</div>
	<?php endif ?>
<?php endif ?>
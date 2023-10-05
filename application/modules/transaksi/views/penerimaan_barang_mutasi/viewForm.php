<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Kode</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['kode_mutasi']; ?></label>
	</div>
</div>

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
		<label class="control-label">: <?php echo $data['nama_gudang_asal']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-2 no-padding">
		<label class="control-label">Tujuan</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: <?php echo $data['nama_gudang_tujuan']; ?></label>
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

<div class="col-xs-12 no-padding">
	<div class="col-xs-2 no-padding">
		<label class="control-label">No. SJ</label>
	</div>
	<div class="col-xs-10 no-padding">
		<label class="control-label">: 
			<?php if ( !empty($data['lampiran']) ): ?>
				<a href="uploads/<?php echo $data['lampiran']; ?>" target="_blank" style="padding-right: 10px;"><?php echo !empty($data['no_sj']) ? $data['no_sj'] : '-'; ?></a>
			<?php else: ?>
				<?php echo !empty($data['no_sj']) ? $data['no_sj'] : '-'; ?>
			<?php endif ?>
		</label>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
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
						<td class="text-center"><?php echo $v_det['nama_group_item']; ?></td>
						<td><?php echo $v_det['nama_item']; ?></td>
						<td><?php echo $v_det['coa'].'<br>'.$v_det['ket_coa']; ?></td>
						<td class="text-center"><?php echo $v_det['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['total']); ?></td>
						<?php $grand_total += $v_det['total']; ?>
					</tr>
				<?php endforeach ?>
				<tr>
					<td class="text-right" colspan="6"><b>TOTAL</b></td>
					<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Keterangan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<?php echo $data['keterangan']; ?>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<?php if ( $akses['a_approve'] == 1 ): ?>
	<?php if ( $data['g_status'] == getStatus('submit') ): ?>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="pbm.approve(this)" data-kode="<?php echo $data['kode_mutasi']; ?>"><i class="fa fa-check"></i> Terima</button>
		</div>
	<?php endif ?>
<?php endif ?>
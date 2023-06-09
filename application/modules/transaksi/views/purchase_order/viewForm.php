<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Tanggal PO</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia( $data['tgl_po'], '-', ' ' )); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">No. PO</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['no_po']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding hide" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['pic']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Gudang</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['gudang']['nama']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Supplier</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['supplier']); ?></label>
	</div>
</div>

<?php if ( $data['tax'] > 0 ): ?>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Tax</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo (is_numeric( $data['tax'] ) && floor( $data['tax'] ) != $data['tax']) ? angkaDecimal($data['tax']) : angkaRibuan($data['tax']).'%'; ?></label>
		</div>
	</div>
<?php endif ?>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<small>
		<table class="table table-bordered tbl_detail" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-4">Item</th>
					<th class="col-xs-2">Satuan</th>
					<th class="col-xs-2">Jumlah</th>
					<th class="col-xs-2">Harga Satuan (Rp.)</th>
					<th class="col-xs-2">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php $grand_total = 0; ?>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td>
							<?php echo strtoupper($v_det['item']['nama']); ?>
						</td>
						<td class="text-center">
							<?php echo angkaRibuan($v_det['pengali']).' '.strtoupper($v_det['satuan']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['jumlah']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['harga']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['jumlah'] * $v_det['harga']); ?>
						</td>
					</tr>
					<?php $grand_total += ($v_det['jumlah'] * $v_det['harga']); ?>
				<?php endforeach ?>
				<tr>
					<td colspan="4" class="text-right"><b>TOTAL</b></td>
					<td class="text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 no-padding">
	<div class="col-xs-6 no-padding">
		<button type="button" class="btn btn-default pull-left" data-id="<?php echo exEncrypt($data['no_po']); ?>" onclick="po.exportPdf(this)"><i class="fa fa-print"></i> Print</button>
	</div>
	<div class="col-xs-6 no-padding">
		<?php if ( $data['done'] == 0 ): ?>
			<button type="button" class="btn btn-primary pull-right" style="margin-left: 5px;" onclick="po.changeTabActive(this)" data-href="action" data-edit="edit" data-id="<?php echo $data['no_po']; ?>"><i class="fa fa-edit"></i> Edit</button>
			<?php if ( $terima == 0 ): ?>
				<button type="button" class="btn btn-danger pull-right" style="margin-right: 5px;" onclick="po.delete(this)" data-id="<?php echo $data['no_po']; ?>"><i class="fa fa-trash"></i> Hapus</button>
			<?php endif ?>
		<?php endif ?>
	</div>
</div>
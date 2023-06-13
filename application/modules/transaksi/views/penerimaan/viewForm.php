<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Tanggal Terima</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia( $data['tgl_terima'], '-', ' ' )); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">No. Faktur</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper($data['no_faktur']); ?></label>
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

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">No. PO</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo !empty($data['po_no']) ? strtoupper($data['po_no']) : '-'; ?></label>
	</div>
</div>

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
					<tr class="data">
						<td>
							<?php echo strtoupper($v_det['item']['nama']); ?>
						</td>
						<td class="text-center">
							<?php echo angkaRibuan($v_det['pengali']).' '.strtoupper($v_det['satuan']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['jumlah_terima']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['harga']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['jumlah_terima'] * $v_det['harga']); ?>
						</td>
					</tr>
					<?php $grand_total += ($v_det['jumlah_terima'] * $v_det['harga']); ?>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" class="text-right"><b>GRAND TOTAL</b></td>
					<td class="text-right total"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tfoot>
		</table>
	</small>
</div>
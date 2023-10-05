<!-- <div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control branch" data-required="1">
			<option value="">-- Pilih Branch --</option>
			<?php foreach ($branch as $k_db => $v_db): ?>
				<option data-tokens="<?php echo $v_db['nama']; ?>" value="<?php echo $v_db['kode_branch']; ?>"><?php echo strtoupper($v_db['kode_branch'].' | '.$v_db['nama']); ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div> -->

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Kode</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo $data['kode_beli']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Nama PiC</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo $data['nama_pic']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo $data['branch']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Supplier</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo $data['supplier']['nama']; ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Tgl Beli</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tgl_beli'], '-', ' ')); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Bukti Pembelian</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: 
			<?php if ( !empty($data['lampiran']) ): ?>
				<a href="uploads/<?php echo $data['lampiran']; ?>" target="_blank" style="padding-right: 10px;"><?php echo $data['no_faktur']; ?></a>
			<?php else: ?>
				<?php echo $data['no_faktur']; ?>
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
					<th class="col-xs-1">Harga</th>
					<th class="col-xs-2">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php $grand_total= 0; ?>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr>
						<td>
							<?php echo strtoupper($v_det['item']['group']['nama']); ?>
						</td>
						<td>
							<?php echo strtoupper($v_det['item']['nama']); ?>
						</td>
						<td>
							<?php echo strtoupper($v_det['item']['satuan']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['jumlah']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['harga']); ?>
						</td>
						<td class="text-right">
							<?php echo angkaDecimal($v_det['total']); ?>
						</td>
					</tr>
					<?php $grand_total += $v_det['total']; ?>
				<?php endforeach ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="text-right" colspan="4"><b>Grand Total</b></td>
					<td class="grand_total text-right"><b><?php echo angkaDecimal($grand_total); ?></b></td>
				</tr>
			</tfoot>
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

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="beli.changeTabActive(this)" data-edit="EDIT" data-id="<?php echo $data['kode_beli']; ?>" data-href="action"><i class="fa fa-edit"></i> Edit</button>
	<button type="button" class="btn btn-danger pull-right" onclick="beli.delete(this)" data-id="<?php echo $data['kode_beli']; ?>" style="margin-right: 10px;"><i class="fa fa-trash"></i> Hapus</button>
</div>
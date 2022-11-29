<?php if ( !empty($data) ): ?>
	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Tanggal Terima</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['tgl_terima'], '-', ' ')); ?></label>
		</div>
	</div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Nama PiC</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo strtoupper($data['beli']['nama_pic']); ?></label>
		</div>
	</div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Branch</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo strtoupper($data['beli']['branch']['nama']); ?></label>
		</div>
	</div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Supplier</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo strtoupper($data['beli']['supplier']['nama']); ?></label>
		</div>
	</div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">Tgl Beli</label>
		</div>
		<div class="col-xs-9 no-padding">
			<label class="control-label">: <?php echo strtoupper(tglIndonesia($data['beli']['tgl_beli'], '-', ' ')); ?></label>
		</div>
	</div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<div class="col-xs-3 no-padding">
			<label class="control-label">No. Faktur</label>
		</div>
		<div class="col-xs-9 no-padding no_faktur">
			<label class="control-label">: 
				<?php if ( !empty($data['beli']['lampiran']) ): ?>
					<a href="uploads/<?php echo $data['beli']['lampiran']; ?>"><?php echo $data['beli']['no_faktur']; ?></a>
				<?php else: ?>
					<?php echo $data['beli']['no_faktur']; ?>
				<?php endif ?>
			</label>
		</div>
	</div>

	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

	<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
		<small>
			<table class="table table-bordered tbl_detail" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-1">Group</th>
						<th class="col-xs-2">Item</th>
						<th class="col-xs-1">Satuan</th>
						<th class="col-xs-1">Jumlah</th>
						<th class="col-xs-1">Jumlah Terima</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data['detail'] as $k_det => $v_det): ?>
						<tr class="data">
							<td>
								<?php echo $v_det['item']['group']['nama']; ?>
							</td>
							<td>
								<?php echo $v_det['item']['nama']; ?>
							</td>
							<td>
								<?php echo $v_det['item']['satuan']; ?>
							</td>
							<td>
								<?php echo angkaDecimal($v_det['jumlah']); ?>
							</td>
							<td>
								<?php echo angkaDecimal($v_det['jumlah_terima']); ?>
							</td>
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
			<textarea class="form-control keterangan" disabled><?php echo strtoupper($data['beli']['keterangan']); ?></textarea>
		</div>
	</div>
<?php else: ?>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding">
			<label class="control-label">Data tidak ditemukan.</label>
		</div>
	</div>
<?php endif ?>
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
		<label class="control-label">Tgl Bayar</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo tglIndonesia($data['tgl_bayar'], '-', ' '); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Total Tagihan</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo angkaDecimal($data['tot_tagihan']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Total Bayar</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: <?php echo angkaDecimal($data['tot_bayar']); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Jenis Pembayaran</label>
	</div>
	<div class="col-xs-9 no-padding">
		<?php
			$jenis_pembayaran = null;
			if ( $data['jenis_bayar'] == 'tunai' ) {
				$jenis_pembayaran = $data['jenis_bayar'];
			} else {
				$jenis_pembayaran = $data['jenis_bayar'].' | '.$data['jenis_kartu']['nama'];
			}
		?>
		<label class="control-label">: <?php echo strtoupper($jenis_pembayaran); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-3 no-padding">
		<label class="control-label">Bukti Pembayaran</label>
	</div>
	<div class="col-xs-9 no-padding">
		<label class="control-label">: 
			<?php if ( !empty($data['lampiran']) ): ?>
				<a href="uploads/<?php echo $data['lampiran']; ?>" target="_blank" style="padding-right: 10px;"><?php echo $data['lampiran']; ?></a>
			<?php else: ?>
				-
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
					<th class="col-xs-2">Tanggal</th>
					<th class="col-xs-4">Pelanggan</th>
					<th class="col-xs-2">Kode Faktur</th>
					<th class="col-xs-2">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( !empty($data['bayar']) ): ?>
					<?php foreach ($data['bayar'] as $key => $value): ?>
						<tr class="data">
							<td>
								<?php echo tglIndonesia($value['jual']['tgl_trans'], '-', ' '); ?>
							</td>
							<td>
								<?php echo strtoupper($value['jual']['member']); ?>
							</td>
							<td class="kode_faktur">
								<?php echo strtoupper($value['jual']['kode_faktur']); ?>
							</td>
							<td class="text-right grand_total">
								<?php echo angkaDecimal($value['jual']['grand_total']); ?>
							</td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="4">Data tidak ditemukan.</td>
					</tr>
				<?php endif ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-danger pull-right" onclick="pp.delete(this)" data-id="<?php echo $data['kode']; ?>"><i class="fa fa-trash"></i> Hapus</button>
</div>
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
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tgl Bayar</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglBayar" id="TglBayar">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Total Tagihan</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control text-right tot_tagihan" placeholder="Total Tagihan" data-required="1" data-tipe="decimal" readonly />
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Total Bayar</label>
	</div>
	<div class="col-xs-12 no-padding">
		<input type="text" class="form-control text-right tot_bayar" placeholder="Total Bayar" data-required="1" data-tipe="decimal" />
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Jenis Kartu</label>
	</div>
	<div class="col-xs-12 no-padding">
		<select class="form-control jenis_pembayaran" data-required="1">
			<option value="">-- Pilih Jenis Kartu --</option>
			<option value="tunai" data-tipe="tunai">TUNAI</option>
			<?php foreach ($jenis_kartu as $k_jk => $v_jk): ?>
				<option value="<?php echo $v_jk['kode_jenis_kartu']; ?>" data-tipe="kartu"><?php echo $v_jk['nama']; ?></option>
			<?php endforeach ?>
		</select>
	</div>
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Bukti Pembayaran</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-12 no-padding attachment" style="margin-top: 0px;">
			<a name="dokumen" class="text-right hide" target="_blank" style="padding-right: 10px;"><i class="fa fa-file"></i></a>
            <label class="" style="margin-bottom: 0px;">
                <input style="display: none;" placeholder="Dokumen" class="file_lampiran no-check" type="file" onchange="pp.showNameFile(this)" data-name="name" data-allowtypes="doc|pdf|docx|jpg|jpeg|png">
                <i class="glyphicon glyphicon-paperclip cursor-p" title="Attachment"></i>
            </label>
		</div>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1 text-center"><input type="checkbox" data-target="pilih" class="check_all"></th>
					<th class="col-xs-1">Tanggal</th>
					<th class="col-xs-2">Kasir</th>
					<th class="col-xs-2">Branch</th>
					<th class="col-xs-3">Pelanggan</th>
					<th class="col-xs-2">Kode Faktur</th>
					<th class="col-xs-1">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( !empty($data_hutang) ): ?>
					<?php foreach ($data_hutang as $key => $value): ?>
						<tr class="data">
							<td class="text-center">
								<input type="checkbox" target="pilih" class="check">
							</td>
							<td>
								<?php echo tglIndonesia($value['tgl_trans'], '-', ' '); ?>
							</td>
							<td>
								<?php echo strtoupper($value['nama_kasir']); ?>
							</td>
							<td>
								<?php echo strtoupper($value['branch']['nama']); ?>
							</td>
							<td>
								<?php echo strtoupper($value['member']); ?>
							</td>
							<td class="kode_faktur">
								<?php echo strtoupper($value['kode_faktur']); ?>
							</td>
							<td class="text-right grand_total">
								<?php echo angkaDecimal($value['grand_total']); ?>
							</td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="5">Data tidak ditemukan.</td>
					</tr>
				<?php endif ?>
			</tbody>
		</table>
	</small>
</div>

<div class="col-xs-12 no-padding"><hr></div>

<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-primary pull-right" onclick="pp.save()"><i class="fa fa-save"></i> Simpan</button>
</div>
<div class="modal-header no-padding header">
    <span class="modal-title"><label class="label-control">Tambah Pembayaran</label></span>
    <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<!-- <div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
		<div class="col-xs-12 no-padding"><label class="label-control">Status Pembayaran</label></div>
		<div class="col-xs-12 no-padding">
			<select class="col-xs-12 form-control status_pembayaran" data-required="1">
				<option value="">-- Pilih Status Pembayaran --</option>
				<option value="1">BARU</option>
				<option value="0">UPDATE</option>
			</select>
		</div>
	</div> -->
	<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
		<div class="col-xs-12 no-padding"><label class="label-control">Jenis Pembayaran</label></div>
		<div class="col-xs-12 no-padding">
			<select class="col-xs-12 form-control jenis_kartu" data-required="1">
				<option value="">-- Pilih Jenis Pembayaran --</option>
				<?php foreach ($jenis_kartu as $k_jk => $v_jk): ?>
					<option value="<?php echo $v_jk['kode_jenis_kartu'] ?>" data-cl="<?php echo $v_jk['cl']; ?>"><?php echo $v_jk['nama']; ?></option>
				<?php endforeach ?>
			</select>
		</div>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="col-xs-6 no-padding" style="padding-right: 5px;">
			<div class="col-xs-12 no-padding"><label class="label-control">Sisa Tagihan</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right sisa_tagihan" data-tipe="decimal" data-required="1" placeholder="Sisa Tagihan" value="<?php echo angkaDecimal($sisa_tagihan); ?>" readonly>
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="padding-left: 5px;">
			<div class="col-xs-12 no-padding"><label class="label-control">Jumlah Bayar</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right jml_bayar" data-tipe="decimal" data-required="1" placeholder="Jumlah Bayar">
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding non_tunai hide" style="margin-bottom: 10px;">
		<div class="col-xs-6 no-padding" style="padding-right: 5px;">
			<div class="col-xs-12 no-padding"><label class="label-control">No. Kartu</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control no_kartu" placeholder="No. Kartu">
			</div>
		</div>
		<div class="col-xs-6 no-padding" style="padding-left: 5px;">
			<div class="col-xs-12 no-padding"><label class="label-control">Nama Kartu</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control nama_kartu" placeholder="Nama Kartu">
			</div>
		</div>
	</div>
	<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	<div class="col-xs-12 no-padding">
		<button type="button" class="col-xs-12 btn btn-primary" onclick="sr.savePembayaran(this)" data-id="<?php echo $id_bayar; ?>" data-kode="<?php echo $kode_faktur; ?>"><i class="fa fa-check"></i> Apply</button>
	</div>
</div>
<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Jenis Kartu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="col-xs-6 form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding">
				<input type="radio" id="exclude" name="jenis_harga" <?php echo ($data['exclude'] == 1) ? 'checked' : ''; ?> >
				<label>Exclude Service Charge & PPN</label><br>
				<input type="radio" id="include" name="jenis_harga" <?php echo ($data['include'] == 1) ? 'checked' : ''; ?> >
				<label>Include Service Charge & PPN</label><br>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="jp.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>
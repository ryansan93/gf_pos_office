<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control nama uppercase" placeholder="Nama" data-required="1">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Deskripsi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control deskripsi uppercase" placeholder="Deskripsi"></textarea>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Kategori</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control kategori" data-required="1">
						<?php if ( !empty($kategori) ): ?>
							<?php foreach ($kategori as $key => $val): ?>
								<option value="<?php echo $val['id']; ?>"><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Jenis</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control jenis" data-required="1">
						<?php if ( !empty($jenis) ): ?>
							<?php foreach ($jenis as $key => $val): ?>
								<option value="<?php echo $val['id']; ?>"><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Branch</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control branch" name="branch[]" multiple="multiple" data-required="1">
						<?php if ( !empty($branch) ): ?>
							<?php foreach ($branch as $key => $val): ?>
								<option value="<?php echo $val['kode_branch']; ?>"><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Additional</label></div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px;">
					<input type="radio" id="1" name="age" value="1">
  					<label for="1">Ya</label><br>
  					<input type="radio" id="0" name="age" value="0" checked>
  					<label for="0">Tidak</label><br>
				</div>
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="menu.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
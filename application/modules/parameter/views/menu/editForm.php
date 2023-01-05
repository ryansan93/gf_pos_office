<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Deskripsi</label></div>
				<div class="col-xs-12 no-padding">
					<textarea class="form-control deskripsi uppercase" placeholder="Deskripsi"><?php echo $data['deskripsi']; ?></textarea>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Branch</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control branch" name="branch[]" multiple="multiple" data-required="1" disabled>
						<?php if ( !empty($branch) ): ?>
							<?php foreach ($branch as $key => $val): ?>
								<?php
									$selected = '';
									if ( $val['kode_branch'] == $data['branch_kode'] ) {
										$selected = 'selected';
									}
								?>
								<option value="<?php echo $val['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-6 no-padding" style="padding-right: 5px;">
					<div class="col-xs-12 no-padding"><label class="control-label">Kategori</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control kategori" data-required="1">
							<?php if ( !empty($kategori) ): ?>
								<?php foreach ($kategori as $key => $val): ?>
									<?php
										$selected = '';
										if ( $val['id'] == $data['kategori_menu_id'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</div>
				</div>
				<div class="col-xs-6 no-padding" style="padding-left: 5px;">
					<div class="col-xs-12 no-padding"><label class="control-label">Jenis</label></div>
					<div class="col-xs-12 no-padding">
						<select class="form-control jenis" data-required="1">
							<?php if ( !empty($jenis) ): ?>
								<?php foreach ($jenis as $key => $val): ?>
									<?php
										$selected = '';
										if ( $val['id'] == $data['jenis_menu_id'] ) {
											$selected = 'selected';
										}
									?>
									<option value="<?php echo $val['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($val['nama']); ?></option>
								<?php endforeach ?>
							<?php endif ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Additional</label></div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px;">
					<input type="radio" id="1" name="age" value="1" <?php echo ($data['additional'] == 1) ? 'checked' : ''; ?> >
  					<label for="1">Ya</label><br>
  					<input type="radio" id="0" name="age" value="0" <?php echo ($data['additional'] == 0) ? 'checked' : ''; ?> >
  					<label for="0">Tidak</label><br>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-2 no-padding"><label class="control-label">PB1</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="ppn col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['ppn'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-2 no-padding"><label class="control-label">Service Charge</label></div>
				<div class="col-xs-10">
					<input type="checkbox" class="service_charge col-xs-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['service_charge'] == 1) ? 'checked' : ''; ?> >
				</div>
			</div>
		</div>
		<div class="col-xs-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="menu.edit(this)" data-kode="<?php echo $data['kode_menu']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>
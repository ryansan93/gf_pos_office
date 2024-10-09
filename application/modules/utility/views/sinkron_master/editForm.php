<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Printer</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Branch</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control branch" data-required="1">
					<option value="">-- Pilih Branch --</option>
					<?php if ( !empty($branch) ): ?>
						<?php foreach ($branch as $key => $value): ?>
							<?php
								$selected = null;
								if ( $value['kode_branch'] == $data['branch_kode'] ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $value['kode_branch']; ?>" <?php echo $selected; ?> ><?php echo $value['nama']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Station</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control printer_station" data-required="1">
					<option value="">-- Pilih Station --</option>
					<?php if ( !empty($printer_station) ): ?>
						<?php foreach ($printer_station as $key => $value): ?>
							<?php
								$selected = null;
								if ( $value['id'] == $data['printer_station_id'] ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $value['id']; ?>" <?php echo $selected; ?> ><?php echo strtoupper($value['nama']); ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Sharing Name</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control sharing_name" placeholder="Sharing Name" data-required="1" value="<?php echo $data['sharing_name']; ?>">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Lokasi</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control lokasi" placeholder="Lokasi" data-required="1" value="<?php echo $data['lokasi']; ?>">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Kategori Menu</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control kategori_menu" multiple="multiple">
					<?php if ( !empty($kategori_menu) ): ?>
						<?php foreach ($kategori_menu as $k_km => $v_km): ?>
							<?php
								$selected = null;
								if ( in_array($v_km['id'], $data['kategori_menu']) ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $v_km['id']; ?>" <?php echo $selected; ?> ><?php echo $v_km['nama']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Jumlah Print</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="form-control text-right jml_print" placeholder="Jumlah" data-tipe="integer" data-required="1" value="<?php echo angkaRibuan($data['jml_print']); ?>">
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="printer.edit(this)" data-id="<?php echo $data['id']; ?>">
				<i class="fa fa-edit"></i>
				Simpan Perubahan
			</button>
		</div>
	</div>
</div>

<!-- <div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Kategori Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Nama</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="col-xs-6 form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Print Check List</label>
			</div>
			<div class="col-xs-12">
				<input type="radio" id="ya" name="print_cl" <?php echo ($data['print_cl'] == 1) ? 'checked' : null; ?> >
				<label>Ya</label><br>
				<input type="radio" id="tidak" name="print_cl" <?php echo ($data['print_cl'] == 0) ? 'checked' : null; ?> >
				<label>Tidak</label><br>
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">User</label>
			</div>
			<div class="col-xs-12 no-padding">
				<select class="form-control user" multiple="multiple">
					<?php if ( !empty($user) ): ?>
						<option value="all">ALL</option>
						<?php foreach ($user as $key => $value): ?>
							<?php
								$selected = null;
								if ( in_array($value['id_user'], $kategori_menu_user) ) {
									$selected = 'selected';
								}
							?>
							<option value="<?php echo $value['id_user']; ?>" <?php echo $selected; ?> ><?php echo $value['nama_group'].' | '.$value['nama_user']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-sm-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="km.edit(this)" data-kode="<?php echo $data['id']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div> -->
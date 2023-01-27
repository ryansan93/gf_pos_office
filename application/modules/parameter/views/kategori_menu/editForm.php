<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
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
</div>
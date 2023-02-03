<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding">
			<div class="col-lg-8">
				<span style="font-weight: bold;">DETAIL MEMBER</span>
			</div>
			<div class="col-md-4 text-right">
				<button type="button" class="close pull-right" data-dismiss="modal" style="color: #000000;">&times;</button>
			</div>
			<div class="col-md-12 text-left">
				<hr style="margin-top: 5px; margin-bottom: 10px;">
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12 no-padding">
				<div class="col-lg-12 text-left"><label class="control-label">Nama</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control nama" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50" value="<?php echo $data['nama']; ?>" disabled>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">No. Telp</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control no_telp" placeholder="No. Telp (MAX : 15)" data-required="1" maxlength="15" value="<?php echo $data['no_telp']; ?>" disabled>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Alamat</label></div>
		        <div class="col-lg-12">
		            <textarea class="form-control alamat" placeholder="Alamat" data-required="1" disabled><?php echo $data['alamat']; ?></textarea>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Grup Member</label></div>
		        <div class="col-lg-12">
		            <select class="form-control member_group" disabled>
		            	<?php
		            		$selected_null = null;
		            		if ( empty($data['member_group_id']) ) {
		            			$selected_null = 'selected';
		            		}
		            	?>

		            	<option value="" <?php echo $selected_null; ?> >NON GRUP</option>
		            	<?php if ( !empty($member_group) ): ?>
		            		<?php foreach ($member_group as $k_mg => $v_mg): ?>
		            			<?php
				            		$selected = null;
				            		if ( $data['member_group_id'] == $v_mg['id'] ) {
				            			$selected = 'selected';
				            		}
				            	?>
		            			<option value="<?php echo $v_mg['id']; ?>" <?php echo $selected; ?> ><?php echo $v_mg['nama']; ?></option>
		            		<?php endforeach ?>
		            	<?php endif ?>
		            </select>
		        </div>
			</div>
			<div class="col-md-12 no-padding hide" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Privilege</label></div>
		        <div class="col-lg-12">
		            <div class="radio" style="margin-top: 0px;">
						<label><input type="radio" name="optradio" value="1" <?php echo (!empty($data['privilege']) && $data['privilege'] == 1) ? 'checked' : ''; ?> disabled>Ya</label>
					</div>
					<div class="radio" style="margin-bottom: 0px;">
						<label><input type="radio" name="optradio" value="0" <?php echo (empty($data['privilege']) || $data['privilege'] == 0) ? 'checked' : ''; ?> disabled>Tidak</label>
					</div>
		        </div>
			</div>
		</div>
		<div class="col-lg-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding btn_view">
			<div class="col-md-12" style="padding-bottom: 10px;">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<?php if ( $akses['a_delete'] == 1 ): ?>
						<button class="btn btn-danger col-md-12" onclick="mbr.delete(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-trash"> Hapus</i></button>
					<?php endif ?>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<?php if ( $akses['a_edit'] == 1 ): ?>
						<button class="btn btn-primary col-md-12" onclick="mbr.editForm(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-edit"> Edit</i></button>
					<?php endif ?>
				</div>
			</div>
			<div class="col-md-12">
				<?php if ( $tanggal <= $data['tgl_berakhir'] && $data['mstatus'] == 1 ): ?>
					<div class="col-md-12 no-padding" style="padding-bottom: 10px;">
						<?php if ( $akses['a_ack'] == 1 ): ?>
							<button class="btn btn-danger col-md-12" onclick="mbr.nonAktif(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-ban"> Non Aktif</i></button>
						<?php endif ?>
					</div>
				<?php endif ?>
				<?php if ( $tanggal > $data['tgl_berakhir'] || $data['mstatus'] == 0 ): ?>
					<div class="col-md-12 no-padding">
						<?php if ( $akses['a_approve'] == 1 ): ?>
							<button class="btn btn-primary col-md-12" onclick="mbr.aktif(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-check"> Aktif</i></button>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
		</div>
		<div class="col-lg-12 no-padding btn_edit hide">
			<div class="col-md-12">
				<div class="col-md-6 no-padding" style="padding-right: 5px;">
					<button class="btn btn-danger col-md-12" onclick="mbr.batalEdit(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-times"> Batal</i></button>
				</div>
				<div class="col-md-6 no-padding" style="padding-left: 5px;">
					<button class="btn btn-primary col-md-12" onclick="mbr.edit(this)" data-kode="<?php echo $data['kode_member']; ?>"><i class="fa fa-edit"> Simpan Perubahan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>
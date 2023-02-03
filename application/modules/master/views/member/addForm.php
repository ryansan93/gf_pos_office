<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12 no-padding">
			<div class="col-lg-8">
				<span style="font-weight: bold;">TAMBAH MEMBER</span>
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
		            <input type="text" class="form-control nama" placeholder="Nama (MAX : 50)" data-required="1" maxlength="50">
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">No. Telp</label></div>
		        <div class="col-lg-12">
		            <input type="text" class="form-control no_telp" placeholder="No. Telp (MAX : 15)" data-required="1" maxlength="15">
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Alamat</label></div>
		        <div class="col-lg-12">
		            <textarea class="form-control alamat" placeholder="Alamat" data-required="1"></textarea>
		        </div>
			</div>
			<div class="col-md-12 no-padding" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Grup Member</label></div>
		        <div class="col-lg-12">
		            <select class="form-control member_group">
		            	<option value="">NON GRUP</option>
		            	<?php if ( !empty($member_group) ): ?>
		            		<?php foreach ($member_group as $k_mg => $v_mg): ?>
		            			<option value="<?php echo $v_mg['id']; ?>"><?php echo $v_mg['nama']; ?></option>
		            		<?php endforeach ?>
		            	<?php endif ?>
		            </select>
		        </div>
			</div>
			<div class="col-md-12 no-padding hide" style="margin-top: 10px;">
				<div class="col-lg-12 text-left"><label class="control-label">Privilege</label></div>
		        <div class="col-lg-12">
		            <div class="radio" style="margin-top: 0px;">
						<label><input type="radio" name="optradio" value="1" checked>Ya</label>
					</div>
					<div class="radio" style="margin-bottom: 0px;">
						<label><input type="radio" name="optradio" value="0">Tidak</label>
					</div>
		        </div>
			</div>
		</div>
		<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-lg-12 no-padding">
			<div class="col-md-12">
				<div class="col-md-12 no-padding">
					<button class="btn btn-primary col-md-12" onclick="mbr.save(this)"><i class="fa fa-save"> Simpan</i></button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Kategori Menu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Nama</label>
			</div>
			<div class="col-xs-12 no-padding">
				<input type="text" class="col-xs-6 form-control nama uppercase" placeholder="Nama" data-required="1">
			</div>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding">
				<label class="control-label">Print Check List</label>
			</div>
			<div class="col-xs-12">
				<input type="radio" id="ya" name="print_cl" checked>
				<label>Ya</label><br>
				<input type="radio" id="tidak" name="print_cl">
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
							<option value="<?php echo $value['id_user']; ?>"><?php echo $value['nama_group'].' | '.$value['nama_user']; ?></option>
						<?php endforeach ?>
					<?php endif ?>
				</select>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="km.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
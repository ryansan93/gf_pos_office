<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Layout Meja</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-xs-12 no-padding">
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Branch</label></div>
				<div class="col-xs-12 no-padding">
					<select class="form-control branch" data-required="1">
						<?php if ( !empty($branch) ): ?>
							<?php foreach ($branch as $key => $val): ?>
								<option value="<?php echo $val['kode_branch']; ?>"><?php echo strtoupper($val['nama']); ?></option>
							<?php endforeach ?>
						<?php endif ?>
					</select>
				</div>
			</div>
			<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
				<div class="col-xs-12 no-padding"><label class="control-label">Nama Lantai</label></div>
				<div class="col-xs-12 no-padding">
					<input type="text" class="form-control lantai uppercase" placeholder="Lantai" data-required="1">
				</div>
			</div>
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding"><label class="control-label">Kontrol Meja</label></div>
				<div class="col-xs-12 no-padding" style="padding-left: 15px;">
					<input type="radio" id="1" name="kontrol_meja" value="1">
  					<label for="1">Ya</label><br>
  					<input type="radio" id="0" name="kontrol_meja" value="0" checked>
  					<label for="0">Tidak</label><br>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
			<div class="col-xs-12 no-padding">
				<table class="table table-bordered tbl_meja" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th class="col-xs-8">Nama Meja</th>
							<th class="col-xs-4"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="text" class="form-control meja uppercase" placeholder="Meja" data-required="1"></td>
							<td>
								<div class="col-xs-12 no-padding">
									<div class="col-xs-6 no-padding" style="padding-right: 3px;">
										<button type="button" class="col-xs-12 btn btn-primary" onclick="meja.addRow(this)"><i class="fa fa-plus"></i></button>
									</div>
									<div class="col-xs-6 no-padding" style="padding-left: 3px;">
										<button type="button" class="col-xs-12 btn btn-danger" onclick="meja.removeRow(this)"><i class="fa fa-times"></i></button>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12 no-padding">
			<button type="button" class="btn btn-primary pull-right" onclick="meja.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
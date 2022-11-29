<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Diskon</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama Diskon</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-8 form-control nama uppercase" placeholder="Nama Diskon" data-required="1" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Deskripsi</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control deskripsi uppercase" data-required="1" placeholder="Deskripsi" data-required="1"><?php echo $data['deskripsi']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Tgl Mulai</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-3 input-group date datetimepicker" name="startDate" id="StartDate">
						        <input type="text" class="form-control text-center" placeholder="Start Date" data-required="1" data-tgl="<?php echo $data['start_date']; ?>" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Tgl Berakhir</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-3 input-group date datetimepicker" name="endDate" id="EndDate">
						        <input type="text" class="form-control text-center" placeholder="End Date" data-required="1" data-tgl="<?php echo $data['end_date']; ?>" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Level</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-2 form-control level" data-required="1">
								<option value="">Pilih Level</option>
								<option value="1" <?php echo ($data['level'] == 1) ? 'selected' : ''; ?> >1</option>
								<option value="2" <?php echo ($data['level'] == 2) ? 'selected' : ''; ?> >2</option>
								<option value="3" <?php echo ($data['level'] == 3) ? 'selected' : ''; ?> >3</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Diskon Persen</label>
						</td>
						<td class="col-sm-10">
							<div class="col-sm-2 no-padding">
								<input type="text" class="col-sm-12 text-right form-control persen" placeholder="Persen" maxlength="6" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['persen']); ?>">
							</div>
							<div class="col-sm-1 text-center no-padding">
								%
							</div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Diskon Nilai</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control text-right nilai" placeholder="Nilai" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['nilai']); ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Non Member</label>
						</td>
						<td class="col-sm-10 text-left">
							<input type="checkbox" class="non_member col-sm-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['non_member'] == 1) ? 'checked' : ''; ?>>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Member</label>
						</td>
						<td class="col-sm-10 text-left">
							<input type="checkbox" class="member col-sm-1 cursor-p" style="height: 20px; margin: 0px; width: 3%;" <?php echo ($data['detail'][0]['member'] == 1) ? 'checked' : ''; ?>>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Minimal Beli</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control text-right min_beli" placeholder="Minimal Beli" maxlength="10" data-tipe="decimal" value="<?php echo angkaDecimal($data['detail'][0]['min_beli']); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="diskon.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>
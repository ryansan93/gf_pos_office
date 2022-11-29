<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Jenis Kartu</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-6 form-control nama uppercase" placeholder="Nama Kartu" data-required="1">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Status</label>
						</td>
						<td class="col-sm-10">
							<select class="col-sm-2 form-control status" data-required="1">
								<option value="">-- Pilih Status --</option>
								<option value="1">Aktif</option>
								<option value="0">Non Aktif</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="jk.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
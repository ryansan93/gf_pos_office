<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Branch</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Kode Branch</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control kode uppercase" placeholder="Kode Branch" data-required="1" maxlength="4">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama Branch</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-8 form-control nama uppercase" placeholder="Nama Branch" data-required="1">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">Alamat</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control alamat uppercase" data-required="1" placeholder="Alamat" data-required="1"></textarea>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">No. Telp</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-4 form-control no_telp uppercase" placeholder="No. Telp" data-required="1" maxlength="15" data-tipe="angka">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">PIN</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-2 form-control pin uppercase" placeholder="PIN" data-required="1" maxlength="4" data-tipe="angka">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="branch.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
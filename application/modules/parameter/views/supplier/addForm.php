<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add Supplier</span>
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
							<input type="text" class="col-sm-6 form-control nama uppercase" placeholder="Nama" data-required="1" maxlength="100">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Alamat</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control alamat" data-required="1" placeholder="Alamat"></textarea>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">NPWP</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-4 form-control npwp uppercase" placeholder="NPWP" data-required="1" maxlength="20">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Penanggung Jawab</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-6 form-control penanggung_jawab uppercase" placeholder="Penanggung Jawab" data-required="1" maxlength="100">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="supplier.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
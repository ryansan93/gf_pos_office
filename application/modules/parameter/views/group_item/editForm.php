<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Edit Group Item</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Kode</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-3 form-control kode uppercase" placeholder="Kode (MAX:10)" data-required="1" value="<?php echo $data['kode']; ?>" maxlength="10" readonly>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Nama</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-6 form-control nama uppercase" placeholder="Nama" data-required="1" value="<?php echo $data['nama']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">COA SAP</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-6 form-control coa uppercase" placeholder="COA (MAX:50)" data-required="1" maxlength="50" value="<?php echo $data['coa']; ?>">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">KET COA SAP</label>
						</td>
						<td class="col-sm-10">
							<textarea class="form-control ket_coa" data-required="1" placeholder="KETERANGAN COA (MAX:250)" maxlength="250"><?php echo trim($data['ket_coa']); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="gi.edit(this)" data-kode="<?php echo $data['kode']; ?>">
				<i class="fa fa-edit"></i>
				Edit
			</button>
		</div>
	</div>
</div>
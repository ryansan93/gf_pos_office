<div class="modal-header header" style="padding-left: 8px; padding-right: 8px;">
	<span class="modal-title">Add PPN</span>
	<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body body">
	<div class="row">
		<div class="col-sm-12 no-padding">
			<table class="table no-border" style="margin-bottom: 0px;">
				<tbody>
					<tr>
						<td class="col-sm-3">				
							<label class="control-label">Branch</label>
						</td>
						<td class="col-sm-9">
							<select class="form-control branch" data-required="1">
								<option value="">Pilih Branch</option>
								<?php if ( !empty($branch) ): ?>
									<?php foreach ($branch as $k_branch => $v_branch): ?>
										<option value="<?php echo $v_branch['kode_branch']; ?>"><?php echo $v_branch['kode_branch'].' | '.strtoupper($v_branch['nama']); ?></option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="col-sm-3">				
							<label class="control-label">Tgl Berlaku</label>
						</td>
						<td class="col-sm-9">
							<div class="col-sm-12 input-group date datetimepicker" name="tglBerlaku" id="TglBerlaku">
						        <input type="text" class="form-control text-center" placeholder="Berlaku" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-3">				
							<label class="control-label">Nilai (%)</label>
						</td>
						<td class="col-sm-9">
							<input type="text" class="col-sm-4 form-control nilai text-right" placeholder="Nilai" data-required="1" data-tipe="decimal" maxlength="6">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="ppn.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
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
							<label class="control-label">Shift</label>
						</td>
						<td class="col-sm-10">
							<input type="text" class="col-sm-4 form-control shift uppercase" placeholder="Nama Shift" data-required="1">
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">				
							<label class="control-label">Start</label>
						</td>
						<td class="col-sm-10">
							<div class="col-xs-3 input-group date datetimepicker" name="startTime" id="StartTime">
						        <input type="text" class="form-control text-center" placeholder="Start Time" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
					<tr>
						<td class="col-sm-2">
							<label class="control-label">End</label>
						</td>
						<td class="col-sm-10">
							<div class="col-xs-3 input-group date datetimepicker" name="endTime" id="EndTime">
						        <input type="text" class="form-control text-center" placeholder="End Time" data-required="1" />
						        <span class="input-group-addon">
						            <span class="glyphicon glyphicon-calendar"></span>
						        </span>
						    </div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-12 no-padding" style="padding-left: 8px; padding-right: 8px;">
			<hr>
			<button type="button" class="btn btn-primary pull-right" onclick="shift.save()">
				<i class="fa fa-save"></i>
				Save
			</button>
		</div>
	</div>
</div>
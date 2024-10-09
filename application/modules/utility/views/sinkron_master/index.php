<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-12 no-padding" style="margin-bottom: 10px;">
			<select class="form-control branch">
				<option value="">-- Pilih Branch --</option>
				<?php foreach ($branch as $key => $value) { ?>
					<option value="<?php echo $value['kode_branch']; ?>"><?php echo $value['nama']; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-xs-12 no-padding" style="margin-bottom: 10px;">
			<div class="col-xs-12 no-padding"><label class="control-label">LIST MENU YANG AKAN DI SINKRON</label></div>
			<div class="col-xs-12 no-padding">
				<?php foreach ($menu as $key => $value) { ?>
					<!-- <input type="radio" id="<?php echo $key; ?>" name="age" value="<?php echo $value['table']; ?>">
					<label for="<?php echo $key; ?>"><?php echo $value['keterangan']; ?></label><br> -->
					<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
						<div class="col-xs-1 no-padding" style="max-width: 5%;">
							<input type="checkbox" class="cursor-p" style="height: 20px; margin: 0px; width: 100%;" data-val="<?php echo $value['table']; ?>">
						</div>
						<div class="col-xs-11 no-padding"><label class="control-label"><?php echo $value['keterangan']; ?></label></div>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-12 no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="col-lg-12 btn btn-primary cursor-p" title="ADD" onclick="sm.sinkronList()"> 
					<i class="fa fa-upload" aria-hidden="true"></i> Proses Sinkron
				</button>
			<?php } ?>
		</div>
		<div class="col-lg-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12 no-padding">
			<small>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="col-lg-4">User</th>
							<th class="col-lg-8">Keterangan</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2">Data tidak di temukan.</td>
						</tr>
					</tbody>
				</table>
			</small>
		</div>
	</div>
</div>
<?php if ( $akses['a_submit'] == 1 ): ?>
	<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
		<button class="col-xs-12 btn btn-success" data-href="action" onclick="mg.changeTabActive(this)"><i class="fa fa-plus"></i> Tambah</button>
	</div>	
<?php endif ?>
<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Tanggal</label>
	</div>
	<div class="col-xs-12 no-padding">
		<div class="input-group date datetimepicker" name="tglInput" id="tglInput">
	        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
	        <span class="input-group-addon">
	            <span class="glyphicon glyphicon-calendar"></span>
	        </span>
	    </div>
	</div>
</div>
<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<div class="col-xs-12 no-padding">
		<label class="control-label">Branch</label>
	</div>
	<div class="col-xs-12 no-padding">
		<!-- <select class="form-control branch" data-required="1"> -->
		<select class="branch" name="branch[]" multiple="multiple" width="100%" data-required="1">
			<option value="">-- Pilih Branch --</option>
			<?php if ( !empty($branch) ): ?>
				<?php foreach ($branch as $key => $value): ?>
					<option value="<?php echo $value['kode_branch']; ?>"><?php echo $value['nama']; ?></option>
				<?php endforeach ?>
			<?php endif ?>
		</select>
	</div>
</div>
<div class="col-xs-12 no-padding">
	<button class="btn btn-primary col-xs-12" onclick="mg.getLists()"><i class="fa fa-search"></i> Tampilkan</button>
</div>
<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
<div class="col-xs-12 no-padding" style="padding-bottom: 10px;">
	<small>
		<table class="table table-bordered" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th>Tanggal</th>
					<th>Branch</th>
					<th>Jumlah</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="3">Data tidak ditemukan.</td>
				</tr>
			</tbody>
		</table>
	</small>
</div>
<div class="col-xs-12 no-padding">
	<button type="button" class="btn btn-default pull-right" onclick="mg.exportPdf()"><label class="control-lable" style="margin-bottom: 0px;"><i class="fa fa-file-pdf-o"></i> Export PDF</label></button>
</div>
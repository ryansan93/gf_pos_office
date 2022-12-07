<div class="row content-panel">
	<div class="col-lg-12 detailed">
		<form role="form" class="form-horizontal">
			<div class="col-xs-12 no-padding">
				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Branch</label>
					</div>
					<div class="col-xs-12 no-padding">
						<select class="form-control branch" data-required="1">
							<option value="">-- Pilih Branch --</option>
							<?php foreach ($branch as $k_db => $v_db): ?>
								<option data-tokens="<?php echo $v_db['nama']; ?>" value="<?php echo $v_db['kode_branch']; ?>"><?php echo strtoupper($v_db['kode_branch'].' | '.$v_db['nama']); ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Tgl Awal</label>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date datetimepicker" name="startDate" id="StartDate">
					        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<div class="col-xs-12 no-padding">
						<label class="control-label">Tgl Akhir</label>
					</div>
					<div class="col-xs-12 no-padding">
						<div class="input-group date datetimepicker" name="endDate" id="EndDate">
					        <input type="text" class="form-control text-center" placeholder="Tanggal" data-required="1" />
					        <span class="input-group-addon">
					            <span class="glyphicon glyphicon-calendar"></span>
					        </span>
					    </div>
					</div>
				</div>

				<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
					<button type="button" class="col-xs-12 btn btn-primary pull-right tampilkan_riwayat" onclick="jual.getLists(this)"><i class="fa fa-search"></i> Tampilkan</button>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr></div>
			<div class="col-xs-12 no-padding">
				<div class="panel-heading no-padding">
					<ul class="nav nav-tabs nav-justified">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#penjualan_produk" data-tab="penjualan_produk">PENJUALAN PRODUK</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#penjualan_harian" data-tab="penjualan_harian">PENJUALAN HARIAN</a>
						</li>
					</ul>
				</div>
				<div class="panel-body no-padding">
					<div class="tab-content">
						<div id="penjualan_produk" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">
							<?php echo $report_harian_produk; ?>
						</div>

						<div id="penjualan_harian" class="tab-pane fade" role="tabpanel" style="padding-top: 10px;">
							<?php echo $report_harian; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 no-padding"><hr></div>
			<div class="col-xs-12 no-padding">
				<small>
					<table class="table table-bordered tbl_detail_pembayaran">
						<thead>
							<tr>
								<th class="col-xs-1">Tanggal</th>
								<th class="col-xs-7">Jenis Pembayaran</th>
								<th class="col-xs-4">Nilai</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="2">Data tidak ditemukan.</td>
							</tr>
						</tbody>
					</table>
				</small>
			</div>
		</form>
	</div>
</div>
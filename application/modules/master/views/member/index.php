<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<?php if ( $akses['a_submit'] == 1 ) { ?>
			<div class="col-lg-12 no-padding">
				<button type="button" data-href="action" class="col-lg-12 btn btn-default cursor-p pull-right" onclick="mbr.importForm(this)"> 
					<i class="fa fa-upload" aria-hidden="true"></i> Import
				</button>
			</div>
			<div class="col-lg-12 no-padding">
				<hr style="margin-top: 10px; margin-bottom: 10px;">
			</div>
			<div class="col-lg-12 no-padding">
				<button type="button" data-href="action" class="col-lg-12 btn btn-primary cursor-p pull-right" title="ADD" onclick="mbr.addForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			</div>
		<?php } ?>
		<div class="col-lg-12 no-padding">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_riwayat" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<small>
			<table class="table table-bordered tbl_riwayat" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-1">Kode</th>
						<th class="col-sm-2">Grup</th>
						<th class="col-sm-2">Nama</th>
						<th class="col-sm-2">No. Telp</th>
						<th class="col-sm-1">Tgl Berakhir</th>
						<th class="col-sm-1">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $key => $value): ?>
							<?php
								$status = 1;
								if ( $value['tanggal'] > $value['tgl_berakhir'] || $value['mstatus'] == 0 ) {
									$status = 0;
								}
							?>

							<tr class="search cursor-p" onclick="mbr.viewForm(this)" data-kode="<?php echo $value['kode_member']; ?>">
								<td><?php echo $value['kode_member']; ?></td>
								<td><?php echo !empty($value['nama_grup']) ? strtoupper($value['nama_grup']) : 'NON GRUP'; ?></td>
								<td><?php echo $value['nama']; ?></td>
								<td><?php echo !empty($value['no_telp']) ? $value['no_telp'] : '-'; ?></td>
								<td><?php echo strtoupper(tglIndonesia($value['tgl_berakhir'], '-', ' ')); ?></td>
								<td class="text-center" style="color: <?php echo ($status == 0) ? 'RED' : '#000000'; ?>;">
									<b>
										<?php if ( $status == 0 ) : ?>
											NON AKTIF
										<?php else: ?>
											AKTIF
										<?php endif ?>
									</b>
								</td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="7">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>
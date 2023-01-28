<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_printer" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="printer.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_printer" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-2 text-center">Branch</th>
						<th class="col-sm-2 text-center">Sharing Name</th>
						<th class="col-sm-2 text-center">Lokasi</th>
						<th class="col-sm-1 text-center">Kategori Menu</th>
						<th class="col-sm-1 text-center">Status</th>
						<th class="col-sm-1 text-center">Station</th>
						<th class="col-sm-1 text-center">Jumlah Print</th>
						<th class="col-sm-2 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="data">
								<td class="text-left"><?php echo $v_data['branch']; ?></td>
								<td class="text-left"><?php echo $v_data['sharing_name']; ?></td>
								<td class="text-left"><?php echo $v_data['lokasi']; ?></td>
								<td class="text-left">
									<?php 
										if ( !empty($v_data['kategori_menu']) ) {
											foreach ($v_data['kategori_menu'] as $k_km => $v_km) {
												echo $v_km['nama'];
												if ( isset($v_data['kategori_menu'][ $k_km+1 ]) ) {
													echo '<br>';
												}
											}
										} else {
											echo '-';
										}
									?>
								</td>
								<td class="text-center"><?php echo ($v_data['status'] == 1) ? 'AKTIF' : 'NON AKTIF'; ?></td>
								<td class="text-left"><?php echo $v_data['nama_station']; ?></td>
								<td class="text-center"><?php echo $v_data['jml_print']; ?></td>
								<td>
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="btn btn-primary" onclick="printer.modalEditForm(this);" data-id="<?php echo $v_data['id']; ?>"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div>
									<div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<button class="btn btn-danger" onclick="printer.delete(this);" data-id="<?php echo $v_data['id']; ?>"><i class="fa fa-trash"></i></button>
										<?php } ?>
									</div>
								</td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="6">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>
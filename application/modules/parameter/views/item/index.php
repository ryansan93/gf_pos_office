<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_item" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="item.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_item" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-xs-1 text-center">Kode</th>
						<th class="col-xs-2 text-center">Nama</th>
						<th class="col-xs-1 text-center">Brand</th>
						<th class="col-xs-1 text-center">Satuan</th>
						<th class="col-xs-1 text-center">Group</th>
						<th class="col-xs-4 text-center">Keterangan</th>
						<th class="col-xs-2 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="search head" data-kode="<?php echo $v_data['kode']; ?>">
								<td><?php echo $v_data['kode']; ?></td>
								<td><?php echo $v_data['nama']; ?></td>
								<td><?php echo $v_data['brand']; ?></td>
								<td>
									<?php 
										$idx = 0;
										foreach ($v_data['satuan'] as $k_satuan => $v_satuan) {
											$ket = $v_satuan['satuan'].' ( '.angkaRibuan($v_satuan['pengali']).' )';

											$idx++;
											if ( count($v_data['satuan']) > $idx ) {
												$ket .= '<br>';
											}

											echo $ket;
										} 
									?>
								</td>
								<td><?php echo $v_data['group']['nama']; ?></td>
								<td><?php echo $v_data['keterangan']; ?></td>
								<td>
									<div class="col-xs-6 no-padding" style="padding-right: 5px;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<button class="col-xs-12 btn btn-danger" onclick="item.delete(this);"><i class="fa fa-trash"></i></button>
										<?php } ?>
									</div>
									<div class="col-xs-6 no-padding" style="padding-left: 5px;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="col-xs-12 btn btn-primary" onclick="item.modalEditForm(this);"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr class="detail hide"></tr>
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
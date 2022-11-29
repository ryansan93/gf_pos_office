<div class="row content-panel detailed">
	<div class="col-lg-12 detailed">
		<div class="col-lg-8 search left-inner-addon no-padding">
			<i class="glyphicon glyphicon-search"></i><input class="form-control" type="search" data-table="tbl_diskon" placeholder="Search" onkeyup="filter_all(this)">
		</div>
		<div class="col-lg-4 action no-padding">
			<?php if ( $akses['a_submit'] == 1 ) { ?>
				<button id="btn-add" type="button" data-href="action" class="btn btn-primary cursor-p pull-right" title="ADD" onclick="hm.modalAddForm(this)"> 
					<i class="fa fa-plus" aria-hidden="true"></i> ADD
				</button>
			<?php } else { ?>
				<div class="col-lg-2 action no-padding pull-right">
					&nbsp
				</div>
			<?php } ?>
		</div>
		<small>
			<table class="table table-bordered table-hover tbl_diskon" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th class="col-sm-3 text-center">Menu</th>
						<th class="col-sm-2 text-center">Jenis Pesanan</th>
						<th class="col-sm-2 text-center">Tgl Berlaku</th>
						<th class="col-sm-2 text-center">Harga</th>
						<th class="col-sm-1 text-center">Action</th>
					</tr>
				</thead>
				<tbody class="list">
					<?php if ( !empty($data) ): ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="head">
								<td class="menu" data-val="<?php echo $v_data['menu_kode']; ?>"><?php echo strtoupper($v_data['menu']['nama']); ?></td>
								<td class="jenis_pesanan" data-val="<?php echo $v_data['jenis_pesanan_kode']; ?>"><?php echo strtoupper($v_data['jenis_pesanan']['nama']); ?></td>
								<td class="text-center tgl_mulai" data-val="<?php echo $v_data['tgl_mulai']; ?>"><?php echo tglIndonesia($v_data['tgl_mulai'], '-', ' '); ?></td>
								<td class="text-right harga" data-val="<?php echo $v_data['harga']; ?>"><?php echo angkaDecimal($v_data['harga']); ?></td>
								<td>
									<!-- <div class="col-sm-6 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_edit'] == 1 ) { ?>
											<button class="btn btn-primary" onclick="hm.modalEditForm(this);"><i class="fa fa-edit"></i></button>
										<?php } ?>
									</div> -->
									<div class="col-sm-12 no-padding" style="display: flex; justify-content: center; align-items: center;">
										<?php if ( $akses['a_delete'] == 1 ) { ?>
											<?php if ( $v_data['tgl_mulai'] > date('Y-m-d') ): ?>
												<button class="btn btn-danger" onclick="hm.delete(this);"><i class="fa fa-trash"></i></button>
											<?php endif ?>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr class="detail hide"></tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="5">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</small>
	</div>
</div>
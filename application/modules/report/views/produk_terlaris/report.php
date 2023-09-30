<div class="col-xs-12 no-padding">
	<div class="col-md-12 search no-padding" style="margin-bottom: 10px;">
		<!-- <button type="button" class="btn btn-default pull-right" onclick="pt.export_excel()"><i class="fa fa-print"></i> Export Excel</button> -->
		<button type="button" class="btn btn-default pull-right" onclick="pt.exportExcel(this)"><i class="fa fa-print"></i> Export Excel</button>
	</div>
	<small>
		<?php if ( !isset($filter) || $filter == 0 ): ?>
			<table class="table table-bordered tbl_report" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-1">No.</th>
						<th class="col-xs-2">Kategori</th>
						<th class="col-xs-2">Jenis</th>
						<th class="col-xs-4">Menu</th>
						<th class="col-xs-1">Qty</th>
						<th class="col-xs-2">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) && count($data) > 0 ): ?>
						<?php $no = 1; ?>
						<?php foreach ($data as $k_data => $v_data): ?>
							<tr class="search">
								<td><?php echo angkaRibuan($no); ?></td>
								<td><?php echo !empty($v_data['kategori']) ? $v_data['kategori'] : '-'; ?></td>
								<td><?php echo $v_data['jenis']; ?></td>
								<td><?php echo $v_data['menu_nama']; ?></td>
								<td class="text-right"><?php echo angkaRibuan($v_data['qty']); ?></td>
								<td class="text-right"><?php echo angkaDecimal($v_data['total']); ?></td>
							</tr>
							<?php $no++; ?>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="6">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		<?php else: ?>
			<table class="table table-bordered tbl_report" style="margin-bottom: 0px;">
				<thead>
					<tr>
						<th class="col-xs-1">No.</th>
						<th class="col-xs-2">Nama Member</th>
						<th class="col-xs-1">Jml Transaksi</th>
						<th class="col-xs-1">Kategori</th>
						<th class="col-xs-2">Jenis</th>
						<th class="col-xs-3">Menu</th>
						<th class="col-xs-1">Qty</th>
						<th class="col-xs-1">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data) && count($data) > 0 ): ?>
						<?php $no = 1; ?>
						<?php foreach ($data as $k_member => $v_member): ?>
							<?php $idx_member = 0; $t_jumlah = 0; $t_total = 0;?>
							<?php foreach ($v_member['detail_menu'] as $k_data => $v_data): ?>
								<tr class="search">
									<?php if ( $idx_member == 0 ): ?>
										<td rowspan="<?php echo count($v_member['detail_menu'])+1; ?>"><?php echo angkaRibuan($no); ?></td>
										<td rowspan="<?php echo count($v_member['detail_menu']); ?>"><?php echo $v_member['nama']; ?></td>
										<td rowspan="<?php echo count($v_member['detail_menu']); ?>" class="text-right"><?php echo $v_member['jml_transaksi']; ?></td>
									<?php endif ?>
									<td><?php echo !empty($v_data['kategori']) ? $v_data['kategori'] : '-'; ?></td>
									<td><?php echo $v_data['jenis']; ?></td>
									<td><?php echo $v_data['menu_nama']; ?></td>
									<td class="text-right"><?php echo angkaRibuan($v_data['qty']); ?></td>
									<td class="text-right"><?php echo angkaDecimal($v_data['total']); ?></td>
								</tr>
								<?php
									$t_jumlah += $v_data['qty']; 
									$t_total += $v_data['total'];
									$idx_member++;
								?>
							<?php endforeach ?>
							<tr>
								<td colspan="5" class="text-right"><b>TOTAL</b></td>
								<td class="text-right"><b><?php echo angkaRibuan($t_jumlah); ?></b></td>
								<td class="text-right"><b><?php echo angkaDecimal($t_total); ?></b></td>
							</tr>
							<?php $no++; ?>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="6">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		<?php endif ?>
	</small>
</div>
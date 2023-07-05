<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-right:5px;">
	<div class="col-xs-4 no-padding">
		<label class="control-label">Gudang</label>
	</div>
	<div class="col-xs-8 no-padding">
		<label class="control-label">: <?php echo strtoupper( $data['gudang']['nama'] ); ?></label>
	</div>
</div>

<div class="col-xs-6 no-padding" style="margin-bottom: 5px; padding-left:5px;">
	<div class="col-xs-4 no-padding">
		<label class="control-label">Tgl Stok Opname</label>
	</div>
	<div class="col-xs-8 no-padding">
		<label class="control-label">: <?php echo strtoupper( tglIndonesia( $data['tanggal'], '-', ' ', true ) ); ?></label>
	</div>
</div>

<div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>

<div class="col-xs-12 search left-inner-addon pull-right no-padding" style="padding-bottom: 10px;">
	<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_item" placeholder="Search" onkeyup="filter_all(this)">
</div>

<div class="col-xs-12 no-padding" style="margin-bottom: 5px;">
	<small>
		<table class="table table-bordered tbl_item" style="margin-bottom: 0px;">
			<thead>
				<tr>
					<th class="col-xs-1">Kode</th>
					<th class="col-xs-1">Group Item</th>
					<th class="col-xs-2">Item</th>
					<th class="col-xs-1">Satuan</th>
					<th class="col-xs-1">Jumlah</th>
					<th class="col-xs-1">Harga Satuan (Rp.)</th>
					<th class="col-xs-1">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data['detail'] as $k_det => $v_det): ?>
					<tr class="search v-center data">
						<td><?php echo strtoupper($v_det['item_kode']); ?></td>
						<td><?php echo strtoupper($v_det['item']['nama']); ?></td>
						<td class="text-center"><?php echo $v_det['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['harga']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_det['jumlah'] * $v_det['harga']); ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</small>
</div>
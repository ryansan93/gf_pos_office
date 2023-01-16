<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_gudang => $v_gudang): ?>
		<tbody>
			<tr>
				<td colspan="8" style="background-color: #ededed;"><b><?php echo $v_gudang['nama']; ?></b></td>
			</tr>
		</tbody>
		<?php foreach ($v_gudang['detail'] as $k_item => $v_item): ?>
			<?php $idx_tgl = 0; ?>
			<tbody class="row-wrapper">
				<?php foreach ($v_item['detail_tanggal'] as $k_tgl => $v_tgl): ?>
					<tr>
						<?php if ( $idx_tgl == 0 ): ?>
							<td rowspan="<?php echo count($v_item['detail_tanggal']); ?>"><?php echo $v_item['kode']; ?></td>
							<td rowspan="<?php echo count($v_item['detail_tanggal']); ?>"><?php echo $v_item['nama']; ?></td>
						<?php endif ?>
						<td class="text-center"><?php echo tglIndonesia($v_tgl['tanggal'], '-', ' '); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['jumlah']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['harga']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['nilai_stok']); ?></td>
					</tr>
					<?php $idx_tgl++; ?>
				<?php endforeach ?>
			</tbody>
		<?php endforeach ?>
	<?php endforeach ?>
<?php else: ?>
	<tbody>
		<tr>
			<td colspan="11">Data tidak ditemukan.</td>
		</tr>
	</tbody>
<?php endif ?>
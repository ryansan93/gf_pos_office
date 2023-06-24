<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $grand_total_jumlah = 0; $grand_total_nilai = 0; ?>
	<?php foreach ($data as $k_gudang => $v_gudang): ?>
		<tr>
			<td colspan="8" style="background-color: #ededed;"><b><?php echo $v_gudang['nama']; ?></b></td>
		</tr>
		<?php foreach ($v_gudang['group_item'] as $k_gi => $v_gi): ?>
			<?php $idx_gi = 0; $total_jumlah_gi = 0; $total_nilai = 0; ?>
			<?php
				$rowspan_gi = 0;
				foreach ($v_gi['detail'] as $k_item => $v_item) {
					$rowspan_gi += count($v_item['detail_tanggal']);
				}
			?>
			<?php foreach ($v_gi['detail'] as $k_item => $v_item): ?>
				<?php $idx_tgl = 0; ?>
				<?php foreach ($v_item['detail_tanggal'] as $k_tgl => $v_tgl): ?>
					<tr>
						<?php if ( $idx_gi == 0 ): ?>
							<td rowspan="<?php echo $rowspan_gi; ?>"><?php echo $v_gi['nama']; ?></td>
						<?php endif ?>
						<?php if ( $idx_tgl == 0 ): ?>
							<td rowspan="<?php echo count($v_item['detail_tanggal']); ?>"><?php echo $v_item['kode']; ?></td>
							<td rowspan="<?php echo count($v_item['detail_tanggal']); ?>"><?php echo $v_item['nama']; ?></td>
						<?php endif ?>
						<td class="text-center"><?php echo tglIndonesia($v_tgl['tanggal'], '-', ' '); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['jumlah']); ?></td>
						<td class="text-center"><?php echo $v_item['satuan']; ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['harga']); ?></td>
						<td class="text-right"><?php echo angkaDecimal($v_tgl['nilai_stok']); ?></td>
					</tr>
					<?php
						$total_jumlah_gi += $v_tgl['jumlah'];
						$total_nilai += $v_tgl['nilai_stok'];
						$grand_total_jumlah += $v_tgl['jumlah'];
						$grand_total_nilai += $v_tgl['nilai_stok'];
					?>

					<?php $idx_gi++; ?>
					<?php $idx_tgl++; ?>
				<?php endforeach ?>
			<?php endforeach ?>
			<tr>
				<td colspan="4" class="text-right"><b>TOTAL</b></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_jumlah_gi); ?></b></td>
				<td colspan="2" class="text-right"></td>
				<td class="text-right"><b><?php echo angkaDecimal($total_nilai); ?></b></td>
			</tr>
		<?php endforeach ?>
	<?php endforeach ?>
	<tr>
		<td colspan="4" class="text-right"><b>GRAND TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_jumlah); ?></b></td>
		<td colspan="2" class="text-right"></td>
		<td class="text-right"><b><?php echo angkaDecimal($grand_total_nilai); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
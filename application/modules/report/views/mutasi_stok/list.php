<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_branch => $v_branch): ?>
		<tbody>
			<tr>
				<td colspan="11" style="background-color: #ededed;"><b><?php echo $v_branch['nama']; ?></b></td>
			</tr>
		</tbody>
		<?php $urut_item = 0; ?>
		<?php foreach ($v_branch['detail'] as $k_item => $v_item): ?>
			<?php $saldo = 0; ?>
			<?php $nilai_saldo = 0; ?>
			<?php $idx_item = 0; ?>
			<?php 
				$rowspan_item = 0;
				foreach ($v_item['detail'] as $k_tgl => $v_tgl) {
					$rowspan_item += count($v_tgl['masuk']) + count($v_tgl['keluar']);
				} 
			?>
			<tbody class="row-wrapper">
				<?php foreach ($v_item['detail'] as $k_tgl => $v_tgl): ?>
					<?php $idx_tgl = 0; ?>
					<?php $rowspan_tanggal = count($v_tgl['masuk']) + count($v_tgl['keluar']); ?>
					<?php foreach ($v_tgl['masuk'] as $k_masuk => $v_masuk): ?>
						<?php $saldo += $v_masuk['masuk']; ?>
						<?php $nilai_saldo += ($v_masuk['masuk'] * $v_masuk['harga']); ?>
						<tr class="data">
							<?php if ( $idx_item == 0 ): ?>
								<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['kode']; ?></td>
								<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['nama']; ?></td>

								<?php $idx_item++; ?>
							<?php endif ?>
							<?php if ( $idx_tgl == 0 ): ?>
								<td class="text-center" rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo strtoupper(tglIndonesia($v_masuk['tgl_trans'], '-', ' ')); ?></td>
								<?php $idx_tgl++; ?>
							<?php endif ?>
							<td><?php echo $v_masuk['kode']; ?></td>
							<td>Masuk</td>
							<td class="text-right"><?php echo angkaDecimal($v_masuk['masuk']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($v_masuk['harga']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($v_masuk['nilai']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($saldo); ?></td>
							<td class="text-right"><?php echo angkaDecimal($nilai_saldo); ?></td>
						</tr>
					<?php endforeach ?>
					<?php foreach ($v_tgl['keluar'] as $k_keluar => $v_keluar): ?>
						<?php $saldo -= $v_keluar['keluar']; ?>
						<?php $nilai_saldo -= ($v_keluar['keluar'] * $v_keluar['harga']); ?>
						<tr class="data">
							<?php if ( $idx_item == 0 ): ?>
								<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['kode']; ?></td>
								<td rowspan="<?php echo $rowspan_item; ?>"><?php echo $v_item['nama']; ?></td>

								<?php $idx_item++; ?>
							<?php endif ?>
							<?php if ( $idx_tgl == 0 ): ?>
								<td class="text-center" rowspan="<?php echo $rowspan_tanggal; ?>"><?php echo strtoupper(tglIndonesia($v_keluar['tgl_trans'], '-', ' ')); ?></td>
								<?php $idx_tgl++; ?>
							<?php endif ?>
							<td><?php echo $v_keluar['kode']; ?></td>
							<td>Keluar</td>
							<td class="text-right"><?php echo angkaDecimal($v_keluar['keluar']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($v_keluar['harga']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($v_keluar['nilai']); ?></td>
							<td class="text-right"><?php echo angkaDecimal($saldo); ?></td>
							<td class="text-right"><?php echo angkaDecimal($nilai_saldo); ?></td>
						</tr>
					<?php endforeach ?>
				<?php endforeach ?>
			</tbody>
		<?php endforeach ?>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="11">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
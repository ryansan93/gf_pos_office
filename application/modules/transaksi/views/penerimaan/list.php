<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p" onclick="terima.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode_terima']; ?>" data-edit="">
			<td class="text-center"><?php echo !empty($v_data['po_no']) ? $v_data['po_no'] : '-'; ?></td>
			<td class="text-center"><?php echo strtoupper(tglIndonesia($v_data['tgl_terima'], '-', ' ')); ?></td>
			<td class="text-center"><?php echo $v_data['kode_terima']; ?></td>
			<td><?php echo strtoupper($v_data['nama_gudang']); ?></td>
			<td><?php echo strtoupper($v_data['supplier']); ?></td>
			<td>
				<?php
					if ( !empty($v_data['list_coa']) ) {
						$idx = 0;
						foreach ($v_data['list_coa'] as $key => $value) {
							if ( $idx == 0 ) {
								echo $value['coa'];
							} else {
								echo '<br>'.$value['coa'];
							}

							$idx++;
						}
					} else {
						echo '-';
					}
				?>
			</td>
			<td>
				<?php
					if ( !empty($v_data['list_coa']) ) {
						$idx = 0;
						foreach ($v_data['list_coa'] as $key => $value) {
							if ( $idx == 0 ) {
								echo $value['ket_coa'];
							} else {
								echo '<br>'.$value['ket_coa'];
							}

							$idx++;
						}
					} else {
						echo '-';
					}
				?>
			</td>
			<td class="text-right">
				<?php echo angkaDecimal($v_data['total']); ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="8">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
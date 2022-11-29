<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="cursor-p" onclick="pp.changeTabActive(this)" data-href="action" data-id="<?php echo $v_data['kode']; ?>" data-edit="">
			<td class="text-center"><?php echo tglIndonesia($v_data['tgl_bayar'], '-', ' '); ?></td>
			<td class="text-center"><?php echo $v_data['kode']; ?></td>
			<td class="text-center"><?php echo strtoupper($v_data['jenis_bayar']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['tot_tagihan']); ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['tot_bayar']); ?></td>
			<td>
				<?php if ( !empty($v_data['lampiran']) ): ?>
					<a href="uploads/<?php echo $v_data['lampiran']; ?>" target="_blank"><?php echo $v_data['lampiran']; ?></a>
				<?php else: ?>
					-
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
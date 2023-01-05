<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php $total_hutang = 0; $total_bayar = 0; ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search">
			<td><?php echo tglIndonesia($v_data['tgl_pesan'], '-', ' '); ?></td>
			<td><?php echo $v_data['faktur_kode']; ?></td>
			<td><?php echo !empty($v_data['member_group']) ? $v_data['member_group'] : '-'; ?></td>
			<td><?php echo $v_data['member']; ?></td>
			<td class="text-right"><?php echo angkaRibuan($v_data['hutang']); ?></td>
			<td class="text-right"><?php echo angkaRibuan($v_data['bayar']); ?></td>
		</tr>
		<?php 
			$total_hutang += $v_data['hutang']; 
			$total_bayar += $v_data['bayar']; 
		?>
	<?php endforeach ?>
	<tr>
		<td class="text-right" colspan="3"><b>TOTAL</b></td>
		<td class="text-right"><b><?php echo angkaRibuan($total_hutang); ?></b></td>
		<td class="text-right"><b><?php echo angkaRibuan($total_bayar); ?></b></td>
	</tr>
<?php else: ?>
	<tr>
		<td colspan="5">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>
<style type="text/css">
	.str { mso-number-format:\@; }
	.decimal_number_format { mso-number-format: "\#\,\#\#0\.00"; }
	/* .decimal_number_format { mso-number-format: "\#\,\#\#0.00"; }
	.decimal_number_format4 { mso-number-format: "\#\,\#\#0.0000"; } */
	.number_format { mso-number-format: "\#\,\#\#0"; }
</style>
<table border="1">
    <thead>
        <tr>
            <th>No. PO</th>
            <th>Tanggal Terima</th>
            <th>Kode Terima</th>
            <th>Gudang</th>
            <th>Supplier</th>
            <th>NPWP</th>
            <th>COA SAP</th>
            <th>Keterangan COA SAP</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( !empty($data) && count($data) > 0 ): ?>
            <?php foreach ($data as $k_data => $v_data): ?>
                <tr>
                    <td><?php echo !empty($v_data['po_no']) ? $v_data['po_no'] : '-'; ?></td>
                    <td><?php echo strtoupper(tglIndonesia($v_data['tgl_terima'], '-', ' ')); ?></td>
                    <td><?php echo $v_data['kode_terima']; ?></td>
                    <td><?php echo strtoupper($v_data['nama_gudang']); ?></td>
                    <td><?php echo strtoupper($v_data['supplier']); ?></td>
                    <td><?php echo strtoupper($v_data['npwp_supplier']); ?></td>
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
                    <td class="decimal_number_format"><?php echo number_format((float) $v_data['total'], 2, ',', ''); ?></td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="9">Data tidak ditemukan.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
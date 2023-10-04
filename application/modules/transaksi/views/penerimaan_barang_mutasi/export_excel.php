<html>
    <head>
        <style type="text/css">
            .str { mso-number-format:\@; }
            .decimal_number_format { mso-number-format: "\#\,\#\#0\.##"; }
            /* .decimal_number_format { mso-number-format: "\#\,\#\#0.#0"; } */
            /* .decimal_number_format { mso-number-format: "#,###.##"; } */
            .number_format { mso-number-format: "\#\,\#\#0"; }
        </style>
    </head>
    <body>
        <div style="width: 100%;">
            <h3>Riwayat Penerimaan Barang Mutasi</h3>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th>Tanggal Mutasi</th>
                    <th>Kode Mutasi</th>
                    <th>Nama PiC</th>
                    <th>Asal</th>
                    <th>Tujuan</th>
                    <th>COA SAP</th>
                    <th>Keterangan COA SAP</th>
                    <th>Status Terima</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( !empty($data) && count($data) > 0 ): ?>
                    <?php foreach ($data as $k_data => $v_data): ?>
                        <tr>
                            <td><?php echo $v_data['tgl_mutasi']; ?></td>
                            <td><?php echo $v_data['kode_mutasi']; ?></td>
                            <td><?php echo strtoupper($v_data['nama_pic']); ?></td>
                            <td><?php echo strtoupper($v_data['nama_gudang_asal']); ?></td>
                            <td><?php echo strtoupper($v_data['nama_gudang_tujuan']); ?></td>
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
                            <td><?php echo ($v_data['g_status'] == getStatus('submit')) ? 'BELUM' : 'SUDAH'; ?></td>
                            <!-- <td class="decimal_number_format"><?php echo $v_data['total']; ?></td> -->
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
    </body>
</html>
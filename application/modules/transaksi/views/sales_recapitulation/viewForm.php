<div class="modal-header no-padding header">
    <span class="modal-title"><label class="label-control">Detail Transaksi</label></span>
    <button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
    <div class="row" style="height: 100%;">
        <div class="col-xs-12" style="padding-top: 10px; height: 100%;">
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">No. Bill</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['kode_faktur']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Waktu</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo str_replace('-', '/', substr($data['tgl_trans'], 0, 16)); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Member</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['member']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Waitress</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['waitress']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <div class="col-xs-2 no-padding"><label class="label-control" style="padding-top: 0px;">Kasir</label></div>
                <div class="col-xs-10 no-padding">
                    <label class="label-control" style="padding-top: 0px;">: <?php echo strtoupper($data['kasir']); ?></label>
                </div>
            </div>
            <div class="col-xs-12 no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
            <div class="col-xs-12 no-padding" style="height: 55%; margin-bottom: 5px;">
                <div class="col-xs-7 no-padding" style="height: 100%; padding-right: 5px; border-right: 1px solid #dedede;">
                    <div class="col-xs-12 no-padding" style="height: 60%; margin-bottom: 5px;">
                        <div class="col-xs-12" style="padding-left: 10px; padding-right: 10px; background-color: #afabff; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                            <label class="label-control" style="margin-bottom: 0px;">LIST BARANG</label>
                        </div>
                        <div class="col-xs-12 list_barang" style="padding-left: 10px; padding-right: 10px; border: 1px solid #dedede; height: 88%; overflow-y: auto;">
                            <?php $idx_jp = 0; ?>
                            <?php foreach ($data['detail'] as $k_jp => $v_jp): ?>
                                <?php if ( $idx_jp > 0 ): ?>
                                    <br>
                                <?php endif ?>
                                <div class="col-xs-12 no-padding">
                                    <b><span><?php echo $v_jp['nama_jenis_pesanan']; ?></span></b>
                                </div>
                                <?php foreach ($v_jp['item'] as $k_det => $v_det): ?>
                                    <div class="col-xs-12 no-padding" style="padding-top: 3px; display: table-cell; vertical-align: middle;">
                                        <div class="col-xs-7 no-padding" style="padding-right: 5px;">
                                            <span><?php echo $v_det['menu_nama'].' @ '.angkaDecimal($v_det['harga']); ?></span>
                                            <?php if ( !empty($v_det['request']) ): ?>
                                                <br>
                                                <span style="padding-left: 15px;"><?php echo '* '.$v_det['request']; ?></span>
                                            <?php endif ?>
                                        </div>
                                        <div class="col-xs-1 no-padding">
                                            <?php echo $v_det['jumlah']; ?>
                                        </div>
                                        <div class="col-xs-3 no-padding text-right">
                                            <?php echo angkaDecimal($v_det['total']); ?>
                                        </div>
                                        <div class="col-xs-1 no-padding" style="padding-left: 5px;">
                                            <button type="button" class="col-xs-12 btn btn-danger" style="padding: 1px;" onclick="sr.deletePesanan(this)" data-kode="<?php echo $v_det['kode_faktur_item']; ?>"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <?php $idx_jp++; ?>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <div class="col-xs-12 no-padding" style="height: 40%; margin-bottom: 5px;">
                        <div class="col-xs-12" style="padding-left: 10px; padding-right: 10px; background-color: #afabff; border-top-left-radius: 5px; border-top-right-radius: 5px;">
                            <label class="label-control" style="margin-bottom: 0px;">JENIS BAYAR & DISKON</label>
                        </div>
                        <div class="col-xs-12 jenis_bayar" style="padding-left: 10px; padding-right: 10px; border: 1px solid #dedede; height: 80%; overflow-y: auto;">
                            <?php if ( !empty($data['jenis_bayar']) ): ?>
                                <?php foreach ($data['jenis_bayar'] as $k_jb => $v_jb): ?>
                                    <div class="col-xs-12 no-padding" style="padding-top: 3px;">
                                        <div class="col-xs-7 no-padding" style="padding-right: 5px;">
                                            <?php echo $v_jb['jenis_bayar']; ?>
                                        </div>
                                        <div class="col-xs-4 no-padding text-right">
                                            <?php echo angkaDecimal($v_jb['nominal']); ?>
                                        </div>
                                        <div class="col-xs-1 no-padding" style="padding-left: 5px;">
                                            <button type="button" class="col-xs-12 btn btn-danger" style="padding: 1px;" data-id="<?php echo $v_jb['id']; ?>" onclick="sr.deletePembayaran(this)"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            <?php else: ?>
                                <div class="col-xs-12 no-padding">Belum ada pembayaran.</div>
                            <?php endif ?>
                            <?php if ( !empty($data['jenis_diskon']) ): ?>
                                <?php if ( $data['jenis_diskon'] ): ?>
                                    <div class="col-xs-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
                                    <?php foreach ($data['jenis_diskon'] as $k_jd => $v_jd): ?>
                                        <div class="col-xs-12 no-padding" style="padding-top: 3px;">
                                            <div class="col-xs-7 no-padding" style="padding-right: 5px;">
                                                <?php echo $v_jd['nama']; ?>
                                            </div>
                                            <div class="col-xs-4 no-padding text-right">
                                                <?php echo angkaDecimal($v_jd['nilai']); ?>
                                            </div>
                                            <div class="col-xs-1 no-padding" style="padding-left: 5px;">
                                                <button type="button" class="col-xs-12 btn btn-danger" style="padding: 1px;" data-id="<?php echo $v_jd['id']; ?>" onclick="sr.deleteDiskon(this)"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            <?php else: ?>
                                <div class="col-xs-12 no-padding">Tidak diskon yang di apply.</div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-5 no-padding" style="padding-left: 5px;">
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Total Belanja</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_belanja']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Diskon</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo '('.angkaDecimal($data['total_diskon']).')'; ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Service Charge</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_sc']) ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">PB1</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_ppn']) ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Total Nota Gabungan</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['grand_total_gabungan']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Total Bayar</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['grand_total']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Jumlah Bayar</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['total_bayar']); ?></label></div>
                    </div>
                    <div class="col-xs-12 no-padding">
                        <div class="col-xs-4 no-padding"><label class="label-control" style="padding-top: 0px;">Kembalian</label></div>
                        <div class="col-xs-1 no-padding"><label class="label-control" style="padding-top: 0px;">:</label></div>
                        <div class="col-xs-7 no-padding text-right"><label class="label-control" style="padding-top: 0px;"><?php echo angkaDecimal($data['kembalian']); ?></label></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 no-padding"><hr style="margin-top: 5px; margin-bottom: 5px;"></div>
            <?php if ( isset($data['bayar_id']) && !empty($data['bayar_id']) ): ?>
                <div class="col-xs-12 no-padding">
                    <div class="col-xs-2 no-padding" style="padding-right: 5px;">
                        <button type="button" class="col-xs-12 btn btn-primary" onclick="sr.modalAddPembayaran(this)" data-id="<?php echo $data['bayar_id']; ?>"><i class="fa fa-plus"></i> Bayar</button>
                    </div>
                    <div class="col-xs-2 no-padding" style="padding-right: 5px;">
                        <button type="button" class="col-xs-12 btn btn-primary" onclick="sr.modalAddDiskon(this)" data-id="<?php echo $data['bayar_id']; ?>"><i class="fa fa-plus"></i> Diskon</button>
                    </div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding"></div>
                    <div class="col-xs-2 no-padding">
                        <button type="button" class="col-xs-12 btn btn-danger" onclick="sr.deleteTransaksi(this)" data-faktur="<?php echo $data['kode_faktur']; ?>"><i class="fa fa-trash"></i> Void Transaksi</button>
                        <!-- <button type="button" class="col-xs-12 btn btn-primary" onclick="bayar.rePrintNota(this)" data-faktur="<?php echo $data['kode_faktur']; ?>" data-id="<?php echo $data['bayar_id']; ?>"><i class="fa fa-print"></i> Re-Print Bill</button> -->
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
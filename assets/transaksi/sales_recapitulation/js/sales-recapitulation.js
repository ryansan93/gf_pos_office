var sr = {
	startUp: function() {
		sr.settingUp();
	}, // end - startUp

	settingUp: function() {
		$("#StartDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $("#EndDate").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $("#StartDate").on("dp.change", function (e) {
            var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
            $("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
        });
        var start_date = $("#StartDate").find('input').data('tgl');
        if ( !empty(start_date) && empty($("#StartDate").find('input').val()) ) {
        	$("#StartDate").data('DateTimePicker').date(moment(new Date(start_date)));
        }
	}, // end - settingUp

	getLists: function() {
        $('.modal').modal('hide');

		var err = 0;
		$.map( $('[data-required=1]'), function(ipt) {
			if ( empty( $(ipt).val() ) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			var params = {
				'start_date': dateSQL($("#StartDate").data('DateTimePicker').date()),
				'end_date': dateSQL($("#EndDate").data('DateTimePicker').date()),
                'branch': $('.branch').val()
			};

			$.ajax({
                url: 'transaksi/SalesRecapitulation/getLists',
                data: {
                    'params': params
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $('table tbody').html( html );
                }
            });
		}
	}, // end - getLists

    viewForm: function(elm) {
        $('.modal').modal('hide');

        var kode_faktur = $(elm).data('kode');

        sr.modalViewForm( kode_faktur );
    }, // end - viewForm

    modalViewForm: function (kode_faktur) {
        $('.modal').modal('hide');

        var data = {
            'kode_faktur': kode_faktur,
        };

        $.get('transaksi/SalesRecapitulation/viewForm',{
            'params': data
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                // $(this).find('.modal-header').css({'padding-top': '0px'});
                // $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                // $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '90%', 'max-height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%', 'max-height': '100%'});
                $(this).find('.modal-body').css({'height': '100%', 'max-height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%', 'max-height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%', 'max-height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%', 'max-height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    // $(this).priceFormat(Config[$(this).data('tipe')]);
                    priceFormat( $(this) );
                });

                var modal_body = $(this).find('.modal-body');

                // $(modal_body).find('.nav-tabs .nav-link:first').click();
                // $(modal_body).find('.btn_remove').click(function() {
                //     bayar.removeItem( $(this) );
                // });

                // $(modal_body).find('.btn_apply').click(function() {
                //     bayar.modalJumlahSplit( $(this) );
                // });
            });
        },'html');
    }, // end - modalViewForm

    modalAddPembayaran: function (elm) {
        var id_bayar = $(elm).attr('data-id');

        $.get('transaksi/SalesRecapitulation/modalAddPembayaran',{
            'params': id_bayar
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                // $(this).find('.modal-header').css({'padding-top': '0px'});
                // $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                // $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                var modal_body = $(this).find('.modal-body');

                $(this).find('select.jenis_kartu').select2().on('select2:select', function(e) {
                    var val = e.params.data.text.toLowerCase();
                    var cl = e.params.data.element.dataset.cl;

                    if ( !empty(cl) && cl == 1 ) {
                        $(modal_body).find('.jml_bayar').val(0);
                        $(modal_body).find('.jml_bayar').attr('disabled', 'disabled');
                    } else {
                        $(modal_body).find('.jml_bayar').removeAttr('disabled');
                    }
                });

                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    // $(this).priceFormat(Config[$(this).data('tipe')]);
                    priceFormat( $(this) );
                });

                $(modal_body).find('#Tanggal').datetimepicker({
                    locale: 'id',
                    format: 'DD MMM Y'
                });
                var tgl = $(modal_body).find('#Tanggal input').attr('data-tgl');
                if ( !empty(tgl) ) {
                    $(modal_body).find('#Tanggal').data('DateTimePicker').date( moment(new Date(tgl)) );
                }

                $(this).removeAttr('tabindex');
            });
        },'html');
    }, // end - modalAddPembayaran

    modalAddDiskon: function (elm) {
        var id_bayar = $(elm).attr('data-id');

        $.get('transaksi/SalesRecapitulation/modalAddDiskon',{
            'params': id_bayar
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    // $(this).priceFormat(Config[$(this).data('tipe')]);
                    priceFormat( $(this) );
                });

                $(this).removeAttr('tabindex');
            });
        },'html');
    }, // end - modalAddDiskon

    pilihDiskon: function (elm) {
        var aktif = $(elm).attr('data-aktif');

        if ( aktif == 1 ) {
            $(elm).attr('data-aktif', 0);
        } else {
            $(elm).attr('data-aktif', 1);
        }
    }, // end - pilihDiskon

    savePembayaran: function (elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;
        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            var sisa_tagihan = numeral.unformat($(modal).find('.sisa_tagihan').val());
            var jml_bayar = numeral.unformat($(modal).find('.jml_bayar').val());

            if ( jml_bayar > sisa_tagihan ) {
                bootbox.alert('Jumlah bayar yang anda input melebihi sisa tagihan.', function() {
                    $(modal).find('.jml_bayar').val( numeral.formatDec(sisa_tagihan) );
                });
            } else {
                $(elm).attr('disabled', 'disabled');

                var data = {
                    'id_bayar': $(elm).attr('data-id'),
                    // 'status_pembayaran': $(modal).find('select.status_pembayaran').select2('val'),
                    'tanggal': dateSQL($(modal).find('#Tanggal').data('DateTimePicker').date()),
                    'jenis_bayar': $(modal).find('select.jenis_kartu option:selected').text(),
                    'kode_jenis_kartu': $(modal).find('select.jenis_kartu').select2('val'),
                    'jml_bayar': jml_bayar,
                    'no_kartu': $(modal).find('input.no_kartu').val(),
                    'nama_kartu': $(modal).find('input.nama_kartu').val()
                };

                if ( sisa_tagihan > jml_bayar ) {
                    bootbox.confirm('Pembayaran kurang dari sisa tagihan apakah anda tetap ingin menyimpan pembayaran ?', function(result) {
                        if ( result ) {
                            sr.execSavePembayaran(data); 
                        }
                    });
                } else {
                    sr.execSavePembayaran(data);
                }
            }
        }
    }, // end - savePembayaran

    execSavePembayaran: function (data) {
        sr.verifikasiPinOtorisasi(function(data_verifikasi) {
            if ( data_verifikasi.status == 1 ) {
                $.ajax({
                    url: 'transaksi/SalesRecapitulation/savePembayaran',
                    data: {
                        'params': data,
                        'keterangan': data_verifikasi.keterangan,
                        'id_verifikasi': data_verifikasi.id_verifikasi
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading('Simpan Pembayaran . . .'); },
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            sr.hitungUlang( data.content.kode_faktur, data.message );
                        } else {
                            bootbox.alert( data.message );
                        }
                    }
                });
            }
        });
    }, // end - execSavePembayaran

    saveDiskon: function (elm) {
        var modal = $(elm).closest('.modal');

        var jml_data = $(modal).find('tr.data[data-aktif=1]').length;

        if ( jml_data == 0 ) {
            bootbox.alert('Tidak ada diskon yang anda pilih.');
        } else {
            sr.verifikasiPinOtorisasi(function(data_verifikasi) {
                if ( data_verifikasi.status == 1 ) {
                    $(elm).attr('disabled', 'disabled');

                    var params = $.map( $(modal).find('tr.data[data-aktif=1]'), function (tr) {
                        var _data = {
                            'id_bayar': $(elm).attr('data-id'),
                            'kode_diskon': $(tr).find('td.kode').text()
                        };

                        return _data;
                    });

                    $.ajax({
                        url: 'transaksi/SalesRecapitulation/saveDiskon',
                        data: {
                            'params': params,
                            'keterangan': data_verifikasi.keterangan,
                            'id_verifikasi': data_verifikasi.id_verifikasi
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading('Delete Pesanan . . .'); },
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                sr.hitungUlang( data.content.kode_faktur, data.message );
                            } else {
                                bootbox.alert( data.message );
                            }
                        }
                    });
                }
            });
        }
    }, // end - saveDiskon

    deletePesanan: function (elm) {
        var kode_faktur_item = $(elm).attr('data-kode');

        bootbox.confirm('Apakah anda yakin ingin menghapus data pesanan ?', function (result) {
            if ( result ) {
                sr.verifikasiPinOtorisasi(function(data_verifikasi) {
                    if ( data_verifikasi.status == 1 ) {
                        var params = {
                            'kode_faktur_item': kode_faktur_item
                        };

                        $.ajax({
                            url: 'transaksi/SalesRecapitulation/deletePesanan',
                            data: {
                                'params': params,
                                'keterangan': data_verifikasi.keterangan,
                                'id_verifikasi': data_verifikasi.id_verifikasi
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading('Delete Pesanan . . .'); },
                            success: function(data) {
                                hideLoading();
                                if ( data.status == 1 ) {
                                    sr.hitungUlang( data.content.kode_faktur, data.message );
                                } else {
                                    bootbox.alert( data.message );
                                }
                            }
                        });
                    }
                });
            }
        });
    }, // end - deletePesanan

    deletePembayaran: function (elm) {
        var id = $(elm).attr('data-id');

        bootbox.confirm('Apakah anda yakin ingin menghapus data pembayaran ?', function (result) {
            if ( result ) {
                sr.verifikasiPinOtorisasi(function(data_verifikasi) {
                    if ( data_verifikasi.status == 1 ) {
                        var params = {
                            'id': id
                        };

                        $.ajax({
                            url: 'transaksi/SalesRecapitulation/deletePembayaran',
                            data: {
                                'params': params,
                                'keterangan': data_verifikasi.keterangan,
                                'id_verifikasi': data_verifikasi.id_verifikasi
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading('Delete Pembayaran . . .'); },
                            success: function(data) {
                                hideLoading();
                                if ( data.status == 1 ) {
                                    sr.hitungUlang( data.content.kode_faktur, data.message );
                                } else {
                                    bootbox.alert( data.message );
                                }
                            }
                        });
                    }
                });
            }
        });
    }, // end - deletePembayaran

    deleteDiskon: function (elm) {
        var id = $(elm).attr('data-id');

        bootbox.confirm('Apakah anda yakin ingin menghapus data diskon ?', function (result) {
            if ( result ) {
                sr.verifikasiPinOtorisasi(function(data_verifikasi) {
                    if ( data_verifikasi.status == 1 ) {
                        var params = {
                            'id': id
                        };

                        $.ajax({
                            url: 'transaksi/SalesRecapitulation/deleteDiskon',
                            data: {
                                'params': params,
                                'keterangan': data_verifikasi.keterangan,
                                'id_verifikasi': data_verifikasi.id_verifikasi
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading('Delete Diskon . . .'); },
                            success: function(data) {
                                hideLoading();
                                if ( data.status == 1 ) {
                                    sr.hitungUlang( data.content.kode_faktur, data.message );
                                } else {
                                    bootbox.alert( data.message );
                                }
                            }
                        });
                    }
                });
            }
        });
    }, // end - deleteDiskon

    deleteTransaksi: function (elm) {
        sr.verifikasiPinOtorisasi(function(data_verifikasi) {
            if ( data_verifikasi.status == 1 ) {
                var kode_faktur = $(elm).attr('data-faktur');

                $.ajax({
                    url: 'transaksi/SalesRecapitulation/deleteTransaksi',
                    data: {
                        'params': kode_faktur,
                        'keterangan': data_verifikasi.keterangan,
                        'id_verifikasi': data_verifikasi.id_verifikasi
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading('Hapus Transaksi . . .'); },
                    success: function(data) {
                        hideLoading();

                        if ( data.status == 1 ) {
                            bootbox.alert( data.message, function () {
                                sr.getLists();
                            });
                        } else {
                            bootbox.alert( data.message );
                        }
                    }
                });
            }
        });
    }, // end - deleteTransaksi

    hitungUlang: function ( kode_faktur, message ) {
        // var params = {
        //     'kode_faktur': kode_faktur
        // };

        $.ajax({
            url: 'transaksi/SalesRecapitulation/hitungUlang',
            data: {
                'params': kode_faktur
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Hitung Ulang Data . . .'); },
            success: function(data) {
                hideLoading();

                if ( data.status == 1 ) {
                    bootbox.alert( message, function () {
                        sr.modalViewForm( kode_faktur );
                    });
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - hitungUlang

    verifikasiPinOtorisasi: function(action) {
        bootbox.dialog({
            message: '<p>Masukkan PIN Otorisasi untuk mengubah data.</p><p><b>Keterangan</b></p><p><textarea class="form-control keterangan"></textarea></p><p><input type="password" class="form-control text-center pin" data-tipe="angka" placeholder="PIN" /></p>',
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Batal',
                    className: 'btn-danger',
                    callback: function(){}
                },
                ok: {
                    label: '<i class="fa fa-check"></i> Lanjut',
                    className: 'btn-primary',
                    callback: function(){
                        var pin = $('.pin').val();
                        var keterangan = $('.keterangan').val();

                        $.ajax({
                            url: 'transaksi/SalesRecapitulation/cekPinOtorisasi',
                            data: {
                                'pin': pin
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading(); },
                            success: function(data) {
                                // hideLoading();
                                if ( data.status == 1 ) {
                                    var _data = {
                                        'status': data.status,
                                        'keterangan': keterangan,
                                        'id_verifikasi': data.content.id_verifikasi
                                    };

                                    action(_data);
                                } else {
                                    bootbox.alert(data.message, function() {
                                        sr.verifikasiPinOtorisasi(action);
                                    });
                                }
                            }
                        });
                    }
                }
            }
        });
    }, // end - verifikasiPinOtorisasi
};

sr.startUp();
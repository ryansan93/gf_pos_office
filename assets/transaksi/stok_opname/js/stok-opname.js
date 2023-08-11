var timeout = null;

var so = {
	startUp: function () {
		so.settingUp();
	}, // end - startUp

	settingUp: function () {
        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#TglStokOpname").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        var data_tgl = $("#TglStokOpname").find('input').attr('data-tgl');
        if ( !empty(data_tgl) ) {
            $("#TglStokOpname").data("DateTimePicker").date(moment(new Date(data_tgl)));
        }

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
        $("#EndDate").on("dp.change", function (e) {
            var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
            if ( maxDate >= (today+' 00:00:00') ) {
                $("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
            }
        });

        $('select.group_item').select2({placeholder: '-- Pilih Group Item --'});

        $('.gudang').select2();
        $('.gudang_riwayat').select2();

        $('[data-tipe=integer],[data-tipe=decimal],[data-tipe=decimal3],[data-tipe=decimal4],[data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);

            priceFormat( $(this) );
        });

        $('.filter_by_column').keyup(function () {
            var elm = $(this);

            clearTimeout(timeout);
            timeout = setTimeout(function() {
                filter_by_column( elm );
            }, 250);
        });
	}, // end - settingUp

    changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        var edit = $(elm).data('edit');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');

            so.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/StokOpname/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);

                if ( $(dcontent).find('button.btn-list-item').length > 0 ) {
                    var id = $(dcontent).find('button.btn-list-item').attr('data-id');

                    if ( !empty( id ) ) {
                        setTimeout(function(){
                            $(dcontent).find('button.btn-list-item').click();
                        }, 500);
                    }
                }

                so.settingUp();
            },
        });
    }, // end - loadForm

    getListItem: function (elm) {
        var div = $('#action');

        var err = 0;
        $.map( $(div).find('.header [data-required=1]'), function (ipt) {
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
                'tanggal': dateSQL( $(div).find('#TglStokOpname').data('DateTimePicker').date() ),
                'gudang_kode': $(div).find('.gudang').select2('val'),
                'group_item': $(div).find('select.group_item').select2('val'),
                'so_id': $(elm).attr('data-id')
            };

            $.ajax({
                url: 'transaksi/StokOpname/getListItem',
                data: {
                    'params': params
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $(div).find('.tbl_item tbody').html( html );

                    so.settingUp();
                }
            });
        }
    }, // end - getListItem

    getLists: function () {
        var div = $('#riwayat');

        var err = 0;
        $.map( $(div).find('[data-required=1]'), function (ipt) {
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
                'start_date': dateSQL( $(div).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(div).find('#EndDate').data('DateTimePicker').date() ),
                'gudang_kode': $(div).find('.gudang_riwayat').select2('val')
            };

            $.ajax({
                url: 'transaksi/StokOpname/getLists',
                data: {
                    'params': params
                },
                type: 'GET',
                dataType: 'HTML',
                beforeSend: function() { showLoading(); },
                success: function(html) {
                    hideLoading();

                    $(div).find('.tbl_riwayat tbody').html( html );
                }
            });
        }
    }, // end - getLists

	save: function () {
        var div = $('#action');

		var err = 0;

        $.map( $(div).find('[data-required=1]'), function (ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        var data_item = 0;
        $.map( $(div).find('tr.data'), function (tr) {
            var jumlah = numeral.unformat($(tr).find('input.jumlah').val());
            var harga = numeral.unformat($(tr).find('input.harga').val());

            if ( jumlah > 0 || !empty(harga) ) {
                if ( empty(jumlah) ) {
                    $(tr).find('input.jumlah').parent().addClass('has-error');
                    err++;
                } else {
                    $(tr).find('input.jumlah').parent().removeClass('has-error');
                }

                if ( empty(harga) ) {
                    $(tr).find('input.harga').parent().addClass('has-error');
                    err++;
                } else {
                    $(tr).find('input.harga').parent().removeClass('has-error');
                }

                if ( !empty(jumlah) && !empty(harga) ) {
                    data_item++;
                }
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else if ( data_item == 0 ) {
            bootbox.alert('Tidak ada data Item yang anda isi, harap cek kembali inputan anda.');
        } else {
            var list_item = $.map( $(div).find('tr.data'), function (tr) {
                // if ( $(tr).find('input[type=checkbox]:checked') ) {
                var jumlah = numeral.unformat($(tr).find('input.jumlah').val());
                var harga = numeral.unformat($(tr).find('input.harga').val());

                if ( jumlah > 0 || harga > 0 ) {
                    var _list_item = {
                        'item_kode': $(tr).find('td.kode').text(),
                        'satuan': $(tr).find('select.satuan').val(),
                        'pengali': $(tr).find('select.satuan option:selected').attr('data-pengali'),
                        'jumlah': jumlah,
                        'harga': harga,
                        'satuan_old': $(tr).find('select.satuan').attr('data-awal-satuan'),
                        'pengali_old': $(tr).find('select.satuan').attr('data-awal-pengali'),
                        'jumlah_old': $(tr).find('input.jumlah').attr('data-awal'),
                        'harga_old': $(tr).find('input.harga').attr('data-awal')
                    };

                    return _list_item;
                }
                // }
            });

            if ( list_item.length == 0 ) {
                bootbox.alert('Tidak ada item yang anda pilih.');
            } else {
                bootbox.confirm('Apakah anda yakin ingin menyimpan data Stok Opname ?', function (result) {
                    if ( result ) {
                        var params = {
                            'gudang_kode': $('.gudang').select2('val'),
                            'tanggal': dateSQL( $('#TglStokOpname').data('DateTimePicker').date() ),
                            'list_item': list_item
                        };

                        $.ajax({
                            url: 'transaksi/StokOpname/save',
                            data: {
                                'params': params
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading(); },
                            success: function(data) {
                                hideLoading();
                                if ( data.status == 1 ) {
                                    so.hitungStokOpname( data.content.kode, data.content.tanggal, data.content.delete, data.content.kode_gudang );
                                } else {
                                    bootbox.alert( data.message );
                                }
                            }
                        });
                    }
                });
            }
        }
	}, // end - save

    edit: function (elm) {
        var div = $('#action');

		var err = 0;

        $.map( $(div).find('[data-required=1]'), function (ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        var data_item = 0;
        $.map( $(div).find('tr.data'), function (tr) {
            var jumlah = numeral.unformat($(tr).find('input.jumlah').val());
            var harga = numeral.unformat($(tr).find('input.harga').val());

            if ( jumlah > 0 || !empty(harga) ) {
                if ( empty(jumlah) ) {
                    $(tr).find('input.jumlah').parent().addClass('has-error');
                    err++;
                } else {
                    $(tr).find('input.jumlah').parent().removeClass('has-error');
                }

                if ( empty(harga) ) {
                    $(tr).find('input.harga').parent().addClass('has-error');
                    err++;
                } else {
                    $(tr).find('input.harga').parent().removeClass('has-error');
                }

                if ( !empty(jumlah) && !empty(harga) ) {
                    data_item++;
                }
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else if ( data_item == 0 ) {
            bootbox.alert('Tidak ada data Item yang anda isi, harap cek kembali inputan anda.');
        } else {
            var list_item = $.map( $(div).find('tr.data'), function (tr) {
                // if ( $(tr).find('input[type=checkbox]:checked') ) {
                var jumlah = numeral.unformat($(tr).find('input.jumlah').val());
                var harga = numeral.unformat($(tr).find('input.harga').val());

                if ( jumlah > 0 || harga > 0 ) {
                    var _list_item = {
                        'item_kode': $(tr).find('td.kode').text(),
                        'satuan': $(tr).find('select.satuan').val(),
                        'pengali': $(tr).find('select.satuan option:selected').attr('data-pengali'),
                        'jumlah': jumlah,
                        'harga': harga,
                        'satuan_old': $(tr).find('select.satuan').attr('data-awal-satuan'),
                        'pengali_old': $(tr).find('select.satuan').attr('data-awal-pengali'),
                        'jumlah_old': $(tr).find('input.jumlah').attr('data-awal'),
                        'harga_old': $(tr).find('input.harga').attr('data-awal')
                    };

                    return _list_item;
                }
                // }
            });

            if ( list_item.length == 0 ) {
                bootbox.alert('Tidak ada item yang anda pilih.');
            } else {
                bootbox.confirm('Apakah anda yakin ingin meng-ubah data Stok Opname ?', function (result) {
                    if ( result ) {
                        var params = {
                            'id': $(elm).attr('data-id'),
                            'gudang_kode': $('.gudang').select2('val'),
                            'tanggal': dateSQL( $('#TglStokOpname').data('DateTimePicker').date() ),
                            'list_item': list_item
                        };

                        $.ajax({
                            url: 'transaksi/StokOpname/edit',
                            data: {
                                'params': params
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading(); },
                            success: function(data) {
                                hideLoading();
                                if ( data.status == 1 ) {
                                    so.hitungStokOpname( data.content.kode, data.content.tanggal, data.content.delete, data.content.kode_gudang );
                                } else {
                                    bootbox.alert( data.message );
                                }
                            }
                        });
                    }
                });
            }
        }
	}, // end - edit

    delete: function (elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-ubah data Stok Opname ?', function (result) {
            if ( result ) {
                var params = {
                    'id': $(elm).attr('data-id')
                };

                $.ajax({
                    url: 'transaksi/StokOpname/delete',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            so.hitungStokOpname( data.content.kode, data.content.tanggal, data.content.delete, data.content.kode_gudang );
                        } else {
                            bootbox.alert( data.message );
                        }
                    }
                });
            }
        });
    }, // end - delete

    hitungStokOpname: function (kode, tanggal, _delete, kode_gudang) {
        var params = {
            'kode': kode,
            'tanggal': tanggal,
            'delete': _delete,
            'kode_gudang': kode_gudang
        };

        $.ajax({
            url: 'transaksi/StokOpname/hitungStokOpname',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading('Hitung Stok . . .'); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert( data.message, function () {
                        location.reload();
                    });
                } else {
                    bootbox.alert( data.message );
                }
            }
        });
    }, // end - hitungStokOpname

    choseItem: function (elm) {
        var tr = $(elm).closest('tr');

        if ( $(elm).is(':checked') ) {
            $(tr).find('input:not([type=checkbox]), select').removeAttr('disabled');
        } else {
            $(tr).find('input:not([type=checkbox]), select').attr('disabled', 'disabled');
            $(tr).find('input:not([type=checkbox])').val('');

            var data_awal = $(tr).find('input.harga').attr('data-awal');
            $(tr).find('input.harga').val( data_awal );
        }
    }, // end - choseItem

    hitTotal: function (elm) {
        var tr = $(elm).closest('tr');

        var jumlah = numeral.unformat( $(tr).find('input.jumlah').val() );
        var harga = numeral.unformat( $(tr).find('input.harga').val() );

        var total = parseFloat(jumlah) * parseFloat(harga);

        $(tr).find('td.total').text( numeral.formatDec(total) );
    }, // end - hitTotal
};

so.startUp();
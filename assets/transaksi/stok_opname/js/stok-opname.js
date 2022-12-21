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
        var minDateTglStokOpname = today+' 00:00:00';
        $("#TglStokOpname").data("DateTimePicker").minDate(moment(new Date(minDateTglStokOpname)));

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

        $('.gudang').select2();
        $('.gudang_riwayat').select2();
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

                so.settingUp();
            },
        });
    }, // end - loadForm

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
            var jumlah = $(tr).find('input.jumlah').val();
            var harga = $(tr).find('input.harga').val();

            if ( !empty(jumlah) || !empty(harga) ) {
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
            bootbox.confirm('Apakah anda yakin ingin menyimpan data Stok Opname ?', function (result) {
                if ( result ) {
                    var list_item = $.map( $(div).find('tr.data'), function (tr) {
                        var jumlah = $(tr).find('input.jumlah').val();
                        var harga = $(tr).find('input.harga').val();

                        if ( !empty(jumlah) && !empty(harga) ) {
                            var _list_item = {
                                'item_kode': $(tr).find('td.kode').text(),
                                'satuan': $(tr).find('select.satuan').val(),
                                'pengali': numeral.unformat( $(tr).find('select.satuan option:selected').attr('data-pengali') ),
                                'jumlah': numeral.unformat( jumlah ),
                                'harga': numeral.unformat( harga )
                            };

                            return _list_item;
                        }
                    });

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
                                bootbox.alert( data.message, function() {
                                    bom.loadForm();
                                });
                            } else {
                                bootbox.alert( data.message );
                            }
                        }
                    });
                }
            });
        }
	}, // end - save
};

so.startUp();
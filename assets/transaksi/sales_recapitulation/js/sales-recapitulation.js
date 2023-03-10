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
                    $(this).priceFormat(Config[$(this).data('tipe')]);
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

    deletePesanan: function (elm) {
        var kode_faktur_item = $(elm).attr('data-kode');

        bootbox.confirm('Apakah anda yakin ingin menghapus data pesanan ?', function (result) {
            if ( result ) {
                var params = {
                    'kode_faktur_item': kode_faktur_item
                };

                $.ajax({
                    url: 'transaksi/SalesRecapitulation/deletePesanan',
                    data: {
                        'params': params
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
    }, // end - deletePesanan

    deletePembayaran: function (elm) {
        var id = $(elm).attr('data-id');

        bootbox.confirm('Apakah anda yakin ingin menghapus data pembayaran ?', function (result) {
            if ( result ) {
                var params = {
                    'id': id
                };

                $.ajax({
                    url: 'transaksi/SalesRecapitulation/deletePembayaran',
                    data: {
                        'params': params
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
    }, // end - deletePembayaran

    deleteDiskon: function (elm) {
        var id = $(elm).attr('data-id');

        bootbox.confirm('Apakah anda yakin ingin menghapus data diskon ?', function (result) {
            if ( result ) {
                var params = {
                    'id': id
                };

                $.ajax({
                    url: 'transaksi/SalesRecapitulation/deleteDiskon',
                    data: {
                        'params': params
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
    }, // end - deleteDiskon

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
};

sr.startUp();
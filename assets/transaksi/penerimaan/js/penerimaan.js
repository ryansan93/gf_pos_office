var terima = {
	start_up: function () {
		terima.setting_up();
	}, // end - start_up

	setting_up: function() {
        var today = moment(new Date()).format('YYYY-MM-DD');
        $("#StartDate, #EndDate").datetimepicker({
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

        $("#TglTerima").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            minDate: moment(new Date((today+' 00:00:00')))
        });
        if ( !empty($("#TglTerima").find('input').data('tgl')) ) {
            var tgl = $("#TglTerima").find('input').data('tgl');
            $("#TglTerima").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this).val() );
        });

        $('.gudang').select2();
        $('.item').select2().on('select2:select', function (e) {
            var _tr = $(this).closest('tr');
            var select_satuan = $(_tr).find('select.satuan');

            var data = e.params.data.element.dataset;
            var satuan = JSON.parse( data.satuan );

            var opt = '<option value="">Pilih Satuan</option>';
            for (var i = 0; i < satuan.length; i++) {
                opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
            }

            $(select_satuan).html( opt );
            $(select_satuan).removeAttr('disabled');
            $(_tr).find('.jumlah').removeAttr('disabled');
            $(_tr).find('.harga').removeAttr('disabled');
        });
    }, // end - setting_up

    addRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        $(tr).find('select.item').select2('destroy')
                                   .removeAttr('data-live-search')
                                   .removeAttr('data-select2-id')
                                   .removeAttr('aria-hidden')
                                   .removeAttr('tabindex');
        $(tr).find('select.item option').removeAttr('data-select2-id');

        var tr_clone = $(tr).clone();

        $(tr_clone).find('input, select').val('');
        $(tr_clone).find('select.satuan').html('<option value="">Pilih Satuan</option>');
        $(tr_clone).find('select.satuan').attr('disabled', 'disabled');
        $(tr_clone).find('.jumlah').attr('disabled', 'disabled');
        $(tr_clone).find('.harga').attr('disabled', 'disabled');

        $(tr_clone).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this).val() );
        });

        $(tbody).append( $(tr_clone) );

        $.each($(tbody).find('select.item'), function(a) {
            $(this).select2();
            $(this).on('select2:select', function (e) {
                var _tr = $(this).closest('tr');
                var select_satuan = $(_tr).find('select.satuan');

                var data = e.params.data.element.dataset;
                var satuan = JSON.parse( data.satuan );

                var opt = '<option value="">Pilih Satuan</option>';
                for (var i = 0; i < satuan.length; i++) {
                    opt += '<option value="'+satuan[i].satuan+'" data-pengali="'+satuan[i].pengali+'">'+satuan[i].satuan+'</option>';
                }

                $(select_satuan).html( opt );
                $(select_satuan).removeAttr('disabled');
                $(_tr).find('.jumlah').removeAttr('disabled');
                $(_tr).find('.harga').removeAttr('disabled');
            });
        });
    }, // end - addRow

    removeRow: function (elm) {
        var tr = $(elm).closest('tr');
        var tbody = $(tr).closest('tbody');

        if ( $(tbody).find('tr').length > 0 ) {
            $(tr).remove();
        }
    }, // end - addRow

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

            terima.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/Penerimaan/loadForm',
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
                terima.setting_up();
            },
        });
    }, // end - loadForm

	getLists: function() {
        var dcontent = $('div#riwayat');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
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
            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

            $.ajax({
                url : 'transaksi/Penerimaan/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading(); },
                success : function(html){
                    hideLoading();
                    $(tbody).html(html);
                },
            });
        }
    }, // end - getLists

	save: function() {
		var dcontent = $('#action');
		var err = 0;
		$.map( $(dcontent).find('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var detail = $.map( $(dcontent).find('.tbl_detail tbody tr'), function(tr) {
						var _detail = {
                            'item_kode': $(tr).find('.item').select2('val'),
                            'satuan': $(tr).find('.satuan').val(),
							'pengali': $(tr).find('.satuan option:selected').attr('data-pengali'),
							'jumlah_terima': numeral.unformat($(tr).find('input.jumlah').val()),
							'harga': numeral.unformat($(tr).find('input.harga').val())
						};

						return _detail;
					});

					var data = {
						'tgl_terima': dateSQL( $(dcontent).find('#TglTerima').data('DateTimePicker').date() ),
                        'no_faktur': $(dcontent).find('.no_faktur').val(),
                        'nama_pic': $(dcontent).find('.nama_pic').val(),
                        'gudang': $(dcontent).find('.gudang').select2('val'),
						'supplier': $(dcontent).find('.supplier').val(),
						'detail': detail
					};

					$.ajax({
		                url: 'transaksi/Penerimaan/save',
		                dataType: 'json',
		                type: 'post',
		                data: {
		                	'params': data
		                },
		                beforeSend: function() {
		                    showLoading();
		                },
		                success: function(data) {
		                    hideLoading();
		                    if ( data.status == 1 ) {
		                    	terima.hitungStok( data.content.id );
		                    } else {
		                        bootbox.alert(data.message);
		                    };
		                },
		            });
				}
			});
		}
	}, // end - save

    hitungStok: function (kode) {
        var params = {'kode': kode};

        $.ajax({
            url: 'transaksi/Penerimaan/hitungStok',
            data: {
                'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
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
    }, // end - hitungStok

    hitTotal: function (elm) {
        var tr = $(elm).closest('tr');

        var jumlah = numeral.unformat($(tr).find('.jumlah').val());
        var harga = numeral.unformat($(tr).find('.harga').val());

        var total = harga * jumlah;

        $(tr).find('.total').val( numeral.formatDec(total) );
    }, // end - hitTotal
};

terima.start_up();
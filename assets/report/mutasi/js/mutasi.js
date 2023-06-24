var mutasi = {
	start_up: function () {
		mutasi.setting_up();
	}, // end - start_up

	setting_up: function() {
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
        	$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
        });

        $('.gudang_asal').select2({placeholder: 'Pilih Gudang'}).on("select2:select", function (e) {
            var gudang = $('.gudang_asal').select2().val();

            for (var i = 0; i < gudang.length; i++) {
                if ( gudang[i] == 'all' ) {
                    $('.gudang_asal').select2().val('all').trigger('change');

                    i = gudang.length;
                }
            }

            $('.gudang_asal').next('span.select2').css('width', '100%');
        });
        $('.gudang_tujuan').select2({placeholder: 'Pilih Gudang'}).on("select2:select", function (e) {
            var gudang = $('.gudang_tujuan').select2().val();

            for (var i = 0; i < gudang.length; i++) {
                if ( gudang[i] == 'all' ) {
                    $('.gudang_tujuan').select2().val('all').trigger('change');

                    i = gudang.length;
                }
            }

            $('.gudang_tujuan').next('span.select2').css('width', '100%');
        });
	}, // end - setting_up

	getLists: function(elm) {
		var err = 0

		$.map( $('[data-required=1]'), function(ipt) {
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
			var params = {
				'gudang_asal': $('.gudang_asal').select2('val'),
				'gudang_tujuan': $('.gudang_tujuan').select2('val'),
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'report/Mutasi/getLists',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	$('table.tbl_report tbody').html( data.content.list_report );
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - getLists

	exportExcel: function(elm) {
		var err = 0

		$.map( $('[data-required=1]'), function(ipt) {
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
			var params = {
				'gudang_asal': $('.gudang_asal').select2('val'),
				'gudang_tujuan': $('.gudang_tujuan').select2('val'),
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'report/Mutasi/excryptParamsExportExcel',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	window.open('report/Mutasi/exportExcel/'+data.content.data, 'blank');
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - exportExcel
};

mutasi.start_up();
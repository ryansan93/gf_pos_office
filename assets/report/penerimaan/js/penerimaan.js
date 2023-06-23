var terima = {
	start_up: function () {
		terima.setting_up();
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

        $('.branch').select2();
        $('.supplier').select2({placeholder: 'Pilih Supplier'}).on("select2:select", function (e) {
            var supplier = $('.supplier').select2().val();

            for (var i = 0; i < supplier.length; i++) {
                if ( supplier[i] == 'all' ) {
                    $('.supplier').select2().val('all').trigger('change');

                    i = supplier.length;
                }
            }

            $('.supplier').next('span.select2').css('width', '100%');
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
				'branch': $('.branch').val(),
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'report/Pembelian/getLists',
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
};

terima.start_up();
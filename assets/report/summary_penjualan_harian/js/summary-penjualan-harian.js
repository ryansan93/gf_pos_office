var sh = {
	start_up: function () {
		sh.setting_up();
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
	            url: 'report/SummaryPenjualanHarian/getLists',
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

	exportPdf: function(elm) {
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
	            url: 'report/SummaryPenjualanHarian/excryptParamsExportPdf',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	window.open('report/SummaryPenjualanHarian/exportPdf/'+data.content.data, 'blank');
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - exportPdf
};

sh.start_up();
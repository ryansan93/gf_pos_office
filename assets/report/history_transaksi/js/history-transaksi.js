var ht = {
	startUp: function () {
		ht.settingUp();
	}, // end - start_up

	settingUp: function() {
		$("#Tanggal").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });

        $('.branch').select2();
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
				'branch': $('.branch').select2('val'),
				'tanggal': dateSQL($('#Tanggal').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'report/HistoryTransaksi/getLists',
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

ht.startUp();
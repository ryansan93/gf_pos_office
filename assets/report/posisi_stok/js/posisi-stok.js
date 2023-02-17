var ps = {
	start_up: function () {
		ps.setting_up();
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
      //   $("#StartDate").on("dp.change", function (e) {
    		// var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
      //   	$("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
      //   });
      //   $("#EndDate").on("dp.change", function (e) {
    		// var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
      //   	$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
      //   });

        $('.gudang').select2();
        $('.item').select2({placeholder: 'Pilih Item'}).on("select2:select", function (e) {
            var item = $('.item').select2().val();

            for (var i = 0; i < item.length; i++) {
                if ( item[i] == 'all' ) {
                    $('.item').select2().val('all').trigger('change');

                    i = item.length;
                }
            }

            $('.item').next('span.select2').css('width', '100%');
        });
        $('.item').next('span.select2').css('width', '100%');
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
				'gudang': $('.gudang').val(),
				'item': $('.item').val(),
				'start_date': dateSQL($('#StartDate').data('DateTimePicker').date()),
				'end_date': dateSQL($('#EndDate').data('DateTimePicker').date())
			};

			$.ajax({
	            url: 'report/PosisiStok/getLists',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	$('table.tbl_report tbody').remove();
	                	$('table.tbl_report thead').after( data.content.list_report );
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - getLists

	export_excel : function () {
		var _data = '<table border="1">'+$('table.tbl_report').html()+'</table>';

        var blob = new Blob([_data], { type: 'application/vnd.ms-excel' });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = "export-mutasi-stok.xls";
        document.body.appendChild(a);
        a.click();
	}, // end - export_excel
};

ps.start_up();
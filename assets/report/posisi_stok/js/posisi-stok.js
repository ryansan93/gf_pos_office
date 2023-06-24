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
        $('.item').select2({placeholder: 'Pilih Item'});

        $('.group_item').select2({placeholder: 'Pilih Group Item'})
        .on("select2:select", function (e) {
            var group_item = $('.group_item').select2('val');

            for (var i = 0; i < group_item.length; i++) {
                if ( group_item[i] == 'all' ) {
                    $('.group_item').select2().val('all').trigger('change');

                    i = group_item.length;
                }
            }

            $('.group_item').next('span.select2').css('width', '100%');

            ps.getItem( group_item );
        })
        .on("select2:unselect", function (e) {
        	var group_item = $('.group_item').select2('val');

        	ps.getItem( group_item );
        });
	}, // end - setting_up

	getItem: function(kode_group) {
		$('.item').find('option:not([value=all])').attr('disabled', 'disabled');

		if ( !empty(kode_group) ) {
			if ( !kode_group.includes("all") ) {
				for (var i = 0; i < kode_group.length; i++) {
					$('.item').find('option[data-kodegroup="'+kode_group[i]+'"]').removeAttr('disabled');
				}
			} else {
				$('.item').find('option').removeAttr('disabled');
			}

			$('.item').removeAttr('disabled');
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
		} else {
			$('.item').attr('disabled', 'disabled');
			$('.item').select2({placeholder: 'Pilih Item'});
		}
	}, // end - getItem

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
				'group_item': $('.group_item').val(),
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
	                	$('table.tbl_report tbody').html( data.content.list_report );
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
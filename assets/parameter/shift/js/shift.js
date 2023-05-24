var shift = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/Shift/modalAddForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                var today = moment(new Date()).format('YYYY-MM-DD');

                $("#StartTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#EndTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        // $("#StartTime").on("dp.change", function (e) {
	        	// 	var minDate = dateTimeSQL($("#StartTime").data("DateTimePicker").date());
	         //    	$("#EndTime").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        // });
		        // $("#EndTime").on("dp.change", function (e) {
	        	// 	var maxDate = dateTimeSQL($("#EndTime").data("DateTimePicker").date());
	        	// 	if ( maxDate >= (today+' 00:00:00') ) {
	         //    		$("#StartTime").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
	        	// 	}
		        // });
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		$('.modal').modal('hide');

		var tr = $(elm).closest('tr');

        $.get('parameter/Shift/modalEditForm',{
            'id': $(elm).attr('data-id')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                var today = moment(new Date()).format('YYYY-MM-DD');

                $("#StartTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#EndTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        // $("#StartTime").on("dp.change", function (e) {
	        	// 	var minDate = dateTimeSQL($("#StartTime").data("DateTimePicker").date());
	         //    	$("#EndTime").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        // });
		        // $("#EndTime").on("dp.change", function (e) {
	        	// 	var maxDate = dateTimeSQL($("#EndTime").data("DateTimePicker").date());
	        	// 	if ( maxDate >= (today+' 00:00:00') ) {
	         //    		$("#StartTime").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
	        	// 	}
		        // });

		        var startTime = $("#StartTime").find('input').attr('data-tgl');
		        var endTime = $("#EndTime").find('input').attr('data-tgl');

		        var _startTime = today+' '+startTime;
		        var _endTime = today+' '+endTime;

		        $("#StartTime").data("DateTimePicker").date(moment(new Date(_startTime)));
		        $("#EndTime").data("DateTimePicker").date(moment(new Date(_endTime)));
            });
        },'html');
	}, // end - modalEditForm

	save: function() {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
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
			$('.modal').modal('hide');

			var shift = $(div).find('.shift').val().toUpperCase();
			var start = dateTimeSQL($(div).find('#StartTime').data('DateTimePicker').date());
			var end = dateTimeSQL($(div).find('#EndTime').data('DateTimePicker').date());

			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var data = {
						'shift': shift,
						'start': start,
						'end': end
					};

			        $.ajax({
			            url: 'parameter/Shift/save',
			            data: {
			                'params': data
			            },
			            type: 'POST',
			            dataType: 'JSON',
			            beforeSend: function() { showLoading(); },
			            success: function(data) {
			                hideLoading();
			                if ( data.status == 1 ) {
			                	bootbox.alert(data.message, function() {
			                		location.reload();
			                	});
			                } else {
			                    bootbox.alert(data.message);
			                }
			            }
			        });
				}
			});
		}
    }, // end - save

    edit: function(elm) {
		var div = $('.modal');

		var err = 0;
		$.map( $(div).find('[data-required=1]'), function(ipt) {
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
			$('.modal').modal('hide');

			var id = $(elm).attr('data-id');
			var shift = $(div).find('.shift').val().toUpperCase();
			var start = dateTimeSQL($(div).find('#StartTime').data('DateTimePicker').date());
			var end = dateTimeSQL($(div).find('#EndTime').data('DateTimePicker').date());

			bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
				if ( result ) {
					var data = {
						'id': id,
						'shift': shift,
						'start': start,
						'end': end
					};

			        $.ajax({
			            url: 'parameter/Shift/edit',
			            data: {
			                'params': data
			            },
			            type: 'POST',
			            dataType: 'JSON',
			            beforeSend: function() { showLoading(); },
			            success: function(data) {
			                hideLoading();
			                if ( data.status == 1 ) {
			                	bootbox.alert(data.message, function() {
			                		location.reload();
			                	});
			                } else {
			                    bootbox.alert(data.message);
			                }
			            }
			        });
				}
			});
		}
    }, // end - edit

    delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				var id = $(elm).attr('data-id');

		        $.ajax({
		            url: 'parameter/Shift/delete',
		            data: {
		                'id': id
		            },
		            type: 'POST',
		            dataType: 'JSON',
		            beforeSend: function() { showLoading(); },
		            success: function(data) {
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert(data.message, function() {
		                		location.reload();
		                	});
		                } else {
		                    bootbox.alert(data.message);
		                }
		            }
		        });
			}
		});
    }, // end - delete
};

shift.start_up();
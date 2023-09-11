var mg = {
	startUp: function () {
		mg.settingUp();
	}, // end - startUp

	addRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		var tr_clone = $(tr).clone();

		$(tr_clone).find('td').removeAttr('data-select2-id');
		$(tr_clone).find('.select2').remove();
		$(tr_clone).find('select').removeClass('select2-hidden-accessible');
		$(tr_clone).find('select').removeAttr('data-select2-id');
		$(tr_clone).find('select').removeAttr('tabindex');
		$(tr_clone).find('select').removeAttr('aria-hidden');
		$(tr_clone).find('select option').removeAttr('data-select2-id');

		$(tr_clone).find('input, select').val('');

		$(tbody).append( $(tr_clone) );

		$(tbody).find('tr:last select').select2({placeholder: 'Pilih Menu'});
	}, // end - addRow

	removeRow: function (elm) {
		var tr = $(elm).closest('tr');
		var tbody = $(tr).closest('tbody');

		if ( $(tbody).find('tr').length > 1 ) {
			$(tr).remove();
		}
	}, // end - addRow

	settingUp: function () {
		$("[name=tglInput]").datetimepicker({
			locale: 'id',
            format: 'DD MMM Y',
            // minDate: new Date().setHours(0,0,0,0),
            useCurrent: false
		});

		$.map( $("[name=tglInput]"), function (div) {
			var data_tgl = $(div).find('input').attr('data-tgl');

			if ( !empty(data_tgl) ) {
				$(div).data('DateTimePicker').date( moment(new Date(data_tgl)) );
			}
		});

		$('#riwayat .branch').select2({placeholder: 'Pilih Branch'}).on("select2:select", function (e) {
            var branch = $('#riwayat .branch').select2().val();

            for (var i = 0; i < branch.length; i++) {
                if ( branch[i] == 'all' ) {
                    $('#riwayat .branch').select2().val('all').trigger('change');

                    i = branch.length;
                }
            }

            $('#riwayat .branch').next('span.select2').css('width', '100%');
        });
        $('#riwayat .branch').next('span.select2').css('width', '100%');

        $('#action .branch').select2({placeholder: 'Pilih Branch'}).on("select2:select", function (e) {
        	var branch = $('#action .branch').select2().val();

        	mg.getMenu( branch );

            $('#action .branch').next('span.select2').css('width', '100%');
        });
        $('#action .branch').next('span.select2').css('width', '100%');
	}, // end - settingUp

	getLists: function () {
		var div_active = $('div.div-tab.active');

		var err = 0;
		$.map( $(div_active).find('[data-required=1]'), function (ipt) {
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
			$.ajax({
	            url: 'transaksi/MenuGagal/getLists',
	            data: {
	                'date': dateSQL($(div_active).find('#tglInput').data('DateTimePicker').date()),
	                'branch_kode': $(div_active).find('.branch').select2('val')
	            },
	            type: 'GET',
	            dataType: 'HTML',
	            beforeSend: function() { showLoading(); },
	            success: function(html) {
	                hideLoading();
	                
	                $(div_active).find('table tbody').html( html );
	            }
	        });
		}
	}, // end - getLists

	changeTabActive: function (elm) {
		var href = $(elm).attr('data-href');

	    var div_tab_contain = $(elm).closest('div.tab-contain');

	    $(div_tab_contain).find('div.div-tab').removeClass('active');
	    $(div_tab_contain).find('div.div-tab').addClass('non-active');

	    $(div_tab_contain).find('div#'+href).removeClass('non-active');
	    $(div_tab_contain).find('div#'+href).addClass('active');

	    var id = $(elm).attr('data-id');
	    var edit = $(elm).attr('data-edit');

	    mg.loadForm( id, edit );
	}, // end - changeTabActive

	loadForm: function (id, edit) {
		$.ajax({
            url: 'transaksi/MenuGagal/loadForm',
            data: {
                'id': id,
                'edit': edit
            },
            type: 'GET',
            dataType: 'HTML',
            beforeSend: function() { showLoading(); },
            success: function(html) {
                hideLoading();
                
                $('div.div-tab.active').html( html );

                mg.settingUp();

                if ( !empty(edit) ) {
                	var branch = $('div.div-tab.active').find('.branch').select2('val');

                	mg.getMenu( branch );
                }
            }
        });
	}, // end - loadForm

	getMenu: function (branch) {
		var opt = '<option>Pilih Menu</option>';

		if ( !empty(branch) ) {			
			$.ajax({
	            url: 'transaksi/MenuGagal/getMenu',
	            data: {
	                'params': branch
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	if ( !empty(data.content) ) {
	                		for (var i = 0; i < data.content.length; i++) {
	                			var _data = data.content[i];

	                			opt += '<option value="'+_data.kode_menu+'">'+_data.nama+'</option>';
	                		}

	                		$('#action').find('select.menu').removeAttr('disabled');
	                		$('#action').find('input.jumlah').removeAttr('disabled');

	                		$('#action').find('select.menu').html( opt );

	                		$.map( $('#action .menu'), function(select) {
	                			var val = $(select).attr('data-val');

		                		$(select).find('option[value="'+val+'"]').attr('selected', 'selected');
	                		});

	                		$('#action .menu').select2({placeholder: 'Pilih Menu'});
	                	}
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		} else {
			$('#action').find('select.menu').val(null).trigger('change');
			$('#action').find('input.jumlah').val('');

			$('#action').find('select.menu').attr('disabled', 'disabled');
	        $('#action').find('input.jumlah').attr('disabled', 'disabled');
		}
	}, // end - getMenu

	save: function () {
		var div_action = $('#action');

		var err = 0;
		$.map( $(div_action).find('[data-required=1]'), function(ipt) {
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data menu gagal ?', function (result) {
				if ( result ) {
					var list_menu = $.map( $(div_action).find('table tbody tr'), function(tr) {
						var _list_menu = {
							'menu_kode': $(tr).find('.menu').select2('val'),
							'jumlah': numeral.unformat( $(tr).find('.jumlah').val() )
						};

						return _list_menu;
					});

					var data = {
						'tanggal': dateSQL($(div_action).find('#tglInput').data('DateTimePicker').date()),
						'branch_kode': $(div_action).find('.branch').select2('val'),
						'list_menu': list_menu
					};

					$.ajax({
			            url: 'transaksi/MenuGagal/save',
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
			                		mg.loadForm( data.content.id, null );
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

	edit: function (elm) {
		var div_action = $('#action');

		var err = 0;
		$.map( $(div_action).find('[data-required=1]'), function(ipt) {
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
			bootbox.confirm('Apakah anda yakin ingin menyimpan data menu gagal ?', function (result) {
				if ( result ) {
					var list_menu = $.map( $(div_action).find('table tbody tr'), function(tr) {
						var _list_menu = {
							'menu_kode': $(tr).find('.menu').select2('val'),
							'jumlah': numeral.unformat( $(tr).find('.jumlah').val() )
						};

						return _list_menu;
					});

					var data = {
						'id': $(elm).attr('data-id'),
						'tanggal': dateSQL($(div_action).find('#tglInput').data('DateTimePicker').date()),
						'branch_kode': $(div_action).find('.branch').select2('val'),
						'list_menu': list_menu
					};

					$.ajax({
			            url: 'transaksi/MenuGagal/edit',
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
			                		mg.loadForm( data.content.id, null );
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

	delete: function (elm) {
		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function (result) {
			if ( result ) {
				$.ajax({
		            url: 'transaksi/MenuGagal/delete',
		            data: {
		                'id': $(elm).attr('data-id')
		            },
		            type: 'POST',
		            dataType: 'JSON',
		            beforeSend: function() { showLoading(); },
		            success: function(data) {
		                hideLoading();
		                if ( data.status == 1 ) {
		                	bootbox.alert(data.message, function() {
		                		mg.loadForm( null, null );

		                		mg.getLists();
		                	});
		                } else {
		                    bootbox.alert(data.message);
		                }
		            }
		        });
			}
		});
	}, // end - delete

	exportPdf: function(elm) {
		var div_active = $('div.div-tab.active');
		var err = 0

		$.map( $(div_active).find('[data-required=1]'), function(ipt) {
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
				'date': dateSQL($(div_active).find('#tglInput').data('DateTimePicker').date()),
	            'branch_kode': $(div_active).find('.branch').select2('val')
			};

			$.ajax({
	            url: 'transaksi/MenuGagal/excryptParamsExportPdf',
	            data: {
	                'params': params
	            },
	            type: 'POST',
	            dataType: 'JSON',
	            beforeSend: function() { showLoading(); },
	            success: function(data) {
	                hideLoading();
	                if ( data.status == 1 ) {
	                	window.open('transaksi/MenuGagal/exportPdf/'+data.content.data, 'blank');
	                } else {
	                    bootbox.alert(data.message);
	                }
	            }
	        });
		}
	}, // end - exportPdf
};

mg.startUp();
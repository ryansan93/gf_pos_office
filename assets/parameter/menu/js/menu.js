var formData = new FormData();

var menu = {
	start_up: function () {
	}, // end - start_up

	modalAddForm: function () {
        $.get('parameter/Menu/modalAddForm',{
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

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

		        $(this).find('textarea').addClass('uppercase');

		        $(this).find('.jenis').select2({placeholder: 'Pilih Jenis'});
		        $(this).find('.kategori').select2({placeholder: 'Pilih Kategori'});
		        $(this).find('.branch').select2({placeholder: 'Pilih Branch'});

		        $(this).removeAttr('tabindex');
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

        $.get('parameter/Menu/modalEditForm',{
        	'kode': $(tr).data('kode')
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

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

				$(this).find('textarea').addClass('uppercase');

				$(this).find('.jenis').select2({placeholder: 'Pilih Jenis'});
				$(this).find('.kategori').select2({placeholder: 'Pilih Kategori'});
		        $(this).find('.branch').select2({placeholder: 'Pilih Branch'});

		        $(this).removeAttr('tabindex');
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

			var nama = $(div).find('.nama').val().toUpperCase();
			var deskripsi = $(div).find('.deskripsi').val();
			var jenis = $(div).find('.jenis').select2('val');
			var kategori = $(div).find('.kategori').select2('val');
			var branch = $(div).find('.branch').select2('val');
			var additional = $(div).find('input[type=radio]:checked').val();
			var ppn = 0;
			if ( $(div).find('input.ppn').is(':checked') ) {
				ppn = 1;
			}
			var service_charge = 0;
			if ( $(div).find('input.service_charge').is(':checked') ) {
				service_charge = 1;
			}
			
			var list_jenis_pesanan = $.map( $(div).find('.tbl_jenis_pesanan tbody tr.data'), function (tr) {
				var _data = {
					'jenis_pesanan': $(tr).find('td.kode').attr('data-val'),
					'harga': numeral.unformat( $(tr).find('input').val() )
				};

				return _data;
			});

			bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
				if ( result ) {
					var data = {
						'nama': nama,
						'deskripsi': !empty(deskripsi) ? deskripsi.toUpperCase() : deskripsi,
						'jenis': jenis,
						'kategori': kategori,
						'branch': branch,
						'additional': additional,
						'ppn': ppn,
						'service_charge': service_charge,
						'list_jenis_pesanan': list_jenis_pesanan
					};

					var __file = $(div).find('input:file').get(0).files[0];

					formData.append('file', __file);
					formData.append('data', JSON.stringify(data));

			        $.ajax({
			            url: 'parameter/Menu/save',
			            data: formData,
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
			                    bootbox.alert(data.message, function() {
			                    	menu.modalAddForm();
			                    });
			                }
			            },
						contentType : false,
						processData : false,
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

			var kode = $(elm).data('kode');
			var nama = $(div).find('.nama').val().toUpperCase();
			var deskripsi = $(div).find('.deskripsi').val();
			var jenis = $(div).find('.jenis').val();
			var kategori = $(div).find('.kategori').val();
			var additional = $(div).find('input[type=radio]:checked').val();
			var ppn = 0;
			if ( $(div).find('input.ppn').is(':checked') ) {
				ppn = 1;
			}
			var service_charge = 0;
			if ( $(div).find('input.service_charge').is(':checked') ) {
				service_charge = 1;
			}

			var list_jenis_pesanan = $.map( $(div).find('.tbl_jenis_pesanan tbody tr.data'), function (tr) {
				var _data = {
					'jenis_pesanan': $(tr).find('td.kode').attr('data-val'),
					'harga': numeral.unformat( $(tr).find('input').val() )
				};

				return _data;
			});

			bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
				if ( result ) {
					var data = {
						'kode': kode,
						'nama': nama,
						'deskripsi': deskripsi,
						'jenis': jenis,
						'kategori': kategori,
						'additional': additional,
						'ppn': ppn,
						'service_charge': service_charge,
						'list_jenis_pesanan': list_jenis_pesanan,
						'filename_old': $(div).find('input:file').attr('data-filename'),
						'pathname_old': $(div).find('input:file').attr('data-pathname')
					};

					var __file = $(div).find('input:file').get(0).files[0];

					formData.append('file', __file);
					formData.append('data', JSON.stringify(data));

			        $.ajax({
			            url: 'parameter/Menu/edit',
			            data: formData,
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
			                    bootbox.alert(data.message, function() {
			                    	menu.modalEditForm();
			                    });
			                }
			            },
						contentType : false,
						processData : false,
			        });
				}
			});
		}
	}, // end - edit

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode = $(tr).data('kode');

		        $.ajax({
		            url: 'parameter/Menu/delete',
		            data: {
		                'kode': kode
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

	showNameFile: function(elm, isLable = 1) {
		var _label = $(elm).closest('label');
		var _spanfile = _label.next('span');
		var _allowtypes = $(elm).data('allowtypes').split('|');
		var _type = $(elm).get(0).files[0]['name'].split('.').pop();
		var _namafile = $(elm).val();
		_namafile = _namafile.substring(_namafile.lastIndexOf("\\") + 1, _namafile.length);
		var temp_url = URL.createObjectURL($(elm).get(0).files[0]);
	
		if (in_array(_type, _allowtypes)) {
		  var _nameHtml = '<u>' + _namafile + '</u> ';

		  if (isLable == 1) {
			if (_spanfile.length) {
			  _spanfile.html(_nameHtml);
			} else {
			  if ( $(_label).next('a').length > 0 ) {
				$(_label).next('a').remove();
			  }
			  $('<a href='+temp_url+' target="_blank">' + _nameHtml + '</a>').insertAfter(_label);
			}
		  }else if (isLable == 0) {
			$(elm).closest('label').attr('title', _namafile);
		  }
		  $(elm).attr('data-filename', _namafile);
		} else {
			$(elm).val('');
			$(elm).closest('label').attr('title', '');
			$(elm).attr('data-filename', '');
			_spanfile.html('');
			bootbox.alert('Format file tidak sesuai. Mohon attach ulang.');
		}
	}
};

menu.start_up();
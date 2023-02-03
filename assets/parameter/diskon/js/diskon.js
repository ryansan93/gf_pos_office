var nama = null;
var deskripsi = null;
var tgl_mulai = null;
var tgl_akhir = null;
var level = null;
var persen = null;
var nilai = null;
var non_member = null;
var member = null;
var min_beli = null;

var diskon = {
	start_up: function () {
	}, // end - start_up

	changeTipeDiskon: function () {
		var val = $('select.tipe_diskon').val();

		$('div.tipe_diskon').addClass('hide');
		$('div#tipe_diskon'+val).removeClass('hide');
	}, // end - changeTipeDiskon

	cekCheckbox: function (elm) {
		var div = $(elm).closest('div.contain');

		if ( $(elm).is(':checked') ) {
			$(div).find('input[type=text]').removeAttr('disabled');
			$(div).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
	            // $(this).priceFormat(Config[$(this).data('tipe')]);
	            priceFormat( $(this).val() );
	        });
		} else {
			$(div).find('input[type=text]').val( 0 );
			$(div).find('input[type=text]').attr('disabled', 'disabled');
		}
	}, // end - cekCheckbox

	removeRowTable: function (elm) {
		var tr = $(elm).closest('tr');

		$(tr).remove();
	}, // end - removeRowTable

	addDaftarJenisMenu: function (elm) {
		var div = $(elm).closest('.contain_tipe_diskon');

		var jenis_menu = $(div).find('select.jenis_menu').select2('val');
		var text_jenis_menu = $(div).find('select.jenis_menu option:selected').text();
		var menu = $(div).find('select.menu').select2('val');
		var kode_branch = $(div).find('select.menu option:selected').attr('data-branch');
		var text_menu = $(div).find('select.menu option:selected').text();
		var diskon = numeral.unformat($(div).find('input.diskon').val());
		var diskon_jenis = $(div).find('select.diskon_jenis').val();
		var text_diskon_jenis = $(div).find('select.diskon_jenis option:selected').text();
		var _text_diskon = numeral.formatDec(diskon);
		var text_diskon = (diskon_jenis == 'persen') ? _text_diskon+' '+text_diskon_jenis : text_diskon_jenis+' '+_text_diskon;

		var tr = '<tr class="data">'
			tr += '<td class="jenis_menu" data-val="'+jenis_menu+'" style="padding: 3px;">'+text_jenis_menu+'</td>';
			tr += '<td class="menu" data-val="'+menu+'" data-branch="'+kode_branch+'" style="padding: 3px;">'+text_menu+'</td>';
			tr += '<td class="diskon text-right" data-val="'+diskon+'" data-jenis="'+diskon_jenis+'" style="padding: 3px;">'+text_diskon+'</td>';
			tr += '<td style="padding: 3px;"><button type="button" class="col-xs-12 btn btn-default" onclick="diskon.removeRowTable(this)"><i class="fa fa-trash"></i></button></td>';
		tr += '</tr>'

		$(div).find('tbody').append( tr );

		$(div).find('select.jenis_menu').val('all').trigger('change');
		$(div).find('select.menu').val('all').trigger('change');
		$(div).find('.diskon').val('');
		$(div).find('.diskon_jenis').val('persen');
	}, // end - addDaftarJenisMenu

	addDaftarBeliDapat: function (elm) {
		var div_tipe_diskon = $(elm).closest('div.tipe_diskon');

		var div_daftar_beli = $(div_tipe_diskon).find('div.daftar_beli');
		var div_daftar_dapat = $(div_tipe_diskon).find('div.daftar_dapat');

		var jenis_menu_beli = $(div_daftar_beli).find('.jenis_menu').select2('val');
		var text_jenis_menu_beli = $(div_daftar_beli).find('.jenis_menu option:selected').text();
		var menu_beli = $(div_daftar_beli).find('.menu').select2('val');
		var text_menu_beli = $(div_daftar_beli).find('.menu option:selected').text();
		var jumlah_beli = numeral.unformat($(div_daftar_beli).find('.jumlah').val());
		var text_jumlah_beli = numeral.formatInt(jumlah_beli);

		var jenis_menu_dapat = $(div_daftar_dapat).find('.jenis_menu').select2('val');
		var text_jenis_menu_dapat = $(div_daftar_dapat).find('.jenis_menu option:selected').text();
		var menu_dapat = $(div_daftar_dapat).find('.menu').select2('val');
		var text_menu_dapat = $(div_daftar_dapat).find('.menu option:selected').text();
		var jumlah_dapat = numeral.unformat($(div_daftar_dapat).find('.jumlah').val());
		var text_jumlah_dapat = numeral.formatInt(jumlah_dapat);
		var diskon = numeral.unformat($(div_daftar_dapat).find('.diskon').val());
		var diskon_jenis = $(div_daftar_dapat).find('.diskon_jenis').val();
		var text_diskon_jenis = $(div_daftar_dapat).find('.diskon_jenis option:selected').text();
		var _text_diskon = numeral.formatDec(diskon);
		var text_diskon = (diskon_jenis == 'persen') ? _text_diskon+' '+text_diskon_jenis : text_diskon_jenis+' '+_text_diskon;

		var tr = '<tr class="data">'
			tr += '<td style="padding: 3px;"><b>BUY</b></td>';
			tr += '<td class="jumlah_beli text-right" data-val="'+jumlah_beli+'" style="padding: 3px;">'+text_jumlah_beli+'</td>';
			tr += '<td class="produk_beli" data-jm="'+jenis_menu_beli+'" data-menu="'+menu_beli+'" style="padding: 3px;">';
			tr += '<b>></b> '+text_jenis_menu_beli+'<br><b>></b> '+text_menu_beli;
			tr += '</td>';
			tr += '<td style="padding: 3px;"><b>GET</b></td>';
			tr += '<td class="jumlah_dapat text-right" data-val="'+jumlah_dapat+'" style="padding: 3px;">'+text_jumlah_dapat+'</td>';
			tr += '<td class="produk_dapat" data-jm="'+jenis_menu_dapat+'" data-menu="'+menu_dapat+'" style="padding: 3px;">';
			tr += '<b>></b> '+text_jenis_menu_dapat+'<br><b>></b> '+text_menu_dapat;
			tr += '</td>';
			tr += '<td class="diskon text-right" data-val="'+diskon+'" data-jenis="'+diskon_jenis+'" style="padding: 3px;">'+text_diskon+'</td>';
			tr += '<td style="padding: 3px;"><button type="button" class="col-xs-12 btn btn-default" onclick="diskon.removeRowTable(this)"><i class="fa fa-trash"></i></button></td>';
		tr += '</tr>'

		$(div_tipe_diskon).find('tbody').append( tr );

		$(div_daftar_beli).find('select.jenis_menu').val('all').trigger('change');
		$(div_daftar_beli).find('select.menu').val('all').trigger('change');
		$(div_daftar_beli).find('.jumlah').val('');

		$(div_daftar_dapat).find('select.jenis_menu').val('all').trigger('change');
		$(div_daftar_dapat).find('select.menu').val('all').trigger('change');
		$(div_daftar_dapat).find('.jumlah').val('');
		$(div_daftar_dapat).find('.diskon').val('');
		$(div_daftar_dapat).find('.diskon_jenis').val('persen');
	}, // end - addDaftarBeliDapat

	filterMenu: function () {
		var _branch = $('.branch').select2('val');

		if ( _branch.length > 0 ) {
			$('.menu option').attr('disabled', 'disabled');

			for (var i = 0; i < _branch.length; i++) {
				var branch = _branch[i];

				$.map( $('div.contain_tipe_diskon'), function (div) {
					if ( $(div).find('select.jenis_menu').length > 0 ) {
						var jenis_menu = $(div).find('select.jenis_menu').select2('val');

						if ( !empty(branch) && !empty(jenis_menu) ) {
							if ( jenis_menu == 'all' ) {
								$(div).find('select.menu option[data-branch="'+branch+'"]').removeAttr('disabled');
							} else {
								$(div).find('select.menu option[data-branch="'+branch+'"][data-jm="'+jenis_menu+'"]').removeAttr('disabled');
							}
							$(div).find('select.menu option[value="all"]').removeAttr('disabled');
							$(div).find('select.menu').select2();
						}
					}
				});
			}
		} else {
			$(div).find('select.menu option').attr('disabled', 'disabled');
		}
	}, // end - filterMenu

	modalAddForm: function () {
		$('.modal').modal('hide');

        $.get('parameter/diskon/modalAddForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '90%', 'max-width': '100%'});

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

                var today = moment(new Date()).format('YYYY-MM-DD');
				$("#StartDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 00:00:00')))
		        });
		        $("#EndDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date((today+' 23:59:59')))
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

		        $("#StartTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#EndTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#StartTime").on("dp.change", function (e) {
	        		var minDate = dateTimeSQL($("#StartTime").data("DateTimePicker").date());
	            	$("#EndTime").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndTime").on("dp.change", function (e) {
	        		var maxDate = dateTimeSQL($("#EndTime").data("DateTimePicker").date());
	        		if ( maxDate >= (today+' 00:00:00') ) {
	            		$("#StartTime").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
	        		}
		        });

		        $('.modal').find('select.jenis_kartu').select2();
		        $('.modal').find('select.branch').select2();
		        $('.modal').find('select.branch').on('select2:select', function (e) {
		        	diskon.filterMenu();
				});
		        $('.modal').find('select.jenis_menu').select2();
		        $('.modal').find('select.jenis_menu').on('select2:select', function (e) {
		        	diskon.filterMenu();
				});
		        $('.modal').find('select.menu').select2();

		        $(this).removeAttr('tabindex');
            });
        },'html');
	}, // end - modalAddForm

	modalEditForm: function (elm) {
		var tr = $(elm).closest('tr');

		$('.modal').modal('hide');

        $.get('parameter/diskon/modalEditForm',{
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
                $(this).find('.modal-dialog').css({'width': '90%', 'max-width': '100%'});

                $(this).find('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
					// $(this).priceFormat(Config[$(this).data('tipe')]);
					priceFormat( $(this) );
				});

				$("#StartDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#StartDate input").data('tgl')+' 00:00:00')))
		        });
		        $("#EndDate").datetimepicker({
		            locale: 'id',
		            format: 'DD MMM Y',
		            minDate: moment(new Date(($("#EndDate input").data('tgl')+' 23:59:59')))
		        });
		        $("#StartDate").on("dp.change", function (e) {
	        		var minDate = dateSQL($("#StartDate").data("DateTimePicker").date())+' 00:00:00';
	            	$("#EndDate").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndDate").on("dp.change", function (e) {
	        		var maxDate = dateSQL($("#EndDate").data("DateTimePicker").date())+' 23:59:59';
	            	$("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
		        });

		        $("#StartTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#EndTime").datetimepicker({
		            locale: 'id',
		            format: 'LT'
		        });
		        $("#StartTime").on("dp.change", function (e) {
	        		var minDate = dateTimeSQL($("#StartTime").data("DateTimePicker").date());
	            	$("#EndTime").data("DateTimePicker").minDate(moment(new Date(minDate)));
		        });
		        $("#EndTime").on("dp.change", function (e) {
	        		var maxDate = dateTimeSQL($("#EndTime").data("DateTimePicker").date());
	            	$("#StartTime").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
		        });

		        $('.jenis_kartu').select2();
		        $('.menu').select2();
		        $('.branch').select2();

		        $(this).removeAttr('tabindex');

				$(this).find('#StartDate').data('DateTimePicker').date(moment(new Date($("#StartDate input").data('tgl'))));
				$(this).find('#EndDate').data('DateTimePicker').date(moment(new Date($("#EndDate input").data('tgl'))));

				var d = new Date();
			    var month = '' + (d.getMonth() + 1);
			    var day = '' + d.getDate();
			    var year = d.getFullYear();

				$(this).find('#StartTime').data('DateTimePicker').date(moment(new Date(year+'-'+month+'-'+day+' '+$("#StartTime input").attr('data-jam'))));
				$(this).find('#EndTime').data('DateTimePicker').date(moment(new Date(year+'-'+month+'-'+day+' '+$("#EndTime input").attr('data-jam'))));
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
			var _non_member = 0;
			var _member = 0;
			if( $(div).find('.non_member').is(':checked') ) {
				_non_member = 1;
			}

			if( $(div).find('.member').is(':checked') ) {
				_member = 1;
			}

			if ( _non_member == 0 && _member == 0 ) {
				bootbox.alert('Harap pilih member atau non member untuk diskon.');
			} else {
				var branch = $(div).find('.branch').select2('val');
				var nama = $(div).find('.nama').val().toUpperCase();
				var deskripsi = $(div).find('.deskripsi').val().toUpperCase();
				var tipe_diskon = $(div).find('select.tipe_diskon').val();
				var non_member = _non_member;
				var member = _member;
				var min_beli = numeral.unformat($(div).find('.min_beli').val());
				var status_ppn = 0;
				if ( $(div).find('.status_ppn').is(':checked') ) {
					status_ppn = 1;
				}
				var ppn = numeral.unformat($(div).find('.ppn').val());
				var status_service_charge = 0;
				if ( $(div).find('.status_service_charge').is(':checked') ) {
					status_service_charge = 1;
				}
				var service_charge = numeral.unformat($(div).find('.service_charge').val());
				var tgl_mulai = dateSQL($(div).find('#StartDate').data('DateTimePicker').date());
				var tgl_akhir = dateSQL($(div).find('#EndDate').data('DateTimePicker').date());
				var jam_mulai = dateTimeSQL($(div).find('#StartTime').data('DateTimePicker').date());
				var jam_akhir = dateTimeSQL($(div).find('#EndTime').data('DateTimePicker').date());

				var jenis_kartu = $(div).find('.jenis_kartu').select2('val');

				var diskon = numeral.unformat($('div#tipe_diskon1').find('.diskon').val());
				var diskon_jenis = $('div#tipe_diskon1').find('.diskon_jenis').val();

				var diskon_menu = $.map( $('div#tipe_diskon2').find('tbody tr.data'), function (tr) {
					var _data = {
						'jenis_menu_id': $(tr).find('td.jenis_menu').attr('data-val'),
						'branch_kode': $(tr).find('td.menu').attr('data-branch'),
						'menu_kode': $(tr).find('td.menu').attr('data-val'),
						'diskon': $(tr).find('td.diskon').attr('data-val'),
						'diskon_jenis': $(tr).find('td.diskon').attr('data-jenis')
					};

					return _data;
				});

				var diskon_beli_dapat = $.map( $('div#tipe_diskon3').find('tbody tr.data'), function (tr) {
					var _data = {
						'jenis_menu_id_beli': $(tr).find('td.produk_beli').attr('data-jm'),
						'menu_kode_beli': $(tr).find('td.produk_beli').attr('data-menu'),
						'jumlah_beli': $(tr).find('td.jumlah_beli').attr('data-val'),
						'jenis_menu_id_dapat': $(tr).find('td.produk_dapat').attr('data-jm'),
						'menu_kode_dapat': $(tr).find('td.produk_dapat').attr('data-menu'),
						'jumlah_dapat': $(tr).find('td.jumlah_dapat').attr('data-val'),
						'diskon_dapat': $(tr).find('td.diskon').attr('data-val'),
						'diskon_jenis_dapat': $(tr).find('td.diskon').attr('data-jenis')
					};

					return _data;
				});

				bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
					if ( result ) {
						var data = {
							'branch': branch,
							'nama': nama,
							'deskripsi': deskripsi,
							'tipe_diskon': tipe_diskon,
							'non_member': non_member,
							'member': member,
							'min_beli': min_beli,
							'status_ppn': status_ppn,
							'ppn': ppn,
							'status_service_charge': status_service_charge,
							'service_charge': service_charge,
							'tgl_mulai': tgl_mulai,
							'tgl_akhir': tgl_akhir,
							'jam_mulai': jam_mulai,
							'jam_akhir': jam_akhir,
							'jenis_kartu': jenis_kartu,
							'diskon': diskon,
							'diskon_jenis': diskon_jenis,
							'diskon_menu': diskon_menu,
							'diskon_beli_dapat': diskon_beli_dapat
						};

				        $.ajax({
				            url: 'parameter/Diskon/save',
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
			var _non_member = 0;
			var _member = 0;
			if( $(div).find('.non_member').is(':checked') ) {
				_non_member = 1;
			}

			if( $(div).find('.member').is(':checked') ) {
				_member = 1;
			}

			if ( _non_member == 0 && _member == 0 ) {
				bootbox.alert('Harap pilih member atau non member untuk diskon.');
			} else {
				var kode = $(elm).data('kode');
				var branch = $(div).find('.branch').select2('val');
				var nama = $(div).find('.nama').val().toUpperCase();
				var deskripsi = $(div).find('.deskripsi').val().toUpperCase();
				var tgl_mulai = dateSQL($(div).find('#StartDate').data('DateTimePicker').date());
				var tgl_akhir = dateSQL($(div).find('#EndDate').data('DateTimePicker').date());
				var jam_mulai = dateTimeSQL($(div).find('#StartTime').data('DateTimePicker').date());
				var jam_akhir = dateTimeSQL($(div).find('#EndTime').data('DateTimePicker').date());
				var persen = numeral.unformat($(div).find('.persen').val());
				var nilai = numeral.unformat($(div).find('.nilai').val());
				var non_member = _non_member;
				var member = _member;
				var min_beli = numeral.unformat($(div).find('.min_beli').val());
				var status_ppn = 0;
				if ( $(div).find('.status_ppn').is(':checked') ) {
					status_ppn = 1;
				}
				var ppn = numeral.unformat($(div).find('.ppn').val());
				var status_service_charge = 0;
				if ( $(div).find('.status_service_charge').is(':checked') ) {
					status_service_charge = 1;
				}
				var service_charge = numeral.unformat($(div).find('.service_charge').val());

				var jenis_kartu = $(div).find('.jenis_kartu').select2('val');

				var menu = $.map( $(div).find('tbody tr'), function (tr) {
					var _menu = $(tr).find('.menu').select2('val');
					if ( !empty(_menu) ) {
						var _data = {
							'menu': _menu,
							'jumlah_min': numeral.unformat( $(tr).find('.jumlah_min').val() )
						};

						return _data;
					}
				});

				bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
					if ( result ) {
						var data = {
							'kode': kode,
							'branch': branch,
							'nama': nama,
							'deskripsi': deskripsi,
							'tgl_mulai': tgl_mulai,
							'tgl_akhir': tgl_akhir,
							'jam_mulai': jam_mulai,
							'jam_akhir': jam_akhir,
							'persen': persen,
							'nilai': nilai,
							'non_member': non_member,
							'member': member,
							'min_beli': min_beli,
							'status_ppn': status_ppn,
							'ppn': ppn,
							'status_service_charge': status_service_charge,
							'service_charge': service_charge,
							'jenis_kartu': jenis_kartu,
							'menu': menu
						};

				        $.ajax({
				            url: 'parameter/Diskon/edit',
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
		}
	}, // end - edit

	delete: function(elm) {
		var tr = $(elm).closest('tr');

		bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
			if ( result ) {
				kode = $(tr).data('kode');

		        $.ajax({
		            url: 'parameter/Diskon/delete',
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
};

diskon.start_up();
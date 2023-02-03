var pp = {
    start_up: function () {
        pp.setting_up();
    }, // end - start_up

    setting_up: function() {
        var today = moment(new Date()).format('YYYY-MM-DD');
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
            if ( maxDate >= (today+' 00:00:00') ) {
                $("#StartDate").data("DateTimePicker").maxDate(moment(new Date(maxDate)));
            }
        });

        $("#TglBayar").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y',
            minDate: moment(new Date((today+' 00:00:00'))).subtract(3, 'days')
        });
        if ( !empty($("#TglBayar").find('input').data('tgl')) ) {
            var tgl = $("#TglBayar").find('input').data('tgl');
            $("#TglBayar").data('DateTimePicker').date( moment(new Date((tgl+' 00:00:00'))) );
        }

        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this).val() );
        });

        $('.check_all').change(function() {
            var data_target = $(this).data('target');

            if ( this.checked ) {
                $.map( $('.check[target='+data_target+']'), function(checkbox) {
                    $(checkbox).prop( 'checked', true );
                });
            } else {
                $.map( $('.check[target='+data_target+']'), function(checkbox) {
                    $(checkbox).prop( 'checked', false );
                });
            }

            pp.hitTotTagihan( $(this) );
        });

        $('.check').change(function() {
            var target = $(this).attr('target');

            var length = $('.check[target='+target+']').length;
            var length_checked = $('.check[target='+target+']:checked').length;

            if ( length == length_checked ) {
                $('.check_all').prop( 'checked', true );
            } else {
                $('.check_all').prop( 'checked', false );
            }

            pp.hitTotTagihan( $(this) );
        });
    }, // end - setting_up

    showNameFile : function(elm, isLable = 1) {
        var _label = $(elm).closest('label');
        var _a = _label.prev('a[name=dokumen]');
        _a.removeClass('hide');
        // var _allowtypes = $(elm).data('allowtypes').split('|');
        var _dataName = $(elm).data('name');
        var _allowtypes = ['doc', 'DOC', 'docx', 'DOCX', 'jpg', 'JPG', 'jpeg', 'JPEG', 'pdf', 'PDF', 'png', 'PNG'];
        var _type = $(elm).get(0).files[0]['name'].split('.').pop();
        var _namafile = $(elm).val();
        var _temp_url = URL.createObjectURL($(elm).get(0).files[0]);
        _namafile = _namafile.substring(_namafile.lastIndexOf("\\") + 1, _namafile.length);

        if (in_array(_type, _allowtypes)) {
            if (isLable == 1) {
                if (_a.length) {
                    _a.attr('title', _namafile);
                    _a.attr('href', _temp_url);
                    if ( _dataName == 'name' ) {
                        $(_a).text( _namafile );  
                    }
                }
            } else if (isLable == 0) {
                $(elm).closest('label').attr('title', _namafile);
            }
            $(elm).attr('data-filename', _namafile);
        } else {
            $(elm).val('');
            $(elm).closest('label').attr('title', '');
            $(elm).attr('data-filename', '');
            _a.addClass('hide');
            bootbox.alert('Format file tidak sesuai. Mohon attach ulang.');
        }
    }, // end - showNameFile

    hitTotTagihan: function(elm) {
        var tr = $(elm).closest('tr');
        var table = $(tr).closest('table');

        var total = 0;
        $.map( $(table).find('tr.data input[type=checkbox]'), function(ipt) {
            if ( ipt.checked == true ) {
                var _tr = $(ipt).closest('tr');

                var _grand_total = numeral.unformat($(_tr).find('td.grand_total').text());

                total += _grand_total;
            }
        });

        $('.tot_tagihan').val( numeral.formatDec(total) );
    }, // end - hitTotTagihan

    changeTabActive: function(elm) {
        var vhref = $(elm).data('href');
        var edit = $(elm).data('edit');
        // change tab-menu
        $('.nav-tabs').find('a').removeClass('active');
        $('.nav-tabs').find('a').removeClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('show');
        $('.nav-tabs').find('li a[data-tab='+vhref+']').addClass('active');

        // change tab-content
        $('.tab-pane').removeClass('show');
        $('.tab-pane').removeClass('active');
        $('div#'+vhref).addClass('show');
        $('div#'+vhref).addClass('active');

        if ( vhref == 'action' ) {
            var v_id = $(elm).attr('data-id');

            pp.loadForm(v_id, edit);
        };
    }, // end - changeTabActive

    loadForm: function(v_id = null, resubmit = null) {
        var dcontent = $('div#action');

        $.ajax({
            url : 'transaksi/PembayaranPiutang/loadForm',
            data : {
                'id' :  v_id,
                'resubmit' : resubmit
            },
            type : 'GET',
            dataType : 'HTML',
            beforeSend : function(){ showLoading(); },
            success : function(html){
                hideLoading();
                $(dcontent).html(html);
                pp.setting_up();
            },
        });
    }, // end - loadForm

    getLists: function() {
        var dcontent = $('div#riwayat');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            var tbody = $(dcontent).find('table.tbl_riwayat tbody');

            var params = {
                'start_date': dateSQL( $(dcontent).find('#StartDate').data('DateTimePicker').date() ),
                'end_date': dateSQL( $(dcontent).find('#EndDate').data('DateTimePicker').date() )
            };

            $.ajax({
                url : 'transaksi/PembayaranPiutang/getLists',
                data : {
                    'params' : params
                },
                type : 'GET',
                dataType : 'HTML',
                beforeSend : function(){ showLoading(); },
                success : function(html){
                    hideLoading();
                    $(tbody).html(html);
                },
            });
        }
    }, // end - getLists

    save: function() {
        var dcontent = $('div#action');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#a94442'});
                } else {
                    $(ipt).parent().addClass('has-error');
                }
                err++;
            } else {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#000000'});
                } else {
                    $(ipt).parent().removeClass('has-error');
                }
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            bootbox.confirm('Apakah anda yakin ingin menyimpan data ?', function(result) {
                if ( result ) {
                    var tot_tagihan = numeral.unformat( $(dcontent).find('.tot_tagihan').val() );
                    var tot_bayar = numeral.unformat( $(dcontent).find('.tot_bayar').val() );

                    var bayar = 0;
                    if ( tot_bayar < tot_tagihan ) {
                        bootbox.confirm('Jumlah bayar kurang dari tagihan.<br>Apakah anda tetap yakin ingin menyimpan data?', function(result) {
                            if ( result ) {
                                pp.exec_save( dcontent );
                            }
                        });
                    } else {
                        pp.exec_save( dcontent );
                    }
                }
            });
        }
    }, // end - save

    exec_save: function(dcontent)  {
        var detail = $.map( $(dcontent).find('tr.data input[type=checkbox]'), function(ipt) {
            var _tr = $(ipt).closest('tr');
            if ( ipt.checked == true ) {
                var _detail = {
                    'kode_faktur': $(_tr).find('.kode_faktur').text().trim(),
                    'jml_tagihan': numeral.unformat($(_tr).find('.grand_total').text().trim())
                };

                return _detail;
            }
        });

        var data = {
            'tgl_bayar': dateSQL( $(dcontent).find('#TglBayar').data('DateTimePicker').date() ),
            'tot_tagihan': numeral.unformat( $(dcontent).find('.tot_tagihan').val() ),
            'tot_bayar': numeral.unformat( $(dcontent).find('.tot_bayar').val() ),
            'jenis_pembayaran': $(dcontent).find('.jenis_pembayaran option:selected').data('tipe'),
            'kode_kartu': $(dcontent).find('.jenis_pembayaran').val(),
            'detail': detail
        };

        var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

        var formData = new FormData();

        formData.append('data', JSON.stringify(data));
        formData.append('file', file_tmp);

        $.ajax({
            url: 'transaksi/PembayaranPiutang/save',
            dataType: 'json',
            type: 'post',
            async:false,
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                showLoading();
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert(data.message, function() {
                        pp.loadForm(data.content.id);
                        var dcontent_riwayat = $('div#riwayat');
                        var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                        var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                        if ( !empty(start_date) && !empty(end_date) ) {
                            pp.getLists();
                        }
                    });
                } else {
                    bootbox.alert(data.message);
                };
            },
        });
    }, // end - exec_save

    edit: function(elm) {
        var dcontent = $('div#action');

        var err = 0;
        $.map( $(dcontent).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#a94442'});
                } else {
                    $(ipt).parent().addClass('has-error');
                }
                err++;
            } else {
                if ( $(ipt).hasClass('file_lampiran') ) {
                    var label = $(ipt).closest('label');
                    $(label).find('i').css({'color': '#000000'});
                } else {
                    $(ipt).parent().removeClass('has-error');
                }
            }
        });

        if ( err > 0 ) {
            bootbox.alert('Harap lengkapi data terlebih dahulu.');
        } else {
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data ?', function(result) {
                if ( result ) {
                    var detail = $.map( $(dcontent).find('table tbody tr'), function(_tr) {
                        var _detail = {
                            'item_kode': $(_tr).find('.item').val(),
                            'jumlah': numeral.unformat( $(_tr).find('.jumlah').val() ),
                            'harga': numeral.unformat( $(_tr).find('.harga').val() ),
                            'total': numeral.unformat( $(_tr).find('.total').val() ),
                        };

                        return _detail;
                    });

                    var data = {
                        'kode_beli': $(elm).data('id'),
                        'supplier': $(dcontent).find('.supplier').val().toUpperCase(),
                        'tgl_beli': dateSQL( $(dcontent).find('#TglBayar').data('DateTimePicker').date() ),
                        'total': numeral.unformat( $(dcontent).find('tfoot .grand_total b').text() ),
                        'detail': detail
                    };

                    var file_tmp = $(dcontent).find('.file_lampiran').get(0).files[0];

                    var formData = new FormData();

                    formData.append('data', JSON.stringify(data));
                    formData.append('file', file_tmp);

                    $.ajax({
                        url: 'transaksi/PembayaranPiutang/edit',
                        dataType: 'json',
                        type: 'post',
                        async:false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        beforeSend: function() {
                            showLoading();
                        },
                        success: function(data) {
                            hideLoading();
                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    pp.loadForm(data.content.id);
                                    var dcontent_riwayat = $('div#riwayat');
                                    var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                                    var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                                    if ( !empty(start_date) && !empty(end_date) ) {
                                        pp.getLists();
                                    }
                                });
                            } else {
                                bootbox.alert(data.message);
                            };
                        },
                    });
                }
            });
        }
    }, // end - edit

    delete: function(elm) {
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data ?', function(result) {
            if ( result ) {
                var kode = $(elm).data('id');

                $.ajax({
                    url: 'transaksi/PembayaranPiutang/delete',
                    dataType: 'json',
                    type: 'post',
                    data: {
                        'kode': kode
                    },
                    beforeSend: function() {
                        showLoading();
                    },
                    success: function(data) {
                        hideLoading();
                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                pp.loadForm();
                                var dcontent_riwayat = $('div#riwayat');
                                var start_date = $(dcontent_riwayat).find('#StartDate input').val();
                                var end_date = $(dcontent_riwayat).find('#EndDate input').val();
                                if ( !empty(start_date) && !empty(end_date) ) {
                                    pp.getLists();
                                }
                            });
                        } else {
                            bootbox.alert(data.message);
                        };
                    },
                });
            }
        });
    }, // end - delete
};

pp.start_up();
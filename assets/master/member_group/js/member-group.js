var mg = {
	startUp: function () {
	}, // end - startUp

	addForm: function () {
        $('.modal').modal('hide');

        $.get('master/MemberGroup/addForm',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    // $(this).priceFormat(Config[$(this).data('tipe')]);
                    priceFormat( $(this) );
                });
            });
        },'html');
    }, // end - addForm

    viewForm: function (elm) {
        $('.modal').modal('hide');

        $.get('master/MemberGroup/viewForm',{
            'kode': $(elm).data('kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    // $(this).priceFormat(Config[$(this).data('tipe')]);
                    priceFormat( $(this) );
                });
            });
        },'html');
    }, // end - viewForm

    editForm: function (elm) {
        var modal = $(elm).closest('.modal');

        $(modal).find('input, textarea').removeAttr('disabled');
        $(modal).find('.btn_view').addClass('hide');
        $(modal).find('.btn_edit').removeClass('hide');
    }, // end - editForm

    batalEdit: function(elm) {
        mg.viewForm($(elm));
    }, // end - batalEdit

    save: function(elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin menyimpan data member group ?', function( result ) {
                if ( result ) {
                    var params = {
                        'nama': $(modal).find('.nama').val()
                    };

                    $.ajax({
                        url: 'master/MemberGroup/save',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                	$('.modal').modal('hide');

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
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin meng-ubah data member group ?', function( result ) {
                if ( result ) {
                    var params = {
                        'kode': $(elm).data('kode'),
                        'nama': $(modal).find('.nama').val()
                    };

                    $.ajax({
                        url: 'master/MemberGroup/edit',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    $('.modal').modal('hide');

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
        bootbox.confirm('Apakah anda yakin ingin meng-hapus data member group ?', function( result ) {
            if ( result ) {
                var params = {
                    'kode': $(elm).data('kode')
                };

                $.ajax({
                    url: 'master/MemberGroup/delete',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();

                        if ( data.status == 1 ) {
                            bootbox.alert(data.message, function() {
                                $('.modal').modal('hide');

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

mg.startUp();
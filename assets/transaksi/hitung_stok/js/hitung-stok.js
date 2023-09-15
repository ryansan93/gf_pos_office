var hs = {
	start_up: function () {
		hs.setting_up();
	}, // end - start_up

	setting_up: function(){
		$('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal], [data-tipe=decimal3],[data-tipe=decimal4], [data-tipe=number]').each(function(){
            // $(this).priceFormat(Config[$(this).data('tipe')]);
            priceFormat( $(this) );
        });

        $("[name=tanggal]").datetimepicker({
			locale: 'id',
            format: 'DD MMM Y',
            useCurrent: false //Important! See issue #1075
		});

		$('select.gudang, select.item').select2();
	}, // end - setting_up

	hitungStok: function() {
		var err = 0;
		$.map( $('[data-required=1]'), function(ipt) {
			if ( empty($(ipt).val()) ) {
				$(ipt).parent().addClass('has-error');
				err++;
			} else {
				$(ipt).parent().removeClass('has-error');
			}
		});

		if ( err > 0 ) {
			bootbox.alert('Harap isi periode terlebih dahulu.');
		} else {
			bootbox.confirm('Apakah anda yakin ingin proses perhitungan stok ?', function(result) {
				if ( result ) {
					var params = {
						tanggal : dateSQL( $('[name=tanggal]').data('DateTimePicker').date() ),
						gudang : $('select.gudang').select2().val(),
						item : $('select.item').select2().val()
					};

					$.ajax({
						url: 'transaksi/HitungStok/hitungStok',
						data: {
							'params': params
						},
						type: 'POST',
						dataType: 'JSON',
						beforeSend: function() {
							showLoading('Proses hitung stok . . .');
						},
						success: function(data) {
							hideLoading();
							if ( data.status == 1 ) {
								bootbox.alert(data.message, function() {
									location.reload();
								});
							} else {
								bootbox.alert(data.message);
							};
						},
					});
				}
			});
		}
	}, // end - hitung_stok
};

hs.start_up();
var home = {
	startUp: function () {
		home.getDataPenjualan();
	},  // end - startUp

	getDataPenjualan: function () {
		$.ajax({
            url: 'home/Home/getDataPenjualan',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                	home.chart(data.content.list_hari, data.content.list_outlet);
                }
            }
        });
	}, // end - getDataPenjualan

	chart: function (list_hari, list_outlet) {
		var dset = [];
		for (var i = 0; i < list_outlet.length; i++) {
			// var r = Math.floor(Math.random() * 255);
	  //       var g = Math.floor(Math.random() * 255);
	  //       var b = Math.floor(Math.random() * 255);

	  		var r = list_outlet[i]['warna']['r'];
	        var g = list_outlet[i]['warna']['g'];
	        var b = list_outlet[i]['warna']['b'];

			dset[i] = {
				fill: false,
				label: list_outlet[i]['nama'],
				lineTension: 0,
				backgroundColor: "rgba("+r+","+g+","+b+",1.0)",
				borderColor: "rgba("+r+","+g+","+b+",0.5)",
				data: list_outlet[i]['list_total']
			}
		}

		new Chart("myChart", {
			type: "line",
			data: {
				labels: list_hari,
				datasets: dset
			},
			options: {
				legend: {display: 'coba'},
				// scales: {
				// 	yAxes: [{ticks: {min: 6, max:16}}],
				// }
			}
		});
	}, // end - chart

	tesChart: function () {
		var xValues = [1, 2, 3, 4, 5, 6, 7];
		var yValues = [0, 1000000, 2000000, 3000000, 4000000, 5000000, 6000000];

		new Chart("myChart", {
			type: "line",
			data: {
				labels: xValues,
				datasets: [
				{
					fill: false,
					label: 'OUTLET SUMATRA',
					lineTension: 0,
					backgroundColor: "rgba(0,0,255,1.0)",
					borderColor: "rgba(0,0,255,0.5)",
					data: [1500000, 2000000, 1800000, 1700000, 2300000, 3000000, 3500000]
				},
				{
					fill: false,
					label: 'OUTLET PUSAT',
					lineTension: 0,
					backgroundColor: "rgba(255,0,0,1.0)",
					borderColor: "rgba(255,0,0,0.5)",
					data: yValues
				}
				]
			},
			options: {
				legend: {display: 'coba'},
				// scales: {
				// 	yAxes: [{ticks: {min: 6, max:16}}],
				// }
			}
		});
	}, // end - tesChart
};

home.startUp();
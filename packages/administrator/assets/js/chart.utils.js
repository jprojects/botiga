
var MONTHS = ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Augost', 'Setembre', 'Octubre', 'Novembre', 'Decembre'];

//canvas1
var config1 = {
	type: 'line',
	data: {
		labels: MONTHS,
		datasets: [{
			label: 'Vendes',
			backgroundColor: '#ffbb00',
			borderColor: '#ffbb00',
			data: [2,4,7,6,9,2,5,8,12,11,6,12],
			fill: false,
		}]
	},
	options: {
		responsive: true,
		title: {
			display: true,
			text: 'Vendes anuals'
		},
		tooltips: {
			mode: 'index',
			intersect: false,
		},
		hover: {
			mode: 'nearest',
			intersect: true
		},
		scales: {
			xAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Mes'
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel: {
					display: true,
					labelString: 'Valor'
				}
			}]
		}
	}
};

//canvas2
var config2 = {
	type: 'pie',
	data: {
		datasets: [{
			label: 'Sistemes de pagament',
			data: [
				5,
				6,
				3,
			],
			backgroundColor: [
				'#ffbb00',
				'#36a2eb',
				'#ff6384',
			]
		}],
		labels: [
			'Transferència',
			'Paypal',
			'Targeta bancària'
		]
	},
	options: {
		responsive: true
	}
};

window.onload = function() {
	var chart1 = document.getElementById('canvas1').getContext('2d');
	window.myLine = new Chart(chart1, config1);
	
	var chart2 = document.getElementById('canvas2').getContext('2d');
	window.myPie = new Chart(chart2, config2);
};


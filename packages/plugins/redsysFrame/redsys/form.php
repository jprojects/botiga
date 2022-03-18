<html>

<head>
    <script src="<?= $data->url; ?>"></script>
</head>

<body>

	<div class="container-fluid margintop">

		<div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4">
			<img src="<?= $data->logo; ?>" alt="..." class="img-fluid">
			<p><h5><?= $data->msg; ?></h5></p>
			<div id="card-form"></div>
			<form name="datos">
				<input type="hidden" id="token"></input>
				<input type="hidden" id="errorCode"></input>
				<a href="javascript:alert(document.datos.token.value + '--' + document.datos.errorCode.value)"> ver</a>
			</form>
		</div>
	</div>

	<script>
		function merchantValidation(){
			//Insertar validaciones…
			return true;
		}
		window.addEventListener("message", function receiveMessage(event) {
			storeIdOper(event,"token", "errorCode", merchantValidation);
		});
		getInSiteForm('card-form', '', '', '', '', 'Texto botón pago', '<?= $data->fuc; ?>', '<?= $data->terminal; ?>', '<?= $data->merchantOrder; ?>', '<?= $data->language; ?>');
	</script>

</body>

</html>
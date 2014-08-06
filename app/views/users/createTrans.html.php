<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-success">
			<div class="panel-heading"><a href="/ex/dashboard">Dashboard <i class="icon-chevron-left icon-large"></i></a>&nbsp;&nbsp;
			Address: <?=$addresses['msxRedeemScript']['address']?> <?=$addresses['currencyName']?> <?=$addresses['currency']?></div>
			<div class="panel-body">
			<h3>Unable to create transaction:</h3>
			<p class="alert alert-danger"><?php
			print_r($createrawtransaction['error']['message']);
			?></p>
			</div>
			<div class="panel-footer">Try again!</div>
		</div>
	</div>
</div>

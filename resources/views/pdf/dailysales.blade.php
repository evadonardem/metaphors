<!DOCTYPE html>
<html>
<head>
	<title>Daily Sales Report</title>	
	<style type="text/css">
	@page {
		margin: 0.5in;
	}

	body {
		font-family: sans-serif;
		margin: 0.5in 0;
		text-align: justify;
		font-size: 80%;
	}

	p {
		margin: 0;
	}

	table {
		border-collapse: collapse;
	}	

	#header, #footer {
		position: fixed;
		left: 0;
		right: 0;		
		font-size: 0.9em;
	}

	#header {
		top: 0;
		border-bottom: 1px solid black;
	}

	#footer {
		bottom: 10;
		border-top: 1px solid black;
	}

	.page-number {
		text-align: center;
	}

	.page-number:before {
		content: "Page " counter(page);
	}

	hr {
		page-break-after: always;
		border: 0;
	}
	</style>
</head>
<body>
	<div id="header">
		<p style="text-align: center;"><strong>Metaphors Marketing Venture (MMV)</strong><br>
			<em>Unique, Fruit Enzyme-based, Health Soaps and Body Products for clear and healthy skin</em></p>
	</div>		

	<p style="text-align: center;"><strong>DAILY SALES REPORT</strong></p>
	<p style="text-align: center;">{{ $report['daterange'] }}</p>
	<br>
	<table border="1" width="100%">
		<tr>
			<th>Products</th>
			<th>Qty.</th>
			<th>Amount</th>
		</tr>

		<?php $totalQuantity = 0; $totalAmount = 0; ?>
		@foreach($report['dailySales'] as $rec)
			<tr>
				<th colspan="3" style="text-align: left;">{{ $rec['date'] }}</th>
			</tr>
			@if(count($rec['products'])>0)
				@foreach($rec['products'] as $product)
					<tr>
						<td>&nbsp;&nbsp;&nbsp;{{ $product['title'] }}</td>
						<td style="text-align: right;">{{ number_format($product['sales']->quantity, 0) }}</td>
						<td style="text-align: right;">{{ number_format($product['sales']->amount, 2) }}</td>

						<?php
							$totalQuantity += $product['sales']->quantity;
							$totalAmount += $product['sales']->amount;
						?>

					</tr>
				@endforeach
			@else
				<tr>
					<td colspan="3">&nbsp;&nbsp;&nbsp;<em>NO SALES</em></td>
				</tr>
			@endif
		@endforeach
		<tr>
			<th style="text-align: right;">Total:</th>
			<th style="text-align: right;">{{ number_format($totalQuantity, 0) }}</th>
			<th style="text-align: right;">{{ number_format($totalAmount, 2) }}</th>
		</tr>
	</table>

	<br>
	<p style="text-align: center;"><em>-------------------[Nothing Follows]--------------------</em></p>

	<div id="footer">
		<p><span class="page-number"></span> | MMV Generated Report ({{ Carbon\Carbon::now()->format('Y M d h:i:s A') }})</p>
	</div>
</body>
</html>
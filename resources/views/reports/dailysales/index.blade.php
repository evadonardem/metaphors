@extends('layouts.main-no-bulletin')

@section('title')
REPORTS | Daily Sales
@stop

@section('embedded_style')
@stop

@section('embedded_script')
<script type="text/javascript">	
	$(function() {
		var report = {
			start : $('#start').val(),
			end : $('#end').val(),
			init : function() {
				var ref = this;

				$('#printBtnPlaceholder').hide();
				$('#printBtn').off();

				$('.input-daterange').datepicker({
					autoclose: true,
					format: 'yyyy-mm-dd'
				});

				$('#generateReportBtn').click(function(e) {
					ref.start = $('#start').val();
					ref.end = $('#end').val();
					ref.generate();
				});	
			},
			generate : function() {
				var ref = this;				

				$('#printBtnPlaceholder').hide();

				// clear content
				$('#dailySalesWrapper').html('');

				$.getJSON("{{ url('reports/dailysales') }}/" + ref.start + '/to/' + ref.end + '/json', function(r) {					

					var dailySalesDateRange = r.daterange;
					var dailySales = r.dailySales;
					
					$('#dailySalesWrapper').append('<p class="alert alert-info">Daily Sales Report ' + dailySalesDateRange + '</p>');

					var table = $('<table class="table table-bordered table-striped"></table>');
					
					var thead = $('<thead></thead>');
					var theadRow = $('<tr></tr>');
					theadRow.append($('<th>Products</th>'));
					theadRow.append($('<th>Qty.</th>'));
					theadRow.append($('<th>Amount.</th>'));
					thead.append(theadRow);
					table.append(thead);

					var totalQuantity = 0;
					var totalAmount = 0;

					var tbody = $('<tbody></tbody>');
					for(i in dailySales) {
						var rec = dailySales[i]
						var row = $('<tr></tr>');
						row.append('<th colspan="3">'+rec.date+'</th>');
						tbody.append(row);

						var products = rec.products;
						if(!$.isEmptyObject(products)) {
							for(j in products) {
								var product = products[j];
								var row = $('<tr></tr>');
								row.append('<td>&nbsp;&nbsp;&nbsp;' + product.code + ' - ' + product.title + '</td>');
								row.append('<td class="text-right">' + $.number(product.sales.quantity,0) + '</td>');
								row.append('<td class="text-right">' + $.number(product.sales.amount, 2) + '</td>');

								tbody.append(row);

								totalQuantity += product.sales.quantity;
								totalAmount += product.sales.amount;
							}
						} else {
							var row = $('<tr></tr>');
							row.append('<td colspan="3">&nbsp;&nbsp;&nbsp;<em>NO SALES</em></td>');
							tbody.append(row);
						}						
					}
					table.append(tbody);

					var tfoot = $('<tfoot></tfoot>');
					var tfootRow = $('<tr></tr>');
					tfootRow.append($('<th class="text-right">Total: </th>'));
					tfootRow.append($('<th class="text-right">' + $.number(totalQuantity, 0) + '</th>'));
					tfootRow.append($('<th class="text-right">' + $.number(totalAmount, 2) + '</th>'));
					tfoot.append(tfootRow);
					table.append(tfoot);

					$('#dailySalesWrapper').append(table);					

					$('#printBtnPlaceholder').show('normal');
					$('#printBtn').off().click(function(e) {
						var printURL = "{{ url('reports/dailysales') }}/"+ref.start+"/to/"+ref.end+"/print";
						var win = window.open(printURL, '_blank', 'menubar=no, resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no');
					});				
				});
			}
		};		
		
		report.init();		
	});	
</script>
@stop

@section('content')

<div class="panel panel-default">
	<div class="panel-heading">
		<h1><span class="glyphicon glyphicon-stats"></span> Daily Sales</h1>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-4">
				<div class="well">
					<p><span class="glyphicon glyphicon-calendar"></span> Pick date range:</p>
					<div class="input-daterange input-group" id="datepicker">
						<input type="text" class="input-sm form-control" id="start" name="start">
						<span class="input-group-addon">to</span>
						<input type="text" class="input-sm form-control" id="end" name="end">
					</div>
					<br class="divider">
					<button id="generateReportBtn" class="btn btn-primary btn-block btn-sm">Generate</button>
				</div>
				<div id="printBtnPlaceholder" class="well">
					<p>To view or print the generated report in printable format. Click the button below.</p>
					<button id="printBtn" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-print"></span> Printable Format (PDF)</button>
				</div>					
			</div>
			<div class="col-md-8">
				<div id="dailySalesWrapper"></div>	
			</div>
		</div>			
	</div>
</div>
@stop
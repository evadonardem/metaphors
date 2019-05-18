@extends('layouts.main-no-bulletin')

@section('title')
Register | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well">
	<h1 class="text-primary">Purchase Orders</h1>
	<div class="row">
		<div class="col-md-4">
			<div class="well">
				<label>Member:</label>
				<p>[{{ $member->code }}] {{ $member->person->lastName }}, {{ $member->person->firstName }} {{ substr($member->person->middleName,0,1) }}.</p>
				<label>Purchase Orders:</label>
				<div id="purchaseOrdersGrid"></div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="well">
				<div class="row">
					<div class="col-md-8">
						<label>Code:</label>
						<p id="purchaseOrderCode"></p>
					</div>
					<div class="col-md-4">
						<label>P.O. Date:</label>
						<p id="purchaseOrderDate"></p>
					</div>
				</div>
				<div id="purchaseOrderProductsGrid"></div>
			</div>
		</div>
	</div>
</div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var grid = $('#purchaseOrdersGrid');
	grid.jqxGrid({
		autoHeight: true,
		source: purchaseOrdersDataAdapter(),
		columns: [
			{ text: 'Code', datafield: 'code', width: '175px' },
			{ text: 'P.O. Date', datafield: 'purchase_order_date' }
		],
		showaggregates: true,
		showstatusbar: true,
		width: '100%',
	});
	grid.jqxGrid('selectedrowindex', 0);
	grid.on('rowselect initialized', function(event) {
		var args = event.args;
		var row = args.rowindex;
		var data = grid.jqxGrid('getrowdata', row);
		$('#purchaseOrderCode').html(data.code);
		$('#purchaseOrderDate').html(data.purchase_order_date);
		purchaseOrderProducts(data.code);
	});
});

function purchaseOrdersDataAdapter() {
	var url = "{{ url() }}/member/{{ $member->code }}/purchase-orders/json";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'code' },
			{ name: 'purchase_order_date' }
		],
		id: 'code',
		url: url
	};
	return new $.jqx.dataAdapter(source);
}
function purchaseOrderProducts(purchaseOrderCode) {
	var url = "{{ url() }}/member/purchase-order/"+purchaseOrderCode+"/products/json";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'code' },
			{ name: 'title' },
			{ name: 'pivot' }
		],
		id: 'code',
		url: url
	};
	var dataAdapter = new $.jqx.dataAdapter(source, {
		loadComplete: function(records) {
			var data = [];
			for(i in records) {
				var product = records[i];
				data[i] = {
					'code' : product.code,
					'title' : product.title,
					'price' : product.pivot.price,
					'quantity' : product.pivot.quantity,
					'amount' : product.pivot.price * product.pivot.quantity
				}
			}

			var purchaseOrderProductsSource = {
				datatype: "json",
				datafields: [
					{ name: 'code' },
					{ name: 'title'},
					{ name: 'price', type: 'decimal' },
					{ name: 'quantity' },
					{ name: 'amount', type: 'decimal' }
				],
				id: 'code',
				localdata: data
			};

			var purchaseOrderProductsDataAdapter = new $.jqx.dataAdapter(purchaseOrderProductsSource);
			var grid = $('#purchaseOrderProductsGrid');
			grid.jqxGrid({
				autoHeight: true,
				columns: [
					{ text: 'Code', datafield: 'code' },
					{ text: 'Title', datafield: 'title' },
					{ text: 'Price', datafield: 'price', cellsalign: 'right', cellsformat: 'd2', width: '75px' },
					{ text: 'Quantity', datafield: 'quantity', cellsalign: 'right', width: '75px', aggregates: ['sum'] },
					{ text: 'Amount', datafield: 'amount', cellsalign: 'right', cellsformat: 'd2', width: '125px', aggregates: ['sum'] }
				],
				showaggregates: true,
				showstatusbar: true,
				source: purchaseOrderProductsDataAdapter,
				width: '100%'
			});
		}
	});
	dataAdapter.dataBind();
}
</script>
@stop

@extends('layouts.main')

@section('title')
Register | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well">
	<h1 class="text-primary">Purchase Order</h1>
	<div class="row">
		<div class="col-md-8">
			<label>Sold To:</label>
			<p>[{{ $member->code }}] {{ $member->person->lastName }}, {{ $member->person->firstName }} {{ substr($member->person->middleName,0,1) }}.</p>
		</div>
		<div class="col-md-4">
			<label>P.O. Date:</label>
			<div id="purchaseOrderDate"></div>
		</div>
	</div>

	<div id="productsGrid" style="margin: 12px 0;"></div>

	<p class="alert alert-warning"><strong>NOTE: </strong>Kindly verify all data entered are correct before processing the purchase order. Take note that action cannot be undone -
		it means that modification of this purchase order is not possible once processed.</p>
	{{ Form::token() }}
	{{ Form::button('Process P.O. &raquo;', array('id' => 'processPurchaseOrderBtn', 'class' => 'btn btn-primary')) }}

</div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	$('#purchaseOrderDate').jqxDateTimeInput({
		height: 20,
		width: '100%'
	});
	$('#purchaseOrderDate').jqxDateTimeInput({ formatString: 'yyyy-MM-dd' });

	var url = "{{ url('products/json') }}";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'code' },
			{ name: 'title' },
			{ name: 'price', type: 'decimal' },
			{ name: 'description' }
		],
		id: 'code',
		url: url
	};
	var dataAdapter = new $.jqx.dataAdapter(source);

	$('#productsGrid').jqxGrid({
		autoHeight: true,
		editable: true,
		selectionmode: 'multiplecellsadvanced',
		source: dataAdapter,
		columns: [
			{ text: 'Code', datafield: 'code', editable: false },
			{ text: 'Title', datafield: 'title', editable: false },
			{ text: 'Price', datafield: 'price', cellsalign: 'right', cellsformat: 'd2', columntype: 'numberinput', width: '75px',
				validation: function(cell, value) {
					if(value<=0) {
						return { result: false, message: "Price must be greater than 0." };
					}
					return true;
				},
				createeditor: function(row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 5 });
				}
			},
			{ text: 'Quantity', datafield: 'quantity', cellsalign: 'right', cellsformat: 'd', columntype: 'numberinput', width: '75px', aggregates: ['sum'],
				validation: function(cell, value) {
					if(value<0) {
						return { result: false, message: "Quantity must be greater than or equal to 0." };
					}
					return true;
				},
				createeditor: function(row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 0, digits: 4 });
				}
			},
			{ text: 'Amount', datafield: 'amount', cellsalign: 'right', cellsformat: 'd2', editable: false, width: '100px', aggregates: ['sum'] }
		],
		showaggregates: true,
		showstatusbar: true,
		width: '100%',
	});
	$('#productsGrid').jqxGrid('focus');
	$('#productsGrid').on('cellendedit', function(event) {
		var args = event.args;
		var row = args.rowindex;
		var oldValue = args.oldvalue;
		var newValue = args.value;

		if(oldValue != newValue) {
			var price = null;
			var quantity = null;
			if(args.datafield == 'price') {
				price = newValue;
				var val = $('#productsGrid').jqxGrid('getcell', row, 'quantity').value;
				quantity = (val==null) ? 0: val;
			} else if (args.datafield == 'quantity') {
				price = $('#productsGrid').jqxGrid('getcell', row, 'price').value;
				quantity = newValue;
			}
			$('#productsGrid').jqxGrid('setcellvalue', row, 'amount', price * quantity);
		}
	});

	$('#processPurchaseOrderBtn').on('click', function() {
		var _token = $('input[name="_token"]').val();

		var memberCode = '{{ $member->code }}';
		var purchaseOrderDate = $('#purchaseOrderDate').val();

		var rows = $('#productsGrid').jqxGrid('getrows');
		var purchaseOrderProducts = new Array();
		for(i in rows) {
			var row = rows[i];
			if(row.quantity>0) {
				var purchaseOrderProduct = new Array();
				purchaseOrderProduct.push(row.code);
				purchaseOrderProduct.push(row.price);
				purchaseOrderProduct.push(row.quantity);

				purchaseOrderProducts.push(purchaseOrderProduct);
			}
		}

		var url = "{{ url('member/'.$member->code.'/purchase-order') }}";
		$.post(url, { purchaseOrderDate : purchaseOrderDate, purchaseOrderProducts : purchaseOrderProducts, _token : _token }, function(data) {
			window.location.reload();
		}).
		done(function() {

		}).
		fail(function() {
			$('#purchaseOrderProcessingFailureDialog')
			var dialog = $('<div id="purchaseOrderProcessingFailureDialog"></div>');
			dialog.append($('<div>P.O. Processing Failed</div>'));
			dialog.append($('<div><p>Payout already created within this P.O. date. Kindly inform the management regarding this matter.</p></div>'));

			dialog.jqxWindow({
				isModal: true,
				width: '30%'
			});

			$('body').append(dialog);

		});

	});

});
</script>
@stop

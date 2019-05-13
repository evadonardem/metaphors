@extends('layouts.main-no-bulletin')

@section('title')
Products | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<h1>Products</h1>
<div id="productsGrid"></div>
<br>
<div class="panel panel-default">
	<div class="panel-heading">
		<span class="glyphicon glyphicon-plus-sign"></span> Add Product
	</div>	
	<div class="panel-body">
		{{ Form::open(array('id' => 'addProductForm', 'method' => 'post', 'files' => true)) }}
        
        <label>Product Image: </label>
        {{ Form::file('image[]', array('class'=> 'form-control')) }}
		
        <label>Product Title: </label>
        {{ Form::text('title', null, array('id' => 'title', 'class' => 'form-control')) }}
        
        <label>Product Description: </label>
		{{ Form::textarea('description', null, array('id' => 'description', 'class' => 'form-control')) }}

		<div id="price" class="form-control"></div>

		{{ Form::submit('Add', array('class' => 'btn btn-primary form-control')) }}

		{{ Form::close() }}

		</div>
		
	</div>
</div>

@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var url = "{{ URL::route('products-json') }}";
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
		autoRowHeight: true,
		editable: true,
		filterable: true,
		selectionmode: 'multiplecellsadvanced',
		source: dataAdapter,
		columns: [			
			{ text: 'Code', datafield: 'code', editable: false, width: '125px' },
			{ text: 'Title', datafield: 'title', editable: false },
			{ text: 'Price', datafield: 'price', cellsalign: 'right', cellsformat: 'd2', columntype: 'numberinput', filterable: false, width: '100px' },
			{ text: ' ', datafield: 'details', columntype: 'button', filterable: false, 
				cellsrenderer: function() {
					return 'Details';
				},
				buttonclick: function(row) {
					var data = $('#productsGrid').jqxGrid('getrowdata', row);
					$('#productDetailsDialog').remove();
					var dialog = $('<div id="productDetailsDialog"></div>');
					var dialogHeader = $('<div>Product Details</div>');
					var dialogContent = $('<div></div>');					

					var productImageThumbnail = $('<p class="text-center"><img src="{{ asset('images/products') }}/'+data.code+'_thumb.jpg" class="img-circle" style="padding: 6px;" /></p>');
					var productTitle = $('<h2 class="text-center">'+data.title+'<br><small>'+data.code+'</small></h2>');
					var productDescription = $('<textarea class="form-control">'+data.description+'</textarea>');
					var controlButtons = $('<div></div>');
					controlButtons.append('<button class="btn btn-primary btn-block">OK</button>');

					dialogContent.append(productImageThumbnail);
					dialogContent.append(productTitle);
					dialogContent.append(productDescription);
					dialogContent.append(controlButtons);

					dialog.append(dialogHeader);
					dialog.append(dialogContent);

					$('body').append(dialog);
					dialog.jqxWindow({
						isModal: true,						
						width: '30%'						
					}).focus();

				},
				width: '75px'
			},
			{ text: ' ', datafield: 'photo', columntype: 'button', filterable: false, 
				cellsrenderer: function() {
					return 'Photo';
				},
				buttonclick: function(row) {
					var data = $('#productsGrid').jqxGrid('getrowdata', row);
					alert('photo...');
				},
				width: '75px'
			}, 
			{ text: ' ', datafield: 'delete', columntype: 'button', filterable: false, 
				cellsrenderer: function() {
					return 'Delete';
				},
				buttonclick: function(row) {
					var data = $('#productsGrid').jqxGrid('getrowdata', row);
					var productCode = data.code;					
					$.get("{{ url('products/destroy') }}/"+productCode, function(data) {						
						$('#productsGrid').jqxGrid('updatebounddata');
					});
				},
				width: '75px'
			}
		],
		pageable: true,
		showfilterrow: true,
		showstatusbar: true,
		width: '100%',		
	});
	$('#productsGrid').jqxGrid('focus');

	$('#price').jqxNumberInput({		
		height: '20px',		
		inputMode: 'simple'		
	});
    
    $('form#addProductForm').submit(function(e) {                
        var url = "{{ URL::route('products-add') }}";        
        var formData = new FormData($(this)[0]);                
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            success: function (data) {            	
                $('#title, #description').val('');
    			$('#amount').val(0);
    			$('#productsGrid').jqxGrid('updatebounddata');
            },
            cache: false,
            contentType: false,
            processData: false
        });        
        e.preventDefault();
    });    

});
</script>
@stop
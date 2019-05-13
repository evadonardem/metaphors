@extends('layouts.print')

@section('title')
Register | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<h2>[{{ $member->code }}]<br> 
	{{ $member->person->lastName }}, {{ $member->person->firstName }}<br>
	<small>Member Downlines</small></h2>	
<div id="downlinesPlaceholder"></div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var url = "{{ URL::to('member/'.$member->code.'/downlines/json') }}";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'code' },
			{ name: 'firstName' },
			{ name: 'middleName' },
			{ name: 'lastName' },
			{ name: 'gender' }	
		],
		id: 'code',
		url: url
	};	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		loadComplete: function(records) {
			var length = records.length;

			$('#downlinesPlaceholder').html('');			
			if(length==0) {
				$('#downlinesPlaceholder').append($('<p>No downlines.</p>'));
				return;
			} 

			$('#downlinesPlaceholder').append($('<p>Total number of levels: <span class="badge">'+length+'</span></p>'));

			for(var i=0; i<length; i++) {
				var level = records[i];				
				
				var data = [];
				for(var j=0; j<level.length; j++) {
					var downline = level[j];
					data[j] = {
						'code' : downline.code,
						'firstName' : downline.firstName,
						'middleName' : downline.middleName,
						'lastName' : downline.lastName,
						'gender' : downline.gender
					};										
				}				

				var downlinesSource = {
					datatype: "json",
					datafields: [						
						{ name: 'code' },
						{ name: 'firstName'},
						{ name: 'middleName'},
						{ name: 'lastName'},
						{ name: 'gender' }
					],
					id: 'code',
					localdata: data
				};

				var downlinesDataAdapter = new $.jqx.dataAdapter(downlinesSource);
				var grid = $('<div id="downlinesLevel'+i+'"></div>');
				$('#downlinesPlaceholder').append('<h3>Level '+(i+1)+'</h3>').append(grid);

				grid.jqxGrid({
					autoHeight: true,				
					columns: [
						{ text: 'Code', datafield: 'code', width: '90px' }, 
						{ text: 'Last Name', datafield: 'lastName' },
						{ text: 'First Name', datafield: 'firstName' },
						{ text: 'Middle Name', datafield: 'middleName' }, 
						{ text: 'Gender', datafield: 'gender', width: '32px' }
					],					
					source: downlinesDataAdapter,
					width: '100%'
				});

			}
		}
	});
	dataAdapter.dataBind();	
});
</script>
@stop
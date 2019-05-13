@extends('layouts.main')

@section('title')
Users Management | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well well-sm">
	<h1><span class="glyphicon glyphicon-list"></span> Users List</h1>
	<div id="usersGrid"></div>
</div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var url = "{{ URL::route('users-json') }}";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'email' },
			{ name: 'username' },
			{ name: 'active' }
		],
		id: 'id',
		url: url
	};
	var dataAdapter = new $.jqx.dataAdapter(source);	

	$('#usersGrid').jqxGrid({
		autoHeight: true,
		autoRowHeight: true,
		editable: true,
		filterable: true,
		selectionmode: 'multiplecellsadvanced',
		source: dataAdapter,
		columns: [			
			{ text: 'ID', datafield: 'id', editable: false, filterable: false },
			{ text: 'Email', datafield: 'email', editable: false },
			{ text: 'Username', datafield: 'username', editable: false },
			{ text: 'Status', datafield: 'active', filterable: false, width: '50px' }			
		],
		pageable: true,
		showfilterrow: true,
		showstatusbar: true,
		width: '100%',		
	});
	$('#usersGrid').jqxGrid('focus');
});
</script>
@stop
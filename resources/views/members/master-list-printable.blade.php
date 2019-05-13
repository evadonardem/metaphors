@extends('layouts.print')

@section('title')
Products | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<h1 class="text-center">List of Members</h1>
<p class="text-center"><em>As of {{ date('l, M d, Y')}}</em></p>
<p>Total number of registered members: <span id="countMembers" class="badge">0</span></p>	
<table class="table">
	<thead>
		<tr>
			<th colspan="5">Member Details</th>
			<th colspan="5">Sponsor Details</th>
		</tr>
		<tr>
			<th>Code</th>
			<th>Last Name</th>
			<th>First Name</th>
			<th>Middle Name</th>
			<th>Gender</th>
			<th>Code</th>
			<th>Last Name</th>
			<th>First Name</th>
			<th>Middle Name</th>
			<th>Gender</th>
		</tr>
	</thead>
	<tbody id="members"></tbody>
	<tfoot>
		<tr>
			<th colspan="10"><p class="text-center"><em>NOTHING FOLLOWS AFTER THIS LINE</em></p></th>
		</tr>
	</tfoot>
</table>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var url = "{{ URL::route('members-json') }}";
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'person_id' },
			{ name: 'code' }			
		],
		id: 'person_id',
		url: url
	};

	var dataAdapter = new $.jqx.dataAdapter(source, {
		loadComplete: function(records) {
			var data = [];
			for(var i=0; i<records.length; i++) {
				var member = records[i];
				data[i] = {					
					'code' : member.code,
					'lastName' : member.person.lastName, 
					'firstName' : member.person.firstName,
					'middleName' : member.person.middleName,
					'gender' : member.person.gender,
					'sponsorCode' : '-',
					'sponsorLastName' : '-',
					'sponsorFirstName' : '-',
					'sponsorMiddleName' : '-',					
					'sponsorGender' : '-'
				};

				if(member.sponsors.length>0) {
					var sponsor = member.sponsors[0];
					data[i].sponsorCode = sponsor.code;
					data[i].sponsorLastName = sponsor.person.lastName;	
					data[i].sponsorFirstName = sponsor.person.firstName;	
					data[i].sponsorMiddleName = sponsor.person.middleName;	
					data[i].sponsorGender = sponsor.person.gender;	
				}

				var row = $('<tr></tr>');
				row.append($('<td>'+member.code+'</td>'));
				row.append($('<td>'+member.person.lastName+'</td>'));
				row.append($('<td>'+member.person.firstName+'</td>'));
				row.append($('<td>'+member.person.middleName+'</td>'));
				row.append($('<td>'+member.person.gender+'</td>'));

				if(member.sponsors.length>0) {
					var sponsor = member.sponsors[0];
					row.append($('<td>'+sponsor.code+'</td>'));
					row.append($('<td>'+sponsor.person.lastName+'</td>'));
					row.append($('<td>'+sponsor.person.firstName+'</td>'));
					row.append($('<td>'+sponsor.person.middleName+'</td>'));
					row.append($('<td>'+sponsor.person.gender+'</td>'));
				} else {
					row.append($('<td colspan="5" class="text-right">&nbsp;</td>'));					
				}

				$('#members').append(row);

			}
			
			
			$('#countMembers').html(records.length);
			

		}
	});
	dataAdapter.dataBind();
});
</script>
@stop
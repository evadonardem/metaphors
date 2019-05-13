@extends('layouts.main-no-bulletin')

@section('title')
Products | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well">
	<div class="text-right">
		<button id="membersListPrintableVersionBtn" class="btn btn-primary">
			<span class="glyphicon glyphicon-new-window"></span> View Printable Version</button>
	</div>
	<h1 class="text-primary">Members</h1>
	<p>Total number of registered members: <span id="countMembers" class="badge">0</span></p>
	<div id="membersGrid"></div>
	<hr class="divider">	
</div>
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

			}
			
			var membersSource = {
				datatype: "json",
				datafields: [					
					{ name: 'code' },
					{ name: 'lastName'},
					{ name: 'firstName'},
					{ name: 'middleName'},
					{ name: 'gender' },
					{ name: 'sponsorCode' },
					{ name: 'sponsorLastName'},
					{ name: 'sponsorFirstName'},
					{ name: 'sponsorMiddleName'},
					{ name: 'sponsorGender' }
				],
				id: 'code',
				localdata: data
			};
			
			var membersDataAdapter = new $.jqx.dataAdapter(membersSource);

			$('#countMembers').html(records.length);
			$('#membersGrid').jqxGrid({
				autoHeight: true,				
				columns: [
					{ text: 'Code', columngroup: 'memberDetails', datafield: 'code', width: '90px' }, 
					{ text: 'Last Name', columngroup: 'memberDetails', datafield: 'lastName' },
					{ text: 'First Name', columngroup: 'memberDetails', datafield: 'firstName' },
					{ text: 'Middle Name', columngroup: 'memberDetails', datafield: 'middleName' }, 
					{ text: 'Gender', columngroup: 'memberDetails', datafield: 'gender', width: '32px' },
					{ text: 'Purchase', datafield: 'memberPurchase', columntype: 'button', 
						cellsrenderer: function() {
							return 'Purchase';
						},
						buttonclick: function(row) {
							var data = $('#membersGrid').jqxGrid('getrowdata', row);
							var url = '{{ url() }}/member/' + data.code + '/purchase-order';
							window.location.replace(url);
						}
					},
					{ text: 'Downlines', datafield: 'memberDownlines', columntype: 'button', 
						cellsrenderer: function() {
							return 'Downlines';
						},
						buttonclick: function(row) {
							var data = $('#membersGrid').jqxGrid('getrowdata', row);
							var url = '{{ url() }}/member/' + data.code + '/downlines';
							window.location.replace(url);
						}
					},
					{ text: 'Purchases', datafield: 'memberPurchases', columntype: 'button', 
						cellsrenderer: function() {
							return 'Purchases';
						},
						buttonclick: function(row) {
							var data = $('#membersGrid').jqxGrid('getrowdata', row);
							var url = '{{ url() }}/member/' + data.code + '/purchase-orders';
							window.location.replace(url);
						}
					},					
					{ text: 'Code', columngroup: 'sponsorDetails', datafield: 'sponsorCode', width: '90px' }, 
					{ text: 'Last Name', columngroup: 'sponsorDetails', datafield: 'sponsorLastName' },
					{ text: 'First Name', columngroup: 'sponsorDetails', datafield: 'sponsorFirstName' },
					{ text: 'Middle Name', columngroup: 'sponsorDetails', datafield: 'sponsorMiddleName' }, 
					{ text: 'Gender', columngroup: 'sponsorDetails', datafield: 'sponsorGender', width: '32px' }
				],
				columngroups: [
					{ text: 'Member Details', align: 'center', name: 'memberDetails' },
					{ text: 'Sponsor Details', align: 'center', name: 'sponsorDetails' }
				],
				filterable: true,
				pageable: true,
				showfilterrow: true,
				showstatusbar: true,
				source: membersDataAdapter,
				width: '100%'
			});

		}
	});
	dataAdapter.dataBind();

	$('#membersListPrintableVersionBtn').on('click', function() {
		window.open("{{ URL::to('members/printable')}}", "_blank", "toolbar=no, scrollbars=yes, location=no, menubar=no, status=no, titlebar=no");
	});

});
</script>
@stop
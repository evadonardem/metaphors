@extends('layouts.main-no-bulletin')

@section('title')
Products | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well"> 
	<h1 class="text-primary"><span class="glyphicon glyphicon-gift"></span> Payouts</h1>
	<div class="row">
		<div class="col-md-7">
			<div class="panel panel-default">
				<div class="panel-heading">Payouts Master-List</div>
				<div class="panel-body">					
					<div id="payoutsGrid"></div>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="panel panel-default">
				<div class="panel-heading">Create New Payout</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-3"><label>Month:</label></div>
						<div class="col-md-9"><div id="payoutMonth"></div></div>
					</div>
					<div class="row">
						<div class="col-md-3"><label>Year:</label></div>
						<div class="col-md-9"><div id="payoutYear"></div></div>
					</div><br>
					<p class="alert alert-warning"><strong>NOTE:</strong> 
						Make it sure that all purchases was entered before creating 
						payout for the selected month/year. Once payout was created 
						adding purchases will not be possible.</p>
					{{ Form::token() }}	
					{{ Form::button('Create', array('id' => 'createPayoutBtn', 'class' => 'btn btn-primary btn-block')) }}
				</div>
			</div>
		</div>
	</div>

	<div class="well">		
		<div class="text-right">			
			<button id="qualifiedMembersPayoutPrintableVersionBtn" class="btn btn-primary">
				<span class="glyphicon glyphicon-new-window"></span> View Printable Version</button>
		</div>		
		<h2 class="text-info">Qualified Members</h2>
		<p>Payout from <sub>(YYYY-MM-DD)</sub>: <span id="payoutFrom" class="badge"></span> | to <sub>(YYYY-MM-DD)</sub>: <span id="payoutTo" class="badge"></span></p>
		<div id="payoutMasterGridWrapper"></div>
	</div>
	<div class="well">
		<div id="memberWithPayoutDetail"></div>
		<div id="payoutMasterGridDetailWrapper"></div>			
	</div> 
</div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() { 
	var grid = $('#payoutsGrid');	
	var months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');	
	$('#payoutMonth').jqxDropDownList({ height: 20, selectedIndex: {{ date('m') - 1 }}, source: months, width: '100%' });
	$('#payoutYear').jqxMaskedInput({ height: 20, mask : '####', width: '100%' }).val('{{ date('Y') }}');
	$('#createPayoutBtn').on('click', function() {
		var payoutMonth;
		for(i in months) {
			var month = months[i];
			if(month == $('#payoutMonth').val()) {
				payoutMonth = i;
				break;
			}
		}
		var payoutYear = $('#payoutYear').val();
		var days = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		days[1] += (payoutYear % 4 == 0 && (payoutYear % 100 != 0 || payoutYear % 400 == 0)) ? 1 : 0;

		var payoutFrom = new Date(payoutYear, payoutMonth, 1);
		var payoutTo = new Date(payoutYear, payoutMonth, days[i]);

		var payout_from = payoutFrom.getFullYear()+'-'+(payoutFrom.getMonth()+1)+'-'+payoutFrom.getDate();
		var payout_to = payoutTo.getFullYear()+'-'+(payoutTo.getMonth()+1)+'-'+payoutTo.getDate();
		var _token = $('input[name="_token"]').val();
		
		var url = '{{ URL::route("payouts-store") }}';			
		$.post(url, { payout_from : payout_from, payout_to : payout_to, _token : _token }, function(data) {
			grid.jqxGrid('updatebounddata');					
		});
	});
		
	grid.jqxGrid({
		autoHeight: true,		
		source: payoutsDataAdapter(),
		columns: [
			{ text: 'From', datafield: 'payout_from', width: '50%' },
			{ text: 'To', datafield: 'payout_to', width: '50%' }
		],
		pageable: true,		
		width: '100%',		
	});	
	grid.jqxGrid('selectedrowindex', 0);
	grid.on('rowselect initialized', function(event) {
		var args = event.args;
		var row = args.rowindex;
		var data = grid.jqxGrid('getrowdata', row);		
		
		$('#payoutFrom').html(data.payout_from);
		$('#payoutTo').html(data.payout_to);

		payoutReport(data.payout_from, data.payout_to);
	});	 
});
function payoutsDataAdapter() { 
	var url = '{{ URL::route("payouts-json") }}';
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'id' },
			{ name: 'payout_from' },
			{ name: 'payout_to' }
		],
		id: 'id',
		url: url
	};
	return new $.jqx.dataAdapter(source); 
}
function payoutReport(payoutFrom, payoutTo) { 
	$('#payoutMasterGridWrapper').html('<p><em>Loading...</em></p>');
	$('#memberWithPayoutDetail').html('');
	$('#payoutMasterGridDetailWrapper').html('');

	var url = '{{ url() }}/payouts/'+payoutFrom+'/'+payoutTo+'/json';	
	var source = {
		datatype: "json",
		datafields: [
			{ name: 'tree' }					
		],
		url: url
	};
	var dataAdapter = new $.jqx.dataAdapter(source, {
		loadComplete : function(records) {
			var forest = records;
			$('#payoutMasterGridWrapper').html('');

			var masterGrid = $('<div id="payoutMasterGrid"></div>');
			$('#payoutMasterGridWrapper').append(masterGrid);

			var membersWithOverrideCommission = [];
			for(i in records) {
				var record = records[i];
				var tree = record.tree;

				// fetched member at level 0 
				var member = tree[0][0];

				membersWithOverrideCommission.push({
					id : i,
					code : member.code,
					firstName : member.firstName,
					middleName : member.middleName,
					lastName : member.lastName,
					gender : member.gender,
					quantity : member.quantity,
					totalQuantityLevel1 : member.totalQuantityLevel1,
					totalOverrideCommissionLevel1 : member.totalOverrideCommissionLevel1,
					totalQuantityLevel2 : member.totalQuantityLevel2,
					totalOverrideCommissionLevel2 : member.totalOverrideCommissionLevel2,
					totalQuantityLevel3 : member.totalQuantityLevel3,
					totalOverrideCommissionLevel3 : member.totalOverrideCommissionLevel3,
					totalQuantityLevel4 : member.totalQuantityLevel4,
					totalOverrideCommissionLevel4 : member.totalOverrideCommissionLevel4,
					totalQuantityLevel5 : member.totalQuantityLevel5,
					totalOverrideCommissionLevel5 : member.totalOverrideCommissionLevel5,
					totalOverrideCommission : member.totalOverrideCommission
				});
			}

			if(membersWithOverrideCommission.length>0) {
				var masterGridSource = {
					datatype: "json",
					datafields: [
						{ name: 'id' },	
						{ name: 'code' },
						{ name: 'firstName'},
						{ name: 'middleName'},
						{ name: 'lastName'},
						{ name: 'gender' },
						{ name: 'quantity', type: 'number' },
						{ name: 'totalQuantityLevel1', type: 'number' },
						{ name: 'totalOverrideCommissionLevel1', type: 'number' },
						{ name: 'totalQuantityLevel2', type: 'number' },
						{ name: 'totalOverrideCommissionLevel2', type: 'number' },
						{ name: 'totalQuantityLevel3', type: 'number' },
						{ name: 'totalOverrideCommissionLevel3', type: 'number' },
						{ name: 'totalQuantityLevel4', type: 'number' },
						{ name: 'totalOverrideCommissionLevel4', type: 'number' },
						{ name: 'totalQuantityLevel5', type: 'number' },
						{ name: 'totalOverrideCommissionLevel5', type: 'number' },
						{ name: 'totalOverrideCommission', type: 'number' }
					],
					id: 'id',
					localdata: membersWithOverrideCommission
				};
				
				var masterGridDataAdapter = new $.jqx.dataAdapter(masterGridSource, {
					loadComplete: function(records) {												
						// load master detail grid
						$('#payoutMasterGridDetailWrapper').html('<p><em>Loading...</em></p>');
						masterGrid.on('rowselect', function(event) {
							$('#payoutMasterGridDetailWrapper').html('');

							var parentMember = event.args.row;
							var id = parentMember.id;
							var tree = forest[id].tree;
							for(i in tree) {
								// root or parent
								if(i==0) {
									$('#memberWithPayoutDetail').html('');									
									var container = $('<div class="well"></div>');
									var parentMemberDetail = $('<h2 class="text-info">'+parentMember.code+' '+parentMember.lastName+', '+parentMember.firstName+' '+((parentMember.middleName.length>0) ? parentMember.middleName.substr(0,1) + "." : null)+'</h2>');
									var parentMemberTotalCommission = $('<p>Total Override Commission from Downlines: <span class="badge">'+($.number(parentMember.totalOverrideCommission, 2))+'</span></p>');
									container.append(parentMemberDetail);
									container.append(parentMemberTotalCommission);
									$('#memberWithPayoutDetail').append(container);									
									continue;
								}

								var masterDetailLabel = $('<label>&raquo; Level '+i+' Override Commission</label>');
								var masterGridDetail = $('<div id="payoutMasterGridDetailLevel'+i+'">'+i+'</div>');
								$('#payoutMasterGridDetailWrapper').append(masterDetailLabel).append(masterGridDetail);

								var downlines = [];
								var level = tree[i];
								for(j in level) {
									var downline = level[j];
									downlines.push({
										code : downline.code,
										firstName : downline.firstName,
										middleName : downline.middleName,
										lastName : downline.lastName,
										gender : downline.gender,
										quantity : downline.quantity,
										overrideCommission : downline.overrideCommission
									});
								}

								var masterGridDetailSource = {
									datatype: "json",
									datafields: [										
										{ name: 'code' },
										{ name: 'firstName'},
										{ name: 'middleName'},
										{ name: 'lastName'},
										{ name: 'gender' },
										{ name: 'quantity', type: 'number' },
										{ name: 'overrideCommission', type: 'number' }
									],
									id: 'code',
									localdata: downlines
								};
								
								var masterGridDetailDataAdapter = new $.jqx.dataAdapter(masterGridDetailSource);

								masterGridDetail.jqxGrid({
									autoHeight: true,
									columns: [
										{ text: 'Code', datafield: 'code', width: '90px', pinned: true },
										{ text: 'Last Name', datafield: 'lastName', width: '12%', pinned: true },
										{ text: 'First Name', datafield: 'firstName', width: '12%', pinned: true },
										{ text: 'Middle Name', datafield: 'middleName', width: '12%', pinned: true },
										{ text: 'Gender', datafield: 'gender', width: '32px', pinned: true },
										{ text: 'Quantity', datafield: 'quantity', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
											aggregatesrenderer: function(aggregates) {
												var value = aggregates['sum'];
												return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
											}
										},
										{ text: 'Commission', datafield: 'overrideCommission', cellsalign: 'right', cellsformat: 'd2', aggregates: ['sum'], 
											aggregatesrenderer: function(aggregates) {
												var value = aggregates['sum'];
												return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
											}
										},
									],
									pageable: true,
									showaggregates: true,
									showstatusbar: true,
									source: masterGridDetailDataAdapter,
									width: '100%'
								});

							}
						});
					}
				});

				masterGrid.jqxGrid({
					autoHeight: true,				
					columns: [
						{ text: 'Code', datafield: 'code', width: '90px', pinned: true },
						{ text: 'Last Name', datafield: 'lastName', width: '12%', pinned: true },
						{ text: 'First Name', datafield: 'firstName', width: '12%', pinned: true },
						{ text: 'Middle Name', datafield: 'middleName', width: '12%', pinned: true },
						{ text: 'Gender', datafield: 'gender', width: '32px', pinned: true },
						{ text: 'Quantity', datafield: 'quantity', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Quantity', columngroup: 'level1', datafield: 'totalQuantityLevel1', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Commission', columngroup: 'level1', datafield: 'totalOverrideCommissionLevel1', cellsalign: 'right', cellsformat: 'd2', width: '90px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Quantity', columngroup: 'level2', datafield: 'totalQuantityLevel2', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Commission', columngroup: 'level2', datafield: 'totalOverrideCommissionLevel2', cellsalign: 'right', cellsformat: 'd2', width: '90px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Quantity', columngroup: 'level3', datafield: 'totalQuantityLevel3', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Commission', columngroup: 'level3', datafield: 'totalOverrideCommissionLevel3', cellsalign: 'right', cellsformat: 'd2', width: '90px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Quantity', columngroup: 'level4', datafield: 'totalQuantityLevel4', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Commission', columngroup: 'level4', datafield: 'totalOverrideCommissionLevel4', cellsalign: 'right', cellsformat: 'd2', width: '90px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Quantity', columngroup: 'level5', datafield: 'totalQuantityLevel5', width: '70px', cellsalign: 'right', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Commission', columngroup: 'level5', datafield: 'totalOverrideCommissionLevel5', cellsalign: 'right', cellsformat: 'd2', width: '90px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
						{ text: 'Total Commission', datafield: 'totalOverrideCommission', cellsalign: 'right', cellsformat: 'd2', width: '125px', aggregates: ['sum'], 
							aggregatesrenderer: function(aggregates) {
								var value = aggregates['sum'];
								return '<div style="float: right; margin: 4px; overflow: hidden;">' + value + '</div>';
							}
						},
					],
					columngroups: [
						{ text: 'Level 1', align: 'center', name: 'level1' },
						{ text: 'Level 2', align: 'center', name: 'level2' },
						{ text: 'Level 3', align: 'center', name: 'level3' },
						{ text: 'Level 4', align: 'center', name: 'level4' },
						{ text: 'Level 5', align: 'center', name: 'level5' }
					],
					filterable: true,
					pageable: true,	
					showaggregates: true,
					showstatusbar: true,
					source: masterGridDataAdapter,
					sortable: true,
					width: '100%'
				});
				masterGrid.jqxGrid('selectrow', 0);
			} else {
				masterGrid.html('<p><em>No qualified member(s).</em></p>');
			}
			
		}
	});
	dataAdapter.dataBind();

	$('#qualifiedMembersPayoutPrintableVersionBtn').unbind('click');
	$('#qualifiedMembersPayoutPrintableVersionBtn').on('click', function() {
		window.open('{{ url() }}/payouts/'+payoutFrom+'/'+payoutTo+'/printable', "_blank", "toolbar=no, scrollbars=yes, location=no, menubar=no, status=no, titlebar=no");
	}); 
}
</script>
@stop
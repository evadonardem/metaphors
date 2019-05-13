@extends('layouts.print')

@section('title')
Payout Report | From: {{ $duration['from']->format('Y-M-d') }} To: {{ $duration['to']->format('Y-M-d') }}
@stop

@section('content')
<h1 class="text-center">Payout Report<br>
	<small>From: {{ $duration['from']->format('Y-M-d') }} 
	To: {{ $duration['to']->format('Y-M-d') }}</small></h1>
<p>This report was generated on {{ date('Y-M-d h:i:s A') }}.</p>
<div id="payoutWrapper"></div>
@stop

@section('embedded_script')
<script type="text/javascript">
$('#payoutWrapper').html('<p><i class="fa fa-user"></i></p>');
$(function() {
	var url = '{{ url() }}/payouts/{{ $duration['from']->format('Y-m-d') }}/{{ $duration['to']->format('Y-m-d') }}/json';
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
				$('#payoutWrapper').html('');
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
						var table = '<table class="table table-striped table-bordered table-condensed" width="100%">';
							table += '<thead>';
							table += '<tr>';
							table += '<th rowspan="2">Code</th>';
							table += '<th rowspan="2">Last Name</th>';
							table += '<th rowspan="2">First Name</th>';
							table += '<th rowspan="2">Middle Name</th>';
							table += '<th rowspan="2">Gender</th>';
							table += '<th rowspan="2">Qty.</th>';
							table += '<th colspan="2">Level 1</th>';
							table += '<th colspan="2">Level 2</th>';
							table += '<th colspan="2">Level 3</th>';
							table += '<th colspan="2">Level 4</th>';
							table += '<th colspan="2">Level 5</th>';
							table += '<th rowspan="2">Total Commission</th>';
							table += '</tr>';
							table += '<tr>';
							table += '<th>Qty.</th>';
							table += '<th>Com.</th>';
							table += '<th>Qty.</th>';
							table += '<th>Com.</th>';
							table += '<th>Qty.</th>';
							table += '<th>Com.</th>';
							table += '<th>Qty.</th>';
							table += '<th>Com.</th>';
							table += '<th>Qty.</th>';
							table += '<th>Com.</th>';
							table += '</tr>';
							table += '</thead>';

						var summationTotalQuantity = 0;
						
						var summationTotalQuantityLevel1 = 0;
						var summationTotalOverrideCommissionLevel1 = 0;
						
						var summationTotalQuantityLevel2 = 0;
						var summationTotalOverrideCommissionLevel2 = 0;

						var summationTotalQuantityLevel3 = 0;
						var summationTotalOverrideCommissionLevel3 = 0;

						var summationTotalQuantityLevel4 = 0;
						var summationTotalOverrideCommissionLevel4 = 0;

						var summationTotalQuantityLevel5 = 0;
						var summationTotalOverrideCommissionLevel5 = 0;
						
						var summationTotalOverrideCommission = 0;
						table += '<tbody>';
						for(j in records) {
							var member = records[j];
							table += '<tr>';
							table += '<td>' + member.code + '</td>';
							table += '<td>' + member.lastName + '</td>';
							table += '<td>' + member.firstName + '</td>';
							table += '<td>' + member.middleName + '</td>';
							table += '<td>' + member.gender + '</td>';

							summationTotalQuantity += member.quantity;
							table += '<td align="right">' + member.quantity + '</td>';
							
							summationTotalQuantityLevel1 += member.totalQuantityLevel1;
							table += '<td align="right">' + member.totalQuantityLevel1 + '</td>';

							summationTotalOverrideCommissionLevel1 += member.totalOverrideCommissionLevel1;
							table += '<td align="right">' + $.number(member.totalOverrideCommissionLevel1, 2) + '</td>';
							
							summationTotalQuantityLevel2 += member.totalQuantityLevel2;
							table += '<td align="right">' + member.totalQuantityLevel2 + '</td>';
							
							summationTotalOverrideCommissionLevel2 += member.totalOverrideCommissionLevel2;
							table += '<td align="right">' + $.number(member.totalOverrideCommissionLevel2, 2) + '</td>';
							
							summationTotalQuantityLevel3 += member.totalQuantityLevel3;
							table += '<td align="right">' + member.totalQuantityLevel3 + '</td>';
							
							summationTotalOverrideCommissionLevel3 += member.totalOverrideCommissionLevel3;
							table += '<td align="right">' + $.number(member.totalOverrideCommissionLevel3, 2) + '</td>';

							summationTotalQuantityLevel4 += member.totalQuantityLevel4;
							table += '<td align="right">' + member.totalQuantityLevel4 + '</td>';

							summationTotalOverrideCommissionLevel4 += member.totalOverrideCommissionLevel4;
							table += '<td align="right">' + $.number(member.totalOverrideCommissionLevel4, 2) + '</td>';
							
							summationTotalQuantityLevel5 += member.totalQuantityLevel5;
							table += '<td align="right">' + member.totalQuantityLevel5 + '</td>';

							summationTotalOverrideCommissionLevel5 += member.totalOverrideCommissionLevel5;
							table += '<td align="right">' + $.number(member.totalOverrideCommissionLevel5, 2) + '</td>';
							
							summationTotalOverrideCommission += member.totalOverrideCommission;
							table += '<td align="right">' + $.number(member.totalOverrideCommission, 2) + '</td>';
							
							table += '</tr>';
						}
						table += '</tbody>';

						table += '<tfoot>';
						table += '<th colspan="5" style="text-align: right;">Grand Total:</th>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantity, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantityLevel1, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommissionLevel1, 2) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantityLevel2, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommissionLevel2, 2) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantityLevel3, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommissionLevel3, 2) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantityLevel4, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommissionLevel4, 2) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalQuantityLevel5, 0) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommissionLevel5, 2) + '</strong></td>';
						table += '<td align="right"><strong>' + $.number(summationTotalOverrideCommission, 2) + '</strong></td>';
						table += '</tfoot>';

						table += '</table>';						

						$('#payoutWrapper').append(table);
						$('#payoutWrapper').append($('<hr class="page-break"/>'));

						var memberWithOverrideCommissionDetails = $('<div/>');
						for(x in forest) {
							var tree = forest[x].tree;
							var member = tree[0][0];
							
							if(member.totalOverrideCommission<=0) continue;

							memberWithOverrideCommissionDetails.append($('<p/>').html('<b>' + member.code + ' ' + member.lastName + ', ' + member.firstName + ' ' + member.middleName.substr(0, 1) + '.</b>'));
							memberWithOverrideCommissionDetails.append($('<p/>').html('Total Override Commission from Downlines ({{ $duration['from']->format('Y-M-d') }} to {{ $duration['to']->format('Y-M-d') }}): <b>' + $.number(member.totalOverrideCommission, 2) + '</b>'));

							// member downlines
							for(y=1; y<=tree.length-1; y++) {
								var level = tree[y];
								var summationQuantity = 0;
								var summationOverrideCommission = 0;
								memberWithOverrideCommissionDetails.append($('<p/>').text('Level ' + y));
								var table = '<table class="table table-striped table-bordered table-condensed" width="100%">';
									table += '<thead>';
									table += '<tr>';
									table += '<th>Code</th>';
									table += '<th>Last Name</th>';
									table += '<th>First Name</th>';
									table += '<th>Middle Name</th>';
									table += '<th>Gender</th>';
									table += '<th>Qty.</th>';
									table += '<th>Commission</th>';
									table += '</tr>';									
									table += '</thead>';
								
								table += '<tbody>';
								for(z in level) {
									downline = level[z];

									if(downline.overrideCommission<=0) continue;

									table += '<tr>';
									table += '<td>' + downline.code + '</td>';
									table += '<td>' + downline.lastName + '</td>';
									table += '<td>' + downline.firstName + '</td>';
									table += '<td>' + downline.middleName + '</td>';
									table += '<td>' + downline.gender + '</td>';
									table += '<td align="right">' + $.number(downline.quantity, 0) + '</td>';
									table += '<td align="right">' + $.number(downline.overrideCommission, 2) + '</td>';
									table += '</tr>';

									summationQuantity += downline.quantity;
									summationOverrideCommission += downline.overrideCommission;
								}
								table += '</tbody>';

								table += '<tfoot>';
								table += '<th colspan="5" style="text-align: right;">Grand Total:</th>';
								table += '<td align="right"><strong>' + $.number(summationQuantity, 0) + '</strong></td>';
								table += '<td align="right"><strong>' + $.number(summationOverrideCommission, 2) + '</strong></td>';
								table += '</tfoot>';

								table += '</table>';

								memberWithOverrideCommissionDetails.append(table);								
							}

							memberWithOverrideCommissionDetails.append($('<hr class="page-break"/>'));
						}

						$('#payoutWrapper').append(memberWithOverrideCommissionDetails);						
					}
				});
				masterGridDataAdapter.dataBind();

			} else {
				$('#payoutWrapper').html('<p><em>No qualified member(s).</em></p>');
			}
		}
	});
	dataAdapter.dataBind();
});
</script>
@stop
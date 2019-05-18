@extends('layouts.main')

@section('title')
Register | Metaphors
@stop

@section('embedded_style')
@stop

@section('content')
<div class="well well-lg">
	<h1 class="text-primary">Registration Form</h1>
	{{ Form::open(array('id' => 'registrationForm', 'url' => URL::route('register'), 'method' => 'post')) }}

	<div class="well well-lg">
		<div class="row">
			<div class="col-md-4">{{ Form::label('dateOfRegistration', 'Date of Registration') }}:</div>
			<div class="col-md-8"><div id="dateOfRegistration"></div></div>
		</div>

		<div class="row">
			<div class="col-md-4">{{ Form::label('memberCode', 'Member Code') }}:</div>
			<div class="col-md-5"><div id="memberCode"></div></div>
			<div class="col-md-3"><input type="button" id="generateMemberCode" value="Generate Code"></div>
		</div>

		<div class="row">
			<div class="col-md-4">{{ Form::label('memberType', 'Membership Type') }}:</div>
			<div class="col-md-8">
				<div class="input-group">
					<span class="input-group-addon">
						{{ Form::radio('memberType', 'Distributor', array('checked' => true) ) }}
					</span>
					<p class="form-control">Distributor</p>
				</div>
				<div class="input-group" style="margin-bottom: 3px;">
					<span class="input-group-addon">
						{{ Form::radio('memberType', 'DistributionCenter' ) }}
					</span>
					<p class="form-control">Distribution Center</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">{{ Form::label('sponsorCode', 'Member Sponsor') }}:</div>
			<div class="col-md-6">{{ Form::hidden('sponsorCode') }}<input type="text" id="sponsor" readonly></span></div>
			<div class="col-md-2"><input type="button" id="removeSponsorBtn" value="Remove"></div>
		</div>

		<div id="membersGrid"></div>

	</div>

	<div class="well well-lg">
		<h2 class="text-primary">Personal Information</h2>
		<div class="row">
			<div class="col-md-4">{{ Form::label('firstName', 'First Name:') }}</div>
			<div class="col-md-8">{{ Form::text('firstName') }}</div>
		</div>
		<div class="row">
			<div class="col-md-4">{{ Form::label('middleName', 'Middle Name:') }}</div>
			<div class="col-md-8">{{ Form::text('middleName') }}</div>
		</div>
		<div class="row">
			<div class="col-md-4">{{ Form::label('lastName', 'Last Name:') }}</div>
			<div class="col-md-8">{{ Form::text('lastName') }}</div>
		</div>
		<div class="row">
			<div class="col-md-4">{{ Form::label('gender', 'Gender:') }}</div>
			<div class="col-md-8"><div id="gender"></div></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">&nbsp;</div>
		<div class="col-md-3">{{ Form::button('Clear', array('id' => 'clearBtn', 'class' => 'btn btn-default btn-block')) }}</div>
		<div class="col-md-3">{{ Form::button('Register', array('id' => 'registerBtn', 'class' => 'btn btn-primary btn-block')) }}</div>
	</div>

	{{ Form::close() }}
</div>

@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	var _token = $('input[name="_token"]').val();

	$('#dateOfRegistration').jqxDateTimeInput({
		height: 20,
		width: '100%'
	});
	$('#dateOfRegistration').jqxDateTimeInput({ formatString: 'yyyy-MM-dd' });

	$('#memberCode').jqxMaskedInput({
		height: 20,
		mask: '####-####',
		width: '100%'
	}).val('');

	$('#generateMemberCode').on('click', function() {
		var url = "{{ URL::route('register-generate-member-code') }}";
		$.get(url, { }, function(data) {
			$('#memberCode').val(data);
		});
	});

	$('#sponsorCode').val('');
	$('#sponsor').jqxInput({ height: 20, width: '100%' }).val('');

	$('#removeSponsorBtn').on('click', function(){
		$('#sponsorCode, #sponsor').val('');
		$('#membersGrid').jqxGrid('clearselection');
	});

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
				var record = records[i];
				data[i] = {
					'person_id' : record.person_id,
					'code' : record.code,
					'lastName' : record.person.lastName,
					'firstName' : record.person.firstName,
					'middleName' : record.person.middleName,
					'gender' : record.person.gender
				};
			}

			var membersSource = {
				datatype: "json",
				datafields: [
					{ name: 'person_id' },
					{ name: 'code' },
					{ name: 'lastName'},
					{ name: 'firstName'},
					{ name: 'middleName'},
					{ name: 'gender' }
				],
				id: 'person_id',
				localdata: data
			};

			var membersDataAdapter = new $.jqx.dataAdapter(membersSource);

			$('#countMembers').html(records.length);
			$('#membersGrid').jqxGrid({
				autoHeight: true,
				columns: [
					{ text: 'Code', datafield: 'code', width: '90px' },
					{ text: 'Last Name', datafield: 'lastName' },
					{ text: 'First Name', datafield: 'firstName' },
					{ text: 'Middle Name', datafield: 'middleName' },
					{ text: 'Gender', datafield: 'gender', width: '32px' }
				],
				filterable: true,
				pageable: true,
				showfilterrow: true,
				showstatusbar: true,
				source: membersDataAdapter,
				width: '100%'
			});

			$('#membersGrid').on('rowselect', function(event) {
				var args = event.args;
				var row = $('#membersGrid').jqxGrid('getrowdata', args.rowindex);
				if(row) {
					$('#sponsorCode').val(row['code']);
					$('#sponsor').val(row['code'] + ' ' + row['lastName'] + ', ' + row['firstName'] + ' ' + row['middleName']);
				}
			});
			$('#membersGrid').jqxGrid('selectrow', -1);

		}
	});
	dataAdapter.dataBind();

	$('#firstName, #middleName, #lastName').jqxInput({
		height: 20,
		width: '100%'
	});

	var genderOptions = new Array("Male", "Female");
	$('#gender').jqxDropDownList({ source: genderOptions, width: '100%' });


	var rules = [
		{ input: '#memberCode', message: 'Required!', action: 'blur', rule: 'required' },
		{ input: '#memberCode', message: 'Already used!', action: 'blur,focus',
			rule: function(input, commit) {
				var state = false;
				$.ajaxSetup({ async: false });
				var memberCodeValidatorURL = "{{ URL::route('register-unique-membercode') }}";
				var memberCodeValidatorPostData = { code : $('#memberCode').val(), _token : _token };
				$.post(memberCodeValidatorURL, memberCodeValidatorPostData, function(data) {
					if(data) state = true;
				});
				return state;
			}
		},
		{ input: '#firstName', message: 'Required!', action: 'blur,focus', rule: 'required' },
		{ input: '#firstName', message: 'Must be 2 to 20 characters!', action: 'blur,focus', rule: 'length=2,20' },
		{ input: '#middleName', message: 'Required!', action: 'blur,focus', rule: 'required' },
		{ input: '#lastName', message: 'Required!', action: 'blur,focus', rule: 'required' },
		{ input: '#gender', message: 'Required!', action: 'blur,focus',
			rule: function(input, commit) {
				return (new String($('#gender').val()).length>0) ? true : false;
			}
		}
	];

	$('#registrationForm').jqxValidator({
		hintType: 'label',
		rules: rules
	});

	$('#registerBtn').on('click', function(){
		if($('#registrationForm').jqxValidator('validate')) {
			if($('#memberType:checked').val() == 'Distributor' && $('#sponsorCode').val().length == 0) {
				alert('No sponsor selected. Kindly pick a sponsor.');
				return;
			}
			$('#registrationForm').submit();
		}
	});

});
</script>
@stop

@extends('layouts.main')

@section('content')
<div class="well well-lg">
	<h1 class="contentHeading text-primary"><span class="glyphicon glyphicon-edit"></span> Sign-up</h1>
	<p>Complete the following sections below:</p>

	{{ Form::open(array('url'=>URL::route('account-sign-up'), 'role'=>'form', 'class'=>'form-horizontal')) }}

	<div class="panel panel-info">
		<div class="panel-heading">PERSONAL INFORMATION</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-md-3 control-label">First Name</label>
				<div class="col-md-9">
					<input type="text" id="firstName">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">Middle Name</label>
				<div class="col-md-9">
					<input type="text" id="middleName">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">Last Name</label>
				<div class="col-md-9">
					<input type="text" id="lastName">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label">Gender</label>
				<div class="col-md-9">
					<div id="gender"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">ACCOUNT DETAILS</div>
		<div class="panel-body">
			<div class="form-group">
				{{ Form::label('email', 'Email', array('class'=>'col-md-3 control-label')) }}
				<div class="col-md-9">{{ Form::text('email') }}</div>
			</div>
			<div class="form-group">
				{{ Form::label('username', 'Username', array('class'=>'col-md-3 control-label')) }}
				<div class="col-md-9">{{ Form::text('username') }}</div>
			</div>
			<div class="form-group">
				{{ Form::label('password', 'Password', array('class'=>'col-md-3 control-label')) }}
				<div class="col-md-9">{{ Form::password('password') }}</div>
			</div>
			<div class="form-group">
				{{ Form::label('confirmPassword', 'Re-Password', array('class'=>'col-md-3 control-label')) }}
				<div class="col-md-9">{{ Form::password('confirmPassword') }}</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-offset-9 col-md-3">{{ Form::button('OK', array('id' => 'okBtn', 'class'=>'btn btn-primary btn-block')) }}</div>
	</div>

	{{ Form::token() }}
	{{ Form::close() }}

</div>
@stop

@section('embedded_script')
<script type="text/javascript">
$(function() {
	$('#firstName, #middleName, #lastName').jqxInput({ height: 30, width: '100%' });

	var gender = new Array('Male', 'Female');
	$('#gender').jqxDropDownList({ height: 30, selectedIndex: 0, source: gender, width: '100%' });

	$('form').jqxValidator({
		hintType: 'label',
		rules: [
			{ input: '#firstName', message: 'Required!', action: 'focus, blur', rule: 'required' },
			{ input: '#firstName', message: 'Must be between 2 to 20 characters!', action: 'focus, blur', rule: 'length=2,20' },
			{ input: '#middleName', message: 'Required!', action: 'focus, blur', rule: 'required' },
			{ input: '#middleName', message: 'Must be between 2 to 20 characters!', action: 'focus, blur', rule: 'length=2,20' },
			{ input: '#lastName', message: 'Required!', action: 'focus, blur', rule: 'required' },
			{ input: '#lastName', message: 'Must be between 2 to 20 characters!', action: 'focus, blur', rule: 'length=2,20' },
			{ input: '#email', message: 'Required!', action: 'keyup, blur', rule: 'required' },
			{ input: '#email', message: 'Invalid format!', action: 'keyup, blur', rule: 'email' },
			{ input: '#email', message: 'Already exists!', action: 'blur, change', rule: 
				function() {
					var url =  "{{ URL::to('/account/sign-up/check-email') }}";
					var email = $('#email').val();
 					state = false;
					$.ajaxSetup({ async: false });					
					$.post(url, { email : email }, function(data) {
						state = (data=='EMAIL_UNIQUE') ? true : false;					
					});					
					return state;
				} 
			},
			{ input: '#username', message: 'Required!', action: 'keyup, blur', rule: 'required' },
			{ input: '#username', message: 'Must be 7 to 20 characters!', action: 'keyup, blur', rule: 'length=7,20' },
			{ input: '#username', message: 'Already exists!', action: 'blur, change', rule: 
				function() {
					var url = "{{ URL::to('/account/sign-up/check-username') }}";
					var username = $('#username').val();
					var state = false;
					$.ajaxSetup({ async: false });
					$.post(url, { username : username }, function(data) {
						state = (data=='USERNAME_UNIQUE') ? true : false;
					});
					return state;
				}
			},
			{ input: '#password', message: 'Required!', action: 'keyup, blur', rule: 'required' },
			{ input: '#password', message: 'Must be 7 to 12 characters!', action: 'keyup, blur', rule: 'length=7,12' },
			{ input: '#confirmPassword', message: 'Confirmation is required!', action: 'keyup, blur', rule: 'required' },
			{ input: '#confirmPassword', message: 'Password doesn\'t match!', action: 'blur, focus', 
				rule: function(input, commit) {
					if(input.val()===$('#password').val()) {
						return true;
					}
					return false;
				} 
			}
		]
	});

	$('#email, #username').jqxInput({ height: 30, width: '100%' });
	$('#username').jqxInput({ maxLength: 20 });
	$('#password, #confirmPassword').jqxPasswordInput({
		height: 28, 
		maxLength: 12,
		showStrength: true, 
		showStrengthPosition: "right", 		
		width: '96%'
	});

	$('#okBtn').on('click', function() {
		if($('form').jqxValidator('validate')) {
			$('form').submit();
		}
	});

});
</script>
@stop
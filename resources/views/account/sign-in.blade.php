@extends('layouts.main')

@section('embedded_style')
<style type="text/css">
.form-signin {
	margin: 0 auto;
	max-width: 350px;
}
#flash_error {
	font-size: 10pt;
	margin-top: 12px;
	padding: 3px 6px;
}
</style>
@stop

@section('content')	
{{ Form::open(array('class' => 'form-signin', 'role' => 'form', 'url'=>URL::route('account-sign-in'))) }}

	<h1>Metaphors</h1>

	<p class="text-info"><span class="glyphicon glyphicon-log-in"></span> Sign-in with your Metaphors Account</p>

	{{ Form::email('email', null, array('autocomplete' => 'off', 'class' => 'form-control', 'placeholder' => 'EMAIL')) }}

	{{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'PASSWORD')) }}

	{{ Form::submit('Sign-in', array('class' => 'btn btn-lg btn-primary btn-block', 'id'=>'loginBtn')) }}

	@if (Session::has('flash_error'))
		<div id="flash_error" class="alert alert-danger">{{ Session::get('flash_error') }}</div>
	@endif

	<hr class="divider">

	<p><a href="{{ URL::route('account-sign-up') }}">Create account now &raquo;</a></p>

{{ Form::close() }}
@stop

@section('embedded_script')
<script type="text/javascript">
$(function(){
	
});
</script>
@stop
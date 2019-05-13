<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title')</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- GLOBAL CSS (Start) -->
		@include('globals.css')
		<!-- GLOBAL CSS (End) -->
		
		@yield('embedded_style')
	</head>
	<body>
		@include('layouts.menu')		

		<div id="content" class="container">			
			@if(Session::has('global'))
				<div id="globalWindow">
					<div>Message</div>
					<div>{{ Session::get('global') }}</div>
				</div>
				<script type="text/javascript">
				$(function() {
					$('#globalWindow').jqxWindow({
						isModal: true,
						width: '350px'
					});
				});
				</script>
			@endif

			@yield('content')		
		</div>
		<footer class="footer">
			@include('layouts.footer')
			@include('scripts.jqwidgets')
			@include('scripts.bootstrap')
			@include('scripts.number')
			@include('scripts.bootstrap-datepicker')
			@yield('embedded_script')
		</footer>
	</body>	
</html>
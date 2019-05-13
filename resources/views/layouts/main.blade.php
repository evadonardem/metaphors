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
			<div class="row">
				<div class="col-md-7">
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
				<div class="col-md-5" style="background: ivory;">
					@include('layouts.banner')
					<div class="panel panel-default">
						<div class="panel-heading">News</div>
						<div class="panel-body"></div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">Events</div>
						<div class="panel-body"></div>
					</div>
				</div>
			</div>			
		</div>
		<footer>
			@include('layouts.footer')			
			@include('scripts.jqwidgets')
			@include('scripts.bootstrap')
			@include('scripts.number')
			@yield('embedded_script')
		</footer>
	</body>	
</html>
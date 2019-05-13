<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title')</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- GLOBAL CSS (Start) -->
		@include('globals.css')
		<style type="text/css" media="print">
		div#content, div#content > .well { border: none; }
		button#printBtn, button#closeBtn { display: none; }
		div#content { width: 100%; }
		hr.page-break { page-break-after: always; }
		</style>
		<!-- GLOBAL CSS (End) -->		
		@yield('embedded_style')
	</head>
	<body>
		<div id="content" class="container">
			<div class="well">
				<div class="text-right">
					<button id="closeBtn" class="btn btn-primary text-right">
						<span class="glyphicon glyphicon-remove-sign"></span> Close</button>
					<button id="printBtn" class="btn btn-primary text-right">
						<span class="glyphicon glyphicon-print"></span> Print</button>					
				</div>
				<p class="text-center"><strong>Metaphors Marketing Ventures (MMV)</strong><br>
					<em>Unique, Fruit Enzyme-based, Health Soaps and Body Products for clear and healthy skin</em>
				</p>						
				<hr class="divider">
				@yield('content')				
			</div>
		</div>

		<div>
			@include('scripts.jqwidgets')
			@include('scripts.bootstrap')
			@include('scripts.number')
			<script type="text/javascript">
			$(function() {
				$('#printBtn').on('click', function() {
					window.print();
				});
				$('#closeBtn').on('click', function() {
					window.close();
				});
			});
			</script>
			@yield('embedded_script')
		</div>
		
	</body>	
</html>
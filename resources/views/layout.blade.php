<html>
	<body>
		<h1>Laravel Quickstart</h1>
		
		@if(Session::has('flash_notice')) 
			<div id="flash_notice">{{ Session::get('flash_notice') }}</div>
		@endif

		@yield('content')
	</body>
</html>
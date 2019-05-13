<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-metaphors-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="{{ URL::route('home') }}" class="navbar-brand">Metaphors</a>
		</div>	

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-metaphors-navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="{{ (Request::is('/')) ? 'active' : null }}"><a href="{{ URL::route('home') }}">
					<span class="glyphicon glyphicon-home"></span> HOME</a></li>

				@if (Auth::check())			
					
				@if (Auth::user()->roles->contains('ADMIN'))
				<li><a href="{{ URL::route('register') }}"><span class="glyphicon glyphicon-file">
					</span> REGISTER</a></li>
				<li><a href="{{ URL::route('members') }}"><span class="glyphicon glyphicon-list-alt">
					</span> MEMBERS</a></li>
				<li></li>			
				<li><a href="{{ URL::route('payouts') }}"><span class="glyphicon glyphicon-gift">
					</span> PAYOUTS</a></li>
				<li><a href="#"><span class="glyphicon glyphicon-calendar">
					</span> NEWS & EVENTS</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span> REPORTS <b class="caret"></b> &nbsp;</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ url('reports/dailysales') }}">Daily Sales</a></li>						
					</ul>
				</li>	
				@endif

				@if (Auth::user()->roles->contains('ACCTMNGR'))
				<li><a href="{{ URL::to('users-management') }}"><span class="glyphicon glyphicon-list">
					</span> USERS MANAGEMENT</a></li>
				@endif
				
				@else
				<li><a href="{{ URL::route('account-sign-in') }}">
					<span class="glyphicon glyphicon-log-in"></span> SIGN-IN</a></li>
				@endif
			</ul>

			<ul class="nav navbar-nav navbar-right">
				@if (Auth::check())
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> {{ strtoupper(Auth::user()->username) }} <b class="caret"></b> &nbsp;</a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Profile</a></li>					
						<li><a href="{{ URL::route('account-change-password') }}">Change Password</a></li>					
						<li class="divider"></li>
						<li><a href="{{ URL::route('account-sign-out') }}">Sign-out</a></li>
					</ul>
				</li>
					@if(Auth::user()->roles->contains('ADMIN'))
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span> SETTINGS <b class="caret"></b> &nbsp;</a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="{{ URL::route('products') }}">Products</a></li>
						</ul>
					</li>
					@endif
				@endif
			</ul>		
		</div>
	</div>
</nav>
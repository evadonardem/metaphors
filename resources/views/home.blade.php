@extends('layouts.main-no-bulletin')

@section('title')
HOME | Metaphors
@stop

@section('embedded_style')
@stop

@section('embedded_script')
@stop

@section('content')
<div class="well">
  <div class="well" style="background: ivory;">
    @include('layouts.banner')
  </div>
  <div class="row">
    <div class="col-md-7">
      <div class="well" style="background: white;">
        <h2>Marketing Plan for Participating Members</h2>
        <h3>Start-up Capital: Php. 950.00</h3>
      	<ul>
      		<li>Sales kit containing 10 pieces of Metaphors products</li>
      		<li>MMV Privilege card/ID</li>
      		<li>Brochures and Paraphernalia</li>
      	</ul>
      	<h3>Distributor's Income</h3>
      	<h4>5 Ways to Earn</h4>
      	<ol>
      		<li>
      			<p><strong>Direct Selling</strong> - Distributors will earn 34% from sale of Metaphors products.
      			Distributors will buy metaphors soap at Php. 75.00 per piece. Retail price is Php. 100.00 per piece.</p>
      			<p>Example: <p>
      			<p>10 pcs. X Php. 25.00 X 30 days = Php. 7,500.00 monthly income<br>
      				20 pcs. X Php. 25.00 X 30 days = Php. 15,000.00 monthly income</p>
      		</li>
      		<li>
      			<p><strong>Network Building</strong> - Distributors will earn from override commissions from a five level referral system
      				established by MMV. Illustrated as follows:</p>
      			<p>Level 1. Php. 5.00 per piece of purchase + Php. 50.00 per referral<br>
      				Level 2. Php. 3.00 per piece of purchase<br>
      				Level 3. Php. 2.00 per piece of purchase<br>
      				Level 4. Php. 1.00 per piece of purchase<br>
      				Level 5. Php. 1.00 per piece of purchase</p>
      		</li>
      		<li><p><strong>Repeat purchases</strong></p></li>
      		<li><p><strong>Sponsor's bonus (Php. 50.00 per registration)</strong></p></li>
      		<li><p><strong>Surprise bonuses</strong></p></li>
      	</ol>
      </div>
    </div>
    <div class="col-md-5">
      <div class="well" style="background: white;">
        <h2>Join Now!</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        <a href="{{ url('register') }}" class="btn btn-primary">Continue Registration &raquo;</a>
      </div>
    </div>
  </div>
</div>
@stop

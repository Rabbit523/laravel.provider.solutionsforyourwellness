@extends('layouts.home_layout') 
@section('content')
@section('title','Email')
<!-- Page Content -->
    <!---->
<div class="welcome">
	 <div class="container">
		 <div class="col-md-3 welcome-left">
			 <h2>Welcome to our site</h2>
		 </div>
		 <div class="col-md-9 welcome-right">
			 <?php echo $messageBody;?>
		 </div>
	 </div>
</div>
<!---->
@stop	
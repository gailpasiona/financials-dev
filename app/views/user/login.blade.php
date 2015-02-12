@extends('templates.master')

@section('content')
<div class="login-container stacked">
	<div class="page-content clearfix">
		<form method="POST" action="{{{ URL::to('/users/login') }}}" accept-charset="UTF-8">
			<h1>Log In</h1>
			<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
			<div class="form-fields">
				<p>Please fill-in below fields to sign in</p>
				<div class="field">
					<input id="username" class="form-control input-lg username-field" type="text" placeholder="Username" value="{{{Input::old('username')}}}" name="username">
				</div>
				<div class="field">
					<input id="password" class="form-control input-lg password-field" type="password" placeholder="Password" value="" name="password">
				</div>
			</div>
			@if (Session::get('error'))
            <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
        @endif

        @if (Session::get('notice'))
            <div class="alert">{{{ Session::get('notice') }}}</div>
        @endif
			<div class="form-action">
				<span class="form-checkbox">
					<input id="Field" class="field login-checkbox" type="checkbox" tabindex="4" value="First Choice" name="Field">
						<label class="choice" for="Field">Remember me</label>
				</span>
				<button class="login-action btn btn-primary">Sign In</button>
			</div>
		</form>
	</div>
</div>
<div class="additionals">
	Forgot Password?  Click to
	<a href="#">Reset</a>
</div>
@stop
@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('css/login.css')}}" />
    
@stop
@extends('templates.master')

@section('nav')
@include('templates.nav.nav_public',array('user' => $user))
@stop

@section('content')
<div class="login-container stacked">
	<div class="page-content clearfix">
		
			<h1>Company Selection</h1>
			<!-- <input type="hidden" name="_token" value="{{{ Session::getToken() }}}"> -->
			<div class="form-fields">
				<p>Please select the company you want to access</p>
				@foreach($companies as $company)
					<a href="session/start/{{$company['alias']}}" class="btn btn-success">{{{$company['name']}}}</a>
				@endforeach
			</div>
	</div>
</div>
@stop

@section('scripts')
<script>


</script>
@stop
@section('styles')
    <link rel="stylesheet" href="{{ URL::asset('css/login.css')}}" />
@stop
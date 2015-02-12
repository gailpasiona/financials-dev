@extends('templates.master')

@section('content')
	
<div class="row">
		
		<div class="col-md-12">
			
			<div class="error-container">
				<h1>Oops!</h1>
				
				<h2>404 Page Not Found</h2>
				
				<div class="error-details">
					Sorry, page does not exists.
					
				</div> <!-- /error-details -->
				
				<div class="error-actions">
					<a class="btn btn-primary btn-lg" href="{{{URL::previous()}}}">
						<i class="icon-chevron-left"></i>
						&nbsp;
						Back to Dashboard						
					</a>
					
					<a class="btn btn-default btn-lg" href="">
						<i class="icon-envelope"></i>
						&nbsp;
						Send Error Report						
					</a>
					
				</div> <!-- /error-actions -->
							
			</div> <!-- /error-container -->			
			
		</div> <!-- /span12 -->
		
	</div>
@stop
@section('styles')
<link rel="stylesheet" href="{{ URL::asset('css/errors.css')}}" />
@stop


<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>@yield('title', 'Integration')</title>

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width">
	<!-- global stylesheets -->	
	{{ HTML::style('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/css/bootstrap.min.css') }}
	{{HTML::style('font-awesome/css/font-awesome.css')}}
	{{HTML::style('css/style.css')}}
	@yield('styles')
	</head>
	<body>
		<div id="wrapper">
			<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
 					<div class="navbar-header">
				      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				      </button>
				      <a class="navbar-brand" href="#">MIB</a>
				    </div>
				    <div class="collapse navbar-collapse">
				      <ul class="nav navbar-nav">
				        <li><a href="{{{route('show_purchases')}}}">PO</a></li>
				        <li><a href="{{{ route('show_suppliers') }}}">Suppliers</a></li>
				        <li><a href="{{{route('show_payroll')}}}">Payroll Summary</a></li>
				        <li><a href="">Billing</a></li>
				        <!-- <li><a href="#about">Billing</a></li> -->
				      </ul>
				    </div><!--/.nav-collapse -->
			</nav>
			<div id="page_content" class="container">
				  
				  @yield('content')
				  
				</div><!-- /.container -->

				
		</div>
		<!-- global scripts-->
		<script src="{{ URL::asset('//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js')}}"></script>
		<script src="{{ URL::asset('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js')}}"></script> 
		<script type="text/javascript">
			$(function() {
        // Highlight the active nav link.
            var url = window.location.pathname;
            var filename = url.substr(url.lastIndexOf('/') + 1);
            $('.navbar a[href$="' + filename + '"]').parent().addClass("active");
        });
		</script>
		@yield('scripts')
	</body>
</html>
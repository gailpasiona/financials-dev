<!doctype html>
<html class="no-js">
<head>
<meta charset="utf-8">
<title>@yield('title', 'default title')</title>
<link rel="icon" href="{{ URL::asset('images/favicon.ico')}}">
 
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width">
@yield('meta')
 
<!-- global stylesheets -->
{{ HTML::style('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/css/bootstrap.min.css') }}
{{ HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css')}}
{{ HTML::style('http://code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css')}}
{{ HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600')}}

{{ HTML::style('css/app.css') }}
{{ HTML::style('css/menu_base.css') }} 
@yield('styles')

<script>
var URL = {
'base' : '{{ URL::to('/') }}',
'current' : '{{ URL::current() }}',
'full' : '{{ URL::full() }}'
};
</script>
</head>

<body>

<div id="wrapper">
 @yield('nav')
 @yield('sub_nav')
    <div id="page-wrapper">
        <div id="content">
            @yield('content')
        </div><!-- ./ #content -->
    </div>
</div><!-- ./ #main -->
 
 
 
<!-- global scripts -->

<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.7.1/modernizr.min.js"></script>
<script src="{{ URL::asset('//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js')}}"></script>
<script src="{{ URL::asset('//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.0.3/js/bootstrap.min.js')}}"></script>
<script src="{{ URL::asset('js/app.js')}}"></script>

<!--yield page specific scripts -->   
@yield('scripts')

 
</body>
</html>


<div id="navtop">
<header class="navbar navbar-inverse navbar-static-top bs-docs-nav" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="./" class="navbar-brand"><!--<img alt="Brand" src="{{ URL::asset('images/mib_logo.png')}}">--> <strong>MIBSSI</strong></a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav navbar-right">
      <li class="dropdown">
      <a data-toggle="dropdown" class="dropdown-toggle" href="javscript:;">
        <i class="fa fa-user fa-lg"></i> 
          <strong> {{$user}}</strong>
        <b class="caret"></b>
      </a>
      
      <ul class="dropdown-menu">
        @if(!isset($admin))
          <li><a href="{{URL::route('switch_session')}}">Switch Company</a></li>
          <li class="divider"></li>
        @endif
        <li><a href="{{{ URL::to('/users/logout') }}}">Logout</a></li>
      </ul>
      
    </li>
    </ul>
    
  </div>
    
  </div>
</header>
</div>

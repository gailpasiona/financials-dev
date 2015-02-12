<!-- <div id="navtop"> -->
<div class="subnavbar">

	<div class="subnavbar-inner">
	
		<div class="container">
			
			<a data-target=".subnav-collapse" data-toggle="collapse" class="subnav-toggle" href="javascript:;">
		      <span class="sr-only">Toggle navigation</span>
		      <i class="fa fa-gears"></i>
		      
		    </a>

			<div class="subnav-collapse in" style="height: auto;">
				<ul class="mainnav">
				
					<li>
						<a href="{{{ action('HomeController@show_admin') }}}">
							<i class="fa fa-home"></i>
							<span>Dashboard</span>
						</a>	    				
					</li>

					<li class="dropdown">
						<a href="{{{action('UsersController@create')}}}">
							<i class="fa fa-users"></i>
							<span>Users</span>
						</a>	    				
					</li>

					<li class="dropdown">
						<a href="#">
							<i class="fa fa-building-o"></i>
							<span>Companies</span>
						</a>
					</li>
					
				</ul>
			</div> <!-- /.subnav-collapse -->

		</div> <!-- /container -->
	
	</div> <!-- /subnavbar-inner -->

</div>
<!-- </div> -->
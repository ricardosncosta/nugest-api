<header id="header">
	<!-- Top navigation -->
	<nav class="navbar navbar-default navbar-inverse">
	 	<div class="container">
		    <!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{ route('home') }}">{{ Config::get('app.name') }}</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			    <ul class="nav navbar-nav navbar-right">
					<form class="navbar-form navbar-left" role="search">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Search">
						</div>
						<button type="submit" class="btn btn-default glyphicon glyphicon-search"></button>
					</form>
                    @if (Auth::check())
	    		        <li class="dropdown">
	    		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Meals&nbsp;<span class="glyphicon glyphicon-cutlery"></span><span class="caret"></span></a>
	    		        	<ul class="dropdown-menu">
	    		        		<li><a href="{{ route('meal::list_get') }}">List</a></li>
	    		        		<li><a href="{{ route('meal::create_get') }}">Create</a></li>
	    		        	</ul>
	    		        </li>
	    		        <li class="dropdown">
	    		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dish&nbsp;<span class="glyphicon glyphicon-cutlery"></span><span class="caret"></span></a>
	    		        	<ul class="dropdown-menu">
	    		        		<li><a href="{{ route('dish::list') }}">List</a></li>
	    		        		<li><a href="{{ route('dish::create_get') }}">Create</a></li>
	    		        	</ul>
	    		        </li>
	    		        <li class="dropdown">
	    		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::User()->email }}&nbsp;<span class="glyphicon glyphicon-user"></span><span class="caret"></span></a>
	    		        	<ul class="dropdown-menu">
	    		        		<li><a href="{{ route('home') }}">Home</a></li>
	    		            	<li role="separator" class="divider"></li>
	    		            	<li><a href="{{ route('user::update_get' )}}">Update account</a></li>
	    		            	<li><a href="{{ route('user::update_email_get' )}}">Update email</a></li>
	    		            	<li><a href="{{ route('user::update_password_get' )}}">Update password</a></li>
	    		            	<li role="separator" class="divider"></li>
	    		            	<li><a href="{{ route('signout') }}">Signout</a></li>
	    		        	</ul>
	    		        </li>
                    @else
	    				<li><a href="{{ route('user::signup_get') }}">Signup</a></li>
			        	<li><a href="{{ route('signin_get') }}">Signin</a></li>
                    @endif
			    </ul>
		    </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<!-- Flash Messages -->
	@if (Session::has('message'))
		<div id="flash-messages" class="container">
			<ul>
			    <li class="alert alert-{{ Session::get('alert-class', 'info') }} alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{{ Session::get('message') }}
				</li>
			</ul>
		</div><!-- #flashMessages -->
	@endif
</header><!-- end #header -->

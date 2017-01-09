
			<header class="jumbotron">
				<h1><?php echo $this->GetConfig('site_title');?></h1>
				<p><?php echo $this->GetConfig('site_slogan');?></p>
			</header>
			<?php if (!System::LoggedIn()) {?>
			<section class="row">
				<div class="col-sm-1"></div>
				<form id="loginForm" class="login-form  col-sm-4" action="/users/login" method="post">
					<div class="panel panel-primary">
						<header class="panel-heading"><h3 class="panel-title">Login</h3></header>
						<article class="panel-body">
							<label for="email">Email</label>
							<input class="form-control" type="text" name="email" />						
							<label for="password">Password</label>
							<input class="form-control" type="password" name="password" />
							<label>
								<input value="remember-me" type="checkbox" />
								Remember me
							</label>
							<br />
							<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
						</article>				
					</div>
				</form>		
				<div class="col-sm-2"></div>
				<form class="register-form col-sm-4" action="/users/register" method="post">
					<div class="panel panel-primary">
						<header class="panel-heading"><h3 class="panel-title">Register</h3></header>
						<article class="panel-body">
							<label for="email">Email</label>
							<input class="form-control" type="text" name="email" />						
							<label for="password">Password</label>
							<input class="form-control" type="password" name="password" />
							<label for="confirmPassword">Confirm Password</label>
							<input class="form-control" type="password" name="confirmPassword" />			
							<br /
							><button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
						</article>
					</div>
				</form>
				<div class="col-sm-1"></div>
			</section>
			<?php }?>
			<pre style="display:none;"><?php var_dump($this);?></pre>
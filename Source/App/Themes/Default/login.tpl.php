			
                <header class="jumbotron">
                    <h1 class="page-header"><?php $data('page_title');?></h1>
                </header>				
				<form id="loginForm" class="login-form" action="/users/login" method="post">
					<div class="panel panel-primary">
						<header class="panel-heading"><h3 class="panel-title">Login</h3></header>
						<article class="panel-body">
							<?php if ( $errors->Count('Post') > 0 ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span>Invalid login
							</div>
							<?php } ?>							
							<label for="email">Email</label>
							<input class="form-control" type="text" name="Email" />						
							<label for="password">Password</label>
							<input class="form-control" type="password" name="Password" />
							<label>
								<input value="remember-me" type="checkbox" />
								Remember me
							</label>
							<br />
							<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
						</article>				
					</div>
				</form>		
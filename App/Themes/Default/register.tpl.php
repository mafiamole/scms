
				<header class="jumbotron">
					<h1 class="page-header"><?php $config('page_title');?></h1>
				</header>					
				<?php if ($data->Count('errors') > 0) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								There are errors in your registration.			  
							</div>				
				<?php } ?>
				<form class="register-form" action="/users/register" method="post">
					<div class="panel panel-primary">
						<header class="panel-heading"><h3 class="panel-title">Register</h3></header>
						<article class="panel-body">
							<label for="Email">Email</label>
							<?php if ( $errors->Has('Post','Email') ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php $errors('Post','Email');?>			  
							</div>
							<?php } ?>						
							<input class="form-control" type="text" name="Email" />						
							<label for="Password">Password</label>
							<?php if ( $errors->Has('Post','Password') ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php $errors('Post','Password')''?>			  
							</div>
							<?php } ?>								
							<input class="form-control" type="password" name="Password" />
							<label for="ConfirmPassword">Confirm Password</label>
							<?php if ( $errors->Has('Post','ConfirmPassword') ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php $errors('Post','ConfirmPassword');?>			  
							</div>
							<?php } ?>								
							<input class="form-control" type="password" name="ConfirmPassword" />			
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
						</article>
					</div>
				</form>	
<?php $post = $this->GetData('postData');?>
<?php $errors = $this->GetData('errors');?>
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>					
				<?php if (count($errors) > 0) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								There are errors in your registration.			  
							</div>				
				<?php } ?>
				<form class="register-form" action="/users/register" method="post">
					<div class="panel panel-primary">
						<header class="panel-heading"><h3 class="panel-title">Register</h3></header>
						<article class="panel-body">
							<label for="email">Email</label>
							<label for="title">Post Title</label>
							<?php if ( isset($errors['email']) ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php echo $errors['email'];?>			  
							</div>
							<?php } ?>						
							<input class="form-control" type="text" name="email" />						
							<label for="password">Password</label>
							<?php if ( isset($errors['password']) ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php echo $errors['password'];?>			  
							</div>
							<?php } ?>								
							<input class="form-control" type="password" name="password" />
							<label for="confirmPassword">Confirm Password</label>
							<?php if ( isset($errors['confirmPassword']) ) {?>
							<div class="alert alert-danger" role="alert">
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
								<span class="sr-only">Error:</span><?php echo $errors['confirmPassword'];?>			  
							</div>
							<?php } ?>								
							<input class="form-control" type="password" name="confirmPassword" />			
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
						</article>
					</div>
				</form>	
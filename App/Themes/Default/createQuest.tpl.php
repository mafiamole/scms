				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>
				<form class="register-form" action="/users/register" method="post">
					<div class="panel panel-primary">						
						<article class="panel-body">
							<label for="email">Name</label>
							<input class="form-control" type="text" name="name" />						
							<label for="password">Category</label>
							<select name="category" class="form-control">
								<option value="1">Category 1</option>
								<option value="2">Category 2</option>
								<option value="3">Category 3</option>
							</select>
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
						</article>
					</div>
				</form>	
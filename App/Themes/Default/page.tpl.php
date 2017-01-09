<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">	
		<title><?php echo $this->FormatTitle()?></title>
		<link href='/Resources/bootstrap-3.3.7-dist/css/bootstrap.min.css' rel='stylesheet' type='text/css' />
		<link href="/Resources/themes/default/general.css" type="text/css" rel="stylesheet"  />		
	</head>
	<body>
		<header>
			<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only">Toggle Navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li class="active"><a href="/">Home</a></li>
							
							<!--<li><a href="/characters">Characters</a></li>-->
							<li><a href="/simm">Simm</a></li>
							<li><a href="/quests">Quests</a></li>
							<?php if (System::LoggedIn()) {?>
								<li><a href="/users/logout">Logout</a></li>
							<?php }?>
						</ul>					
					</div>
				</div>
			</nav>
		</header>
		<main class="container"  role="main">
			<?php $this->Call($data->Get('pageController','Controller'));?>
		</main>
		<footer class="container text-center">
			<a href="">&copy; Starfleet Strategic Response Fleet 2016. All rights reserved.</a><br />
			Version: 0.0.0.1 (Alpha)
		</footer>
		<script src="/Resources/js/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="/Resources/bootstrap-3.3.7-dist/js/bootstrap.min.js" type="text/javascript"></script>	
		
	</body>
</html>
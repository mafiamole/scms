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
						<a class="navbar-brand" href="#">Admin Panel</a>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
                        <?php if ( $data->Has('TopNavigation') ) {
                                foreach ($data->TopNavigation as $topNav) {
                                    //$hasAccess = in_array($_SESSION['users']->UserGroups,$topNav['UserGroups']);
                                    //if (!$hasAccess) continue; // Skip to the next navigation item!
                                    $match = $topNav->Equals('Active',true,'class="Active"','');
                        ?>
                                    <li <?php echo $match;?>><a href="<?php $topNav('URL');?>"><?php $topNav('ContentTitle');?></a></li>
                        <?php
                                
                                }
                            } else { ?>
							<?php if (System::LoggedIn()) {?>
								<li><a href="/users/logout">Logout</a></li>
							<?php }?>                            
                        <?php } ?>

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
		
        <pre style="display:none;"><?php var_dump($this);?></pre>
	</body>
</html>
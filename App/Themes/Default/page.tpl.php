<?php
function ExtractID($userGroup)
{
	return $userGroup->Id;
}
?>
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
			<?php $this->Call('TopNavigation');?>
		</header>
		<main class="container"  role="main">
			<?php
			echo $this->data->mainContent;
			?>
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
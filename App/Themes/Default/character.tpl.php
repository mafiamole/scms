				
				<?php 
				//$character = $this->GetData('character');
				//$fields = $this->GetData('fields');
				$character 	= $data->Get('character');
				$fields 	= $data->Get('fields');
				if ($character) {
					?>
					<header class="jumbotron">
						<h1 class="page-header"><?php $config('page_title');?></h1>
<?php
if (isset($_SESSION['user']->Id) && $character->UserId == $_SESSION['user']->Id) {
?>
						<a class="btn btn-primary btn-lg" href="/simm/edit/<?php echo $character->ContentId;?>" class="btn btn-primary" role="button">Edit</a>
<?php
}						
?>
					</header>
					<div class="media">
					  <div class="media-left">
						<a href="#">
						  <img class="media-object" width="200" src="<?php echo $character->{'Profile Picture'};?>" alt="<?php echo $character->ContentTitle;?>'s profile picture" />
						  <img class="media-object" style="max-width:200px;" src="<?php echo $character->Rank->Image;?>" alt="<?php echo $character->Rank->ContentTitle;?>" />
						</a>
					  </div>
					  <div class="media-body">
						<h4 class="media-heading"><?php echo $character->ContentTitle;?></h4>
						<table class="table">
						<?php foreach ($fields as $field) { 
							$fieldName = $field->Name;
								if ( is_scalar($character->$fieldName) && !in_array($fieldName,array('Rank','Profile Picture')) ) {
						?>
							<tr><th><?php echo $fieldName;?></th><td><?php echo $character->$fieldName;?></td></tr>
						<?php 
								}
							}
						?>
						</table>
					  </div>
					</div>					
					
					<?php
	
				} else {
				?>
				<h1 class="page-header">Character not found</h1>
				
				<?php
				}
				?>
				
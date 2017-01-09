<?php $post = $this->GetData('postData');?>
<?php $errors = $this->GetData('errors');?>

				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>
		<form method="post">
			<label for="Title">Post Title</label>
			<?php if ( isset($errors['Title']) ) {?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span><?php echo $errors['Title'];?>			  
			</div>
			<?php } ?>
			<input class="form-control" type="text" name="Title" value="<?php echo $post['Title'];?>" />
			<label for="Description">Post Body</label>
			<?php if ( isset($errors['Description']) ) {?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span><?php echo $errors['Description'];?>			  
			</div>
			<?php } ?>			
			<textarea class="form-control" name="Description"><?php echo $post['Description'];?></textarea><br />
			<fieldset>
				<label for="usersCharacters">Posting Characters</label><br />
				<?php if ( isset($errors['UsersCharacters']) ) {?>
				<div class="alert alert-danger" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span><?php echo $errors['usersCharacters'];?>			  
				</div>
				<?php } ?>
				<?php foreach($this->GetData('UsersCharacters') as $character) { ?>
					<label><input class="form-control" type="checkbox" name="UsersCharacters[]" <?php echo in_array($character->ContentId,$post['UsersCharacters'])?'checked="checked"':''?> value="<?php echo $character->ContentId;?>" /><?php echo $character->ContentTitle;?></label>
				<?php }?>
			</fieldset>
			<br />
			<!--
			<fieldset>
				<label for="othersCharacters">Other players characters</label><br />
				<?php if ( isset($errors['OthersCharacters']) ) {?>
				<div class="alert alert-danger" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span><?php echo $errors['OthersCharacters'];?>			  
				</div>
				<?php } ?>				
				<?php foreach($this->GetData('OthersCharacters') as $character) { ?>
					<label><input class="form-control" type="checkbox" name="OthersCharacters[]" <?php echo in_array($character->ContentId,$post['OthersCharacters'])?'checked="checked"':''?> value="<?php echo $character->ContentId;?>" /><?php echo $character->ContentTitle;?></label>
				<?php }?>
			</fieldset>
			-->
			<button class="btn btn-lg btn-primary btn-block" type="submit">Post</button>
		</form>
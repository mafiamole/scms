        <header class="jumbotron">
            <h1 class="page-header"><?php $data('page_title');?></h1>
        </header>
		<form method="post">
			<label for="Title">Post Title</label>
			<?php if ( $errors->Has('Post','Title') ) {?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span><?php $errors('Post','Title');?>			  
			</div>
			<?php } ?>
			<input class="form-control" type="text" name="Title" value="<?php $data('post','Title');?>" />
			<label for="Description">Post Body</label>
			<?php if ( $errors->Has('Post','Description') ) {?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span><?php $errors('Post','Description');?>			  
			</div>
			<?php } ?>			
			<textarea class="form-control" name="Description"><?php $data('post','Description');?></textarea><br />
			<fieldset>
				<label for="usersCharacters">Posting Characters</label><br />
				<?php if ( $errors->Has('Post','UsersCharacters') ) {?>
				<div class="alert alert-danger" role="alert">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span><?php echo $errors('Post','usersCharacters');?>			  
				</div>
				<?php } ?>
				<?php foreach($data->UsersCharacters as $character) {
                    $characterSelected = in_array($character->ContentId,$data->GetAll('post','UsersCharacters'));
                ?>
					<label><input class="form-control" type="checkbox" name="UsersCharacters[]" <?php echo $characterSelected?'checked="checked"':''?> value="<?php $character('ContentId');?>" /><?php  $character('ContentTitle');?></label>
				<?php }?>
			</fieldset>
			<br />
			<button class="btn btn-lg btn-primary btn-block" type="submit">Post</button>
		</form>
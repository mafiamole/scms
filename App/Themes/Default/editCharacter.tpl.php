<?php 
$fields = $this->GetData('fields');
$character = $this->GetData('character');
$additionalData = $this->GetData('additionalData');
?>
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>				
				<form class="register-form" action="" method="post" enctype="multipart/form-data">
					<div class="panel panel-primary">						
						<article class="panel-body">
							<label for="name">Name</label>
							<input class="form-control" type="text" name="Title" value="<?php echo $character->ContentTitle;?>" />				
							<input type="hidden" name="URL" value="/characters/<?php echo $character->ContentId;?>" />
							<input type="hidden" name="ContentId" value="<?php echo $character->ContentId;?>" />
							<input type="hidden" name="ContentLangId" value="<?php echo $character->ContentLangId;?>" />
							<?php foreach ($fields as $field) {
								$fieldValue = $character->{$field->Name};
								?>
								<label for="<?php echo $field->Name;?>"><?php echo $field->Name;?></label>
								<?php if($field->Type == "Integer") { ?>						
								<input class="form-control" type="number" name="<?php echo $field->Name;?>" value="<?php echo $fieldValue;?>" />
								<?php } else if($field->Type == "Content" && $field->TypeData) {
									$data = $additionalData[$field->TypeData];
								?>								
								<select class="form-control" name="<?php echo $field->Name;?>"  >
								<?php
									foreach($data as $option) {
										$selected = ($option->ContentId == $fieldValue?'selected="selected"':'');
								?>
									<option <?php echo $selected;?> value="<?php echo $option->ContentId;?>"><?php echo $option->ContentTitle;?></option>
								<?php
									}
								?>
								</select>
								<?php } else if($field->Type == "Image") { 
								if ( strlen($fieldValue) >0) {
								?>
								<img width="100px" src="<?php echo $fieldValue;?>" alt="Current profile picture" />
								<?php
								}
								?>
								<input class="form-control" type="file" name="<?php echo $field->Name;?>" value="<?php echo $fieldValue;?>" />
								<?php } else {?>
								<input class="form-control" type="text" name="<?php echo $field->Name;?>" value="<?php echo $fieldValue;?>" />
								<?php } ?>
							<?php
							}
							?>
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Edit</button>
						</article>
					</div>
				</form>	
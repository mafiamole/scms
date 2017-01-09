<?php 
$fields = $this->GetData('fields');
$postData = $this->GetData('postData');
$additionalData = $this->GetData('additionalData');
$selectedPosition = $this->GetData('selectedPosition');
?>
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>				
				<form class="register-form" action="" method="post"  enctype="multipart/form-data">
					<div class="panel panel-primary">						
						<article class="panel-body">
							<label for="email">Name</label>
							<input class="form-control" type="text" name="Title" />				
							<input type="hidden" name="URL" value="/characters/" />
							
							<?php foreach ($fields as $field) { ?>
								<label for="<?php echo $field->Name;?>"><?php echo $field->Name;?></label>
								<?php if($field->Type == "Integer") { ?>						
								<input class="form-control" type="number" name="<?php echo $field->Name;?>" value="<?php echo $postData[$field->Name];?>" />
								<?php } else if($field->Type == "Content" && $field->TypeData) {
									$data = $additionalData[$field->TypeData];
								?>								
								<select class="form-control" name="<?php echo $field->Name;?>"  >
								<?php
									foreach($data as $option) {
										$selected = ($option->ContentId == $selectedPosition?'selected="selected"':'');
								?>
									<option <?php echo $selected;?> value="<?php echo $option->ContentId;?>"><?php echo $option->ContentTitle;?></option>
								<?php
									}
								?>
								</select>
								<?php } else if($field->Type == "Image") { ?>
								<input class="form-control" type="file" name="<?php echo $field->Name;?>" value="<?php echo $postData[$field->Name];?>" />
								<?php } else {?>
								<input class="form-control" type="text" name="<?php echo $field->Name;?>" value="<?php echo $postData[$field->Name];?>" />
								<?php } ?>
							<?php
							}
							?>
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
						</article>
					</div>
				</form>	
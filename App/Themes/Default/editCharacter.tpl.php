
				<header class="jumbotron">
					<h1 class="page-header"><?php $config('page_title');?></h1>
				</header>				
				<form class="register-form" action="" method="post" enctype="multipart/form-data">
					<div class="panel panel-primary">						
						<article class="panel-body">
							<label for="name">Name</label>
							<input class="form-control" type="text" name="Title" value="<?php $data('Character','ContentTitle');?>" />				
							<input type="hidden" name="URL" value="/characters/<?php $data('Character','ContentId');?>" />
							<input type="hidden" name="ContentId" value="<?php $data('Character','ContentId');?>" />
							<input type="hidden" name="ContentLangId" value="<?php $data('Character','ContentLangId');?>" />
							<?php foreach ($data->Fields as $field) {
                                $fieldName = $field->Name;
								$fieldType = $field->Type;
								?>
								<label for="<?php $field('Name');?>"><?php $field('Name');?></label>
								<?php if($field->Match('Type','Integer')) { ?>						
								<input class="form-control" type="number" name="<?php $field('Name');?>" value="<?php $character($fieldName);?>" />
								<?php } else if($field->Match('Type','Content' && $fieldType) {
									$options = $data->Get('additionalData',$fieldType);
								?>								
								<select class="form-control" name="<?php $field('Name');?>"  >
								<?php
									foreach($options as $option) {
										$selected = ($option->ContentId == $fieldValue?'selected="selected"':'');
								?>
									<option <?php echo $selected;?> value="<?php $option('ContentId');?>"><?php $option('ContentTitle');?></option>
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
								<input class="form-control" type="file" name="<?php $field('Name');?>" />
								<?php } else {?>
								<input class="form-control" type="text" name="<?php $field('Name');?>" value="<?php $data('character',$fieldName);?>" />
								<?php } ?>
							<?php
							}
							?>
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Edit</button>
						</article>
					</div>
				</form>	
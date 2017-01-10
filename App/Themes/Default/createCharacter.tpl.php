<?php 
$selectedPosition = $data->SelectedPosition;
?>
				<header class="jumbotron">
					<h1 class="page-header"><?php $config('page_title');?></h1>
				</header>				
				<form class="register-form" action="" method="post"  enctype="multipart/form-data">
					<div class="panel panel-primary">						
						<article class="panel-body">
							<label for="email">Name</label>
							<input class="form-control" type="text" name="Title" />				
							<input type="hidden" name="URL" value="/characters/" />
							
							<?php
                            foreach ($data->Fields as $field)
                            {
                                $fieldName  = $field->Name;
                                $fieldType  = $field->Type;
                                $typeData   = $field->TypeData;
                            ?>
								<label for="<?php $field('Name');?>"><?php $field('Name');?></label>
								<?php if($fieldType == "Integer") { ?>				
								<input class="form-control" type="number" name="<?php $field('Name');?>" value="<?php $data('PostData',$fieldName);?>" />
								<?php } else if($field->Match('Type',"Content") && $typeData) {
									$options = $additionalData->$typeData;
								?>								
								<select class="form-control" name="<?php $field('Name');?>" >
								<?php
									foreach($options as $option) {
										$selected = ($option->Match('ContentId',$selectedPosition,'selected="selected"',''));
								?>
									<option <?php echo $selected;?> value="<?php $option('ContentId');?>"><?php $option('ContentTitle');?></option>
								<?php
									}
								?>
								</select>
								<?php } else if($field->Type == "Image") { ?>
								<input class="form-control" type="file" name="<?php $field('Name');?>" value="<?php $data('PostData',$fieldName);?>" />
								<?php } else {?>0
s								<input class="form-control" type="text" name="<?php $field('Name');?>" value="<?php $data('PostData',$fieldName);?>" />
								<?php } ?>
							<?php
							}
							?>
							<br /><button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
						</article>
					</div>
				</form>	
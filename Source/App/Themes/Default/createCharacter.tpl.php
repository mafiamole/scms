<?php 
$selectedPosition   = $data->SelectedPosition;
$additionalData     = $data->AdditionalData;
?>
                <header class="jumbotron">
                        <h1 class="page-header"><?php $data('page_title');?></h1>
                </header>
                <form class="register-form" action="" method="post"  enctype="multipart/form-data">
                    <div class="panel panel-primary">
                        <article class="panel-body">
                            <?php if ( $errors->Count('General') ) {?>
                            <div class="alert alert-danger" role="alert">
                                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                <span class="sr-only">Error:</span><?php $errors('General');?>
                            </div>
                            <?php } ?>
                            <label for="Title">Name</label>
                            <input class="form-control" type="text" name="Title" value="<?php $data('PostData','Title');?>"/>				
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
                                <?php } else if($field->Equals('Type',"Content") && $typeData) {
                                        $options = $additionalData->$typeData;
                                ?>
                                <select class="form-control" name="<?php $field('Name');?>" >
                                <?php
                                    foreach($options as $option)
                                    {
                                        $selected = ($option->Equals('ContentId',$selectedPosition,'selected="selected"',''));
                                ?>
                                    <option <?php echo $selected;?> value="<?php $option('ContentId');?>"><?php $option('ContentTitle');?></option>
                                <?php
                                    }
                                ?>
                                </select>
                                <?php } else if($field->Type == "Image") { ?>
                                <input class="form-control" type="file" name="<?php $field('Name');?>" value="<?php $data('PostData',$fieldName);?>" />
                                <?php } else {?>
                                <input class="form-control" type="text" name="<?php $field('Name');?>" value="<?php $data('PostData',$fieldName);?>" />
                                <?php } ?>
                            <?php
                            }
                            ?>
                            <br /><button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
                        </article>
                    </div>
                </form>	
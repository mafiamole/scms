<?php
// Admin controller
$M = array();
$M['Content']                   = LoadModel(Common::LocalDB(),"Content","ContentModel");
$M['ContentLangModel'] 		= LoadModel(Common::LocalDB(),"Content","ContentLangModel");
$M['ContentTypesModel'] 	= LoadModel(Common::LocalDB(),"Content","ContentTypesModel");
$M['ContentDataModel'] 		= LoadModel(Common::LocalDB(),"Content","ContentDataModel");
$M['ContentTypeFieldsModel'] 	= LoadModel(Common::LocalDB(),"Content","ContentTypeFieldsModel");

$contentTypes = $M['ContentTypesModel']->GetAll();
$editType 			= $parameters->Get(1);
$selectedTypeId 	= $parameters->Get(2);
$subAction		 	= $parameters->Get(3);
$selectedContentId 	= $parameters->Get(4);
$selectedType = null;
foreach ($contentTypes as $key => $ct)
{
	$c = $M['Content']->GetContentByTypeId($ct->Id,GetGroups());
	$t = $M['ContentTypesModel']->GetContentTypesFields($ct->Id);
	$contentTypes[$key]->Content = $c;
	$contentTypes[$key]->Fields = $t;
	if ( $selectedTypeId == $ct->Id )
	{
		$selectedType = $ct;
	}
}



?>
<div class="jumbotron">
  <div class="container">
    <h1>Admin Panel</h1>
  </div>
</div>
<?php if (strtolower($editType) == "content") {?>
<p><a class="btn btn-default btn-xl" href="/admin/content/Add">Add Type</a></p>
<div class="row">
	<div class="col-md-3">
		<ul class="list-group">
	
	<?php foreach ($contentTypes as $key => $ct) {?>
			<li class="list-group-item">				
				<h4 class="list-group-item-heading">
					<a href="/admin/content/<?php echo $ct->Id;?>"><?php echo $ct->Name;?><span class="badge"><?php echo count($ct->Fields);?></span></a>
					<a class="btn btn-danger btn-xs pull-right" href="/admin/content/<?php echo $ct->Id;?>/delete">Delete</a>
					<a class="btn btn-default btn-xs pull-right" href="/admin/content/<?php echo $ct->Id;?>/edit#editFieldForm">Edit</a>											
				</h4>
				<p class="list-group-item-text">
					<a class="btn btn-default btn-xs" href="/admin/content/<?php echo $ct->Id;?>/add">Add Content <span class="badge"><?php echo count($ct->Content);?></span></a>
				</p>
			</li>
	<?php }?>
		</ul>
	</div>
	<div class="col-md-9">
		<?php
		$fieldTypes = array('String'=>"Text",'Rich'=>"Rich Text",'Integer'=>'Whole number','Number'=>"Number",'Content'=>"Content",'Image'=>"Image");
		if (strtolower($subAction) == "edit") {
				if ( $selectedTypeId && !$selectedContentId)
				{
					?>
					<h3 id="#editFieldForm"><?php echo $selectedType->Name;?> Fields <a class="btn btn-default btn-xs" href="/admin/content/<?php echo $ct->Id;?>/add">Add Field</a></h3>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Field Name</th>
								<th>Field Type</th>
								<th>Remove Field</th>
							</tr>
						</thead>
					<?php foreach ($selectedType->Fields as $t) {?>
						<tr>
							<td><input type="text" value="<?php echo $t->Name;?>" /></td>
							<td>
								<select>
									<option value="">Please select a type.. </option>
									<?php foreach($fieldTypes as $machineName => $friendlyName) { ?>
										<?php $selected = ($t->Type == $machineName?'selected="selected"':'');?>
										<option value="<?php echo $machineName;?>" <?php echo $selected;?>><?php echo $friendlyName;?></option>
									<?php } ?>
								</select>
							</td>
							<td><a class="btn btn-danger btn-xs pull-right" href="/admin/content/<?php echo $ct->Id;?>/delete/<?php echo $t->id;?>">Delete</a></td>
						</tr>
					<?php }	?>
					</table>
					<a class="btn btn-danger pull-right" href="/admin/content/<?php echo $ct->Id;?>/edit">Save Changes</a>
				<?php			
				}
		} else {
			
				if ( $selectedTypeId && !$selectedContentId)
				{
					?>
					<ul class="list-group">
					<?php foreach ($selectedType->Content as $c) {?>
						<li class="list-group-item"><?php echo $c->ContentTitle;?></li>
					<?php }	?>
					</ul>
				<?php			
				}
				else if ( $selectedTypeId && $selectedContentId)
				{
				}
				else
				{
				}
		}
		?>
	</div>	
</div>
<?php }?>
<?php $questCats = $this->GetData('questCategories');?>

				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				</header>	
				<?php foreach ($questCats as $cat => $questCategory) { ?>
				<h2><?php echo $questCategory->ContentTitle;?></h2>
				<table class="table">
					<thead>
						<tr>
							<td>Quest</td>
							<td>Posts</td>
							<td>View</td>
							<?php if (System::LoggedIn()) {?>
							<td>Create Post</td>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach($questCategory->Quests as $quest) { ?>
						<tr>
							<td><?php echo $quest->ContentTitle;?></td>
							<td><?php echo count($quest->Posts);?></td>
							<td><a href="/quests/view/<?php echo $quest->ContentId;?>">View</a></td>
							<?php if (System::LoggedIn()) {?>
							<td><a href="/quests/post/<?php echo $quest->ContentId;?>">Add</a></td>
							<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php }?>
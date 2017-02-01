				<header class="jumbotron">
					<h1 class="page-header"><?php $data('page_title');?></h1>
				</header>
				<?php foreach ($data->StoryCategories as $cat => $questCategory) { ?>
				<h2><?php $questCategory('ContentTitle');?></h2>
				<table class="table">
					<thead>
						<tr>
							<td>Story</td>
							<td>Posts</td>
							<td>View</td>
							<?php if (System::LoggedIn()) {?>
							<td>Create Post</td>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach($questCategory->Stories as $quest) { ?>
						<tr>
							<td><?php $quest('ContentTitle');?></td>
							<td><?php echo $quest->Count('Posts');?></td>
							<td><a href="/quests/view/<?php $quest('ContentId');?>">View</a></td>
							<?php if (System::LoggedIn()) {?>
							<td><a href="/quests/post/<?php $quest('ContentId');?>">Add</a></td>
							<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php }?>

				<header class="jumbotron">
					<h1 class="page-header"><?php $config('page_title');?></h1>
					<p>
						<a href="<?php echo $_SERVER['REQUEST_URI'];?>/create/" class="btn btn-primary btn-lg" role="button">Create Character »</a>
					</p>
				</header>
				<table class="table">
					<thead>
						<tr>
							<?php foreach($data->Headers as $header) {?>
								<td><?php echo $header;?></td>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach($data->Characters as $character) { ?>
						<tr>
							<?php foreach($data->Headers as $key => $header) {?>
							<td>
							<?php if ($key == 'rank') {?>
								<img src="<?php $character($key,'image');?>" alt="<?php $character($key,'name');?>" />
							<?php } else if ($key == "view") { ?>
								<a href="<?php $character($key);?>">View</a>
							<?php } else { ?>							
								<?php $character($key);?>
							<?php } ?>
							</td>
							<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
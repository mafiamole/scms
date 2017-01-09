
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
					<p>
						<a href="<?php echo $_SERVER['REQUEST_URI'];?>/create/" class="btn btn-primary btn-lg" role="button">Create Character »</a>
					</p>
				</header>
				<table class="table">
					<thead>
						<tr>
							<?php foreach($this->GetData('headers') as $header) {?>
								<td><?php echo $header;?></td>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php foreach($this->GetData('characters') as $character) { ?>
						<tr>
							<?php foreach($this->GetData('headers') as $key => $header) {?>
							<td>
							<?php if ($key == "rank") {?>
								<img src="<?php echo $character[$key]['image'];?>" alt="<?php echo $character[$key]['name'];?>" />
							<?php } else if ($key == "view") { ?>
								<a href="<?php echo $character[$key];?>">View</a>
							<?php } else { ?>							
								<?php echo $character[$key];?>
							<?php } ?>
							</td>
							<?php } ?>					
						</tr>
					<?php } ?>
					</tbody>
				</table>
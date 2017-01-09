				
				<?php 
				$quest = $this->GetData('quest');
				if ($quest) {
					?>
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?> <span class="badge"><?php echo count($quest->Posts);?></span></h1>
					<a class="btn btn-primary btn-lg" href="/quests/post/<?php echo $quest->ContentId;?>">Create a new post!</a>
					<p>Contribute to this story and submit your own post!</p>
				</header>					
					<!--
					<nav aria-label="Page navigation" class="text-center">
					  <ul class="pagination">
						<li>
						  <a href="#" aria-label="Previous">
							<span aria-hidden="true">Previous</span>
						  </a>
						</li>
						<li><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li>
						  <a href="#" aria-label="Next">
							<span aria-hidden="true">Next</span>
						  </a>
						</li>
					  </ul>
					</nav>
					-->
					<?php
					if ( count($quest->Posts) > 0) {
						foreach ($quest->Posts as $post) {
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><?php echo $post->ContentTitle;?></h3></div>
				
							<div class="panel-body">
								<div class="media">
								
									<div class="media-left">
									Posted By<br />
										<?php foreach ($post->Characters as $character) {?>
										
										<a href="/simm/view/<?php echo $character->ContentId;?>">
											<img class="media-object" width="128px" src="<?php echo $character->{'Profile Picture'};?>" alt="<?php echo $character->ContentTitle;?>">
											<?php if ( isset($character->Rank) && isset($character->Rank->ContentTitle)&& isset($character->Rank->Image)) { 
											$rank = $character->Rank;
											
											?>
											<img class="media-object" width="128px" src="<?php echo $rank->Image;?>" alt="<?php echo $rank->ContentTitle;?>">
											<?php } ?>
											<?php echo isset($rank->ContentTitle)?$rank->ContentTitle:"";?> <?php echo $character->ContentTitle;?>
										</a>
										
										<?php 
										} ?>									
									</div>
									<div class="media-body">
										<?php echo $post->ContentDescription;?>
									</div>
								</div>
							</div>
							<div class="panel-footer">Posted on: <?php echo $post->DateCreated;?></div>
						</div>
						<?php
						
						}
					?>
					<a class="btn btn-primary btn-lg" href="/quests/post/<?php echo $quest->ContentId;?>">Create a new post!</a>
					<?php
					} else {
					?>
					No posts found. <?php if (System::LoggedIn()) {?>Why not <a class="btn btn-primary btn-xs" href="/quests/post/<?php echo $quest->ContentId;?>">Add</a> one?<?php }?>
					<?php
					}
				?>
					<!--
					<nav aria-label="Page navigation" class="text-center">
					  <ul class="pagination">
						<li>
						  <a href="#" aria-label="Previous">
							<span aria-hidden="true">Previous</span>
						  </a>
						</li>
						<li><a href="#">1</a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li>
						  <a href="#" aria-label="Next">
							<span aria-hidden="true">Next</span>
						  </a>
						</li>
					  </ul>
					</nav>
					-->
				<?php
				
				} else {
				?>
					<h1 class="page-header">Quest not found</h1>
				
				<?php
				}
				?>
				
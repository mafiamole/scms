				
				<?php
				if ($data->Has('story')) {
                                    $story = $data->story;
                                    $postCount = $story->Count('Posts');
					?>
				<header class="jumbotron">
					<h1 class="page-header"><?php $data('page_title');?> <span class="badge"><?php echo $postCount;?></span></h1>
					<a class="btn btn-primary btn-lg" href="/quests/post/<?php $data->story('ContentId');?>">Create a new post!</a>
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
					if ( $postCount > 0) {
						foreach ($quest->posts as $post) {
						?>
						<div class="panel panel-default">
							<div class="panel-heading"><h3 class="panel-title"><?php $post('ContentTitle');?></h3></div>
				
							<div class="panel-body">
								<div class="media">
								
									<div class="media-left">
									Posted By<br />
										<?php foreach ($post->Characters as $character) {?>
										
										<a href="/simm/view/<?php $character('ContentId');?>">
											<img class="media-object" width="128px" src="<?php $character('Profile Picture');?>" alt="<?php $character('ContentTitle');?>">
											<?php if ( $character->Has('Rank','ContentTitle') && $character->Has('Rank','Image')) {?>
											<img class="media-object" width="128px" src="<?php $character('rank','Image');?>" alt="<?php $character('rank','ContentTitle');?>">
											<?php } ?>
											<?php $character('rank','ContentTitle');?> <?php $character('ContentTitle');?>
										</a>
										
										<?php 
										} ?>									
									</div>
									<div class="media-body">
										<?php $post('ContentDescription');?>
									</div>
								</div>
							</div>
							<div class="panel-footer">Posted on: <?php $post('DateCreated');?></div>
						</div>
						<?php
						
						}
					?>
					<a class="btn btn-primary btn-lg" href="/quests/post/<?php $quest('ContentId');?>">Create a new post!</a>
					<?php
					} else {
					?>
					No posts found. <?php if (System::LoggedIn()) {?>Why not <a class="btn btn-primary btn-xs" href="/quests/post/<?php $quest('ContentId');?>">Add</a> one?<?php }?>
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
				
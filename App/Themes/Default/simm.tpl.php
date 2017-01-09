
				<header class="jumbotron">
					<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
					<p>
						<a href="<?php echo $_SERVER['REQUEST_URI'];?>/create/" class="btn btn-primary btn-lg" role="button">Create Character »</a>
					</p>					
				</header>		
				<?php 
				$pageDescription = $this->GetData('description');
				echo $pageDescription['Body'];?>
				<h2>
				
				Sim Manifest
					<div class="btn pull-right" data-toggle="collapse" data-target="#manifest" aria-expanded="false" aria-controls="manifest">
					  <span class="caret" >
					  </span>
					  <span class="sr-only">Toggle Manifest view</span>
					</div>			
				</h2>

				<ul id="manifest" class="list-group collapse in">
				<?php foreach ($this->GetData('manifest') as $manifestGroup) { ?>
					<li class="list-group-item">
						<h4><?php echo $manifestGroup->ContentTitle;?></h4>
						<ul class="media-list">
					<?php foreach ($manifestGroup->Positions as $position) {
						$character = $position->Character;
						
						$characterRank = (isset($character->Rank)?$character->Rank:null);
						$canEditCharacter = isset($_SESSION['user']->Id) && ($character != null && $character->UserId == $_SESSION['user']->Id);
					?>
							<li class="media">
								<div class="media-left">
									<?php if ($character != null) { ?>
									<img style="width:150px;" class="media-object" src="<?php echo $characterRank->Image;?>" alt="<?php echo $character->ContentTitle;?>" />
									<?php } else {?>
									<img style="width:150px;" class="media-object" src="/Resources/images/ranks/b-blank.png" alt="Blank rank" />
									<?php } ?>
								</div>
								<div class="media-body">
									<h4 class="media-heading"><?php echo $position->ContentTitle;?></h4>
									<div class="row">
										<div class="col-md-3">
											<?php if ($character != null) { ?>
											<a href="<?php echo isset($character->ContentId)?"/simm/view/{$character->ContentId}":"";?>"><?php echo $characterRank->ContentTitle;?> <?php echo $character->ContentTitle;?></a>

											<?php } else {
													if(isset($position->Status) && $position->Status == "Open") {
												?>
												<a href="<?php echo $_SERVER['REQUEST_URI'];?>/create?position=<?php echo $position->ContentId;?>" class="btn btn-primary" role="button">Apply for position »</a>
												
											<?php
													}
												}												
											?>
										</div>
										<div class="col-ms-9">
											<?php if ($canEditCharacter) {
											?>
												<a href="/simm/edit/<?php echo $character->ContentId;?>" class="btn btn-primary" role="button">Edit</a>
											<?php
														}
											?><br />										
											<?php echo $position->ContentDescription;?>
											
										</div>										
									</div>
								</div>
							</li>
					<?php } ?>
						</ul>
					</li>
				<?php } ?>
				</ul>

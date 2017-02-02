			
			<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only">Toggle Navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
                        <?php if ( $data->Has('TopNavigation') ) {
								
                                foreach ($data->TopNavigation as $topNav) {
									$userGroups = array_map("ExtractID",$_SESSION['user']->groups);
									$navGroups = $topNav->GetAll('UserGroups');
									$hasGroups = (count(array_intersect($userGroups,$navGroups)) > 0);
                                    $hasAccess = isset($_SESSION['user']) && $hasGroups;
                                    if (!$hasAccess) continue; // Skip to the next navigation item!
									 $match = '';
                                    $match = $topNav->Equals('Active',true,'class="Active"','');
                        ?>
                                    <li <?php echo $match;?>><a href="<?php $topNav('URL');?>"><?php $topNav('ContentTitle');?></a></li>
                        <?php
                                
                                }
                            } else { ?>
							<li class="active"><a href="/">Home</a></li>
							
							<!--<li><a href="/characters">Characters</a></li>-->
							<li><a href="/simm">Simm</a></li>
							<li><a href="/quests">Quests</a></li>
							<?php if (System::LoggedIn()) {?>
								<li><a href="/users/logout">Logout</a></li>
							<?php }?>                            
                        <?php } ?>

						</ul>					
					</div>
				</div>
			</nav>
<?php
	$content = $this->GetData('content');
?>
				<h1 class="page-header"><?php echo $this->GetConfig('page_title');?></h1>
				<p>
				<?php echo $content['body'];?>
				</p>
<?php

$view = new View($this->theme,$this->data,$this->config);
echo $view->Show('index.tpl.php');
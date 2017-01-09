<?php

$view = new View($this->theme,$this->defaults,$this->data,$this->config);
echo $view->show('index.tpl.php');
<?php

$view = new View($this->theme,$this->defaults,$this->data,$this->config);
$view->Show('index.tpl.php');
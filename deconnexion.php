<?php

require_once __DIR__. '/include/init.php';

unset ($_SESSION['utilisateur']);
header('Location:index.php');
die;
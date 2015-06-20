<?php

include dirname(__FILE__).'/lib/instagram.php';

$url = $_GET['url'];
InstagramUploader::nextPage($url);

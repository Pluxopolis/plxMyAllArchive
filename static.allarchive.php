<?php
if(!defined('PLX_ROOT')) exit;

include(dirname(__FILE__).'/class.plx.archives.php');
$plxArchives = new plxArchives();
$plxArchives->getArticles();
$plxArchives->Display();
unset($plxArchives);

?>

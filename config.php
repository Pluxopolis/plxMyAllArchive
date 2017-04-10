<?php if(!defined('PLX_ROOT')) exit; ?>
<?php

# Control du token du formulaire
plxToken::validateFormToken($_POST);

# Liste des langues disponibles et prises en charge par le plugin
$aLangs = array($plxAdmin->aConf['default_lang']);

# Si le plugin plxMyMultiLingue est installé on filtre sur les langues utilisées
# On garde par défaut le fr si aucune langue sélectionnée dans plxMyMultiLingue
if(defined('PLX_MYMULTILINGUE')) {
	$langs = plxMyMultiLingue::_Langs();
	$multiLangs = empty($langs) ? array() : explode(',', $langs);
	$aLangs = $multiLangs;
}

if(!empty($_POST)) {
	$plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
	$plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
	$plxPlugin->setParam('sortby', $_POST['sortby'], 'string');
	$plxPlugin->setParam('sort', $_POST['sort'], 'string');
	$plxPlugin->setParam('format', $_POST['format'], 'string');
	$plxPlugin->setParam('exclude', implode(',',$_POST['catId']), 'string');
	$plxPlugin->setParam('template', $_POST['template'], 'string');
	$plxPlugin->setParam('url', $_POST['url'], 'string');
	foreach($aLangs as $lang) {
		$plxPlugin->setParam('mnuName_'.$lang, $_POST['mnuName_'.$lang], 'string');
	}
	$plxPlugin->saveParams();
	header('Location: parametres_plugin.php?p=plxMyAllArchive');
	exit;
}

$var = array();
# initialisation des variables propres à chaque lanque
$langs = array();
foreach($aLangs as $lang) {
	# chargement de chaque fichier de langue
	$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.'plxMyAllArchive/lang/'.$lang.'.php');
	$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $langs[$lang]['L_DEFAULT_MENU_NAME'] : $plxPlugin->getParam('mnuName_'.$lang);
}
# initialisation des variables communes à chaque langue
$var['mnuDisplay'] =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
$var['mnuPos'] =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
$var['sortby'] =  $plxPlugin->getParam('sortby')=='' ? 'by_year' : $plxPlugin->getParam('sortby');
$var['sort'] =  $plxPlugin->getParam('sort')=='' ? 'desc' : $plxPlugin->getParam('sort');
$var['format'] =  $plxPlugin->getParam('format')=='' ? $plxPlugin->getLang('L_DEFAULT_FORMAT') : $plxPlugin->getParam('format');
$var['exclude'] =  $plxPlugin->getParam('exclude')=='' ? '' : $plxPlugin->getParam('exclude');
$var['template'] = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');
$var['url'] = $plxPlugin->getParam('url')=='' ? 'allarchive' : $plxPlugin->getParam('url');

# On récupère les templates des pages statiques
$files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
	foreach($array as $k=>$v)
		$aTemplates[$v] = $v;
}

$aSortBy = array();
$aSortBy['by_year'] = $plxPlugin->getLang('L_SORT_BY_YEAR');
$aSortBy['by_category']	= $plxPlugin->getLang('L_SORT_BY_CATEGORY');
$aSortBy['by_author']	= $plxPlugin->getLang('L_SORT_BY_AUTHOR');
$aSortBy['by_title']	= $plxPlugin->getLang('L_SORT_BY_TITLE');

$aSort = array('desc'=>$plxPlugin->getLang('L_SORT_DESCENDING_DATE'), 'asc'=>$plxPlugin->getLang('L_SORT_ASCENDING_DATE'));

?>
<style>
form.inline-form label {
	width: 250px;
}
</style>
<div id="tabContainer">
<form class="inline-form" id="form_plxallarchive" action="parametres_plugin.php?p=plxMyAllArchive" method="post">
	<div class="tabs">
		<ul>
			<li id="tabHeader_main"><?php $plxPlugin->lang('L_MAIN') ?></li>
			<?php
			foreach($aLangs as $lang) {
				echo '<li id="tabHeader_'.$lang.'">'.strtoupper($lang).'</li>';
			}
			?>
		</ul>
	</div>
	<div class="tabscontent">
		<div class="tabpage" id="tabpage_main">
			<fieldset>
				<p>
					<label for="id_url"><?php $plxPlugin->lang('L_URL') ?>&nbsp;:</label>
					<?php plxUtils::printInput('url',$var['url'],'text','20-255') ?>
				</p>
				<p>
					<label for="id_mnuDisplay"><?php echo $plxPlugin->lang('L_MENU_DISPLAY') ?>&nbsp;:</label>
					<?php plxUtils::printSelect('mnuDisplay',array('1'=>L_YES,'0'=>L_NO),$var['mnuDisplay']); ?>
				</p>
				<p>
					<label for="id_mnuPos"><?php $plxPlugin->lang('L_MENU_POS') ?>&nbsp;:</label>
					<?php plxUtils::printInput('mnuPos',$var['mnuPos'],'text','2-5') ?>
				</p>
				<p>
					<label for="id_sortby"><?php $plxPlugin->lang('L_MENU_SORT_BY') ?>&nbsp;:</label>
					<?php plxUtils::printSelect('sortby', $aSortBy, $var['sortby']) ?>
				</p>
				<p>
					<label for="id_sort"><?php $plxPlugin->lang('L_MENU_SORT') ?>&nbsp;:</label>
					<?php plxUtils::printSelect('sort', $aSort, $var['sort']) ?>
				</p>
				<p>
					<label for="id_format"><?php $plxPlugin->lang('L_MENU_FORMAT') ?></label>
					<?php plxUtils::printInput('format',$var['format'],'text','50-50') ?>
				</p>
				<p>
					<label><?php $plxPlugin->lang('L_MENU_EXCLUDE_CATEGORIES') ?>&nbsp;:</label>
				</p>
				<p>
					<?php
					$catId = explode(',', $var['exclude']);
					$selected = (is_array($catId) AND in_array('000', $catId)) ? ' checked="checked"' : '';
					echo '<input type="checkbox" id="cat_unclassified" name="catId[]"'.$selected.' value="000" /><label for="cat_unclassified">&nbsp;'. L_UNCLASSIFIED .'</label><br />';
					$selected = (is_array($catId) AND in_array('home', $catId)) ? ' checked="checked"' : '';
					echo '<input type="checkbox" id="cat_home" name="catId[]"'.$selected.' value="home" /><label for="cat_home">&nbsp;'. L_CATEGORY_HOME_PAGE .'</label><br />';
					foreach($plxAdmin->aCats as $cat_id => $cat_name) {
						$selected = (is_array($catId) AND in_array($cat_id, $catId)) ? ' checked="checked"' : '';
						echo '<input type="checkbox" id="cat_'.$cat_id.'" name="catId[]"'.$selected.' value="'.$cat_id.'" />';
						if($plxAdmin->aCats[$cat_id]['active'])
							echo '<label for="cat_'.$cat_id.'">&nbsp;'.plxUtils::strCheck($cat_name['name']).'</label><br />';
						else
							echo '<label for="cat_'.$cat_id.'">&nbsp;<em>'.plxUtils::strCheck($cat_name['name']).'</em></label><br />';
					}
				?>
				</p>
				<p>
					<label for="id_template"><?php $plxPlugin->lang('L_MENU_TEMPLATE') ?>&nbsp;:</label>
					<?php plxUtils::printSelect('template', $aTemplates, $var['template']) ?>
				</p>
			</fieldset>
		</div>
		<?php foreach($aLangs as $lang) : ?>
		<div class="tabpage" id="tabpage_<?php echo $lang ?>">
			<?php if(!file_exists(PLX_PLUGINS.'plxMyAllArchive/lang/'.$lang.'.php')) : ?>
			<p><?php printf($plxPlugin->getLang('L_LANG_UNAVAILABLE'), PLX_PLUGINS.'plxMyAllArchive/lang/'.$lang.'.php') ?></p>
			<?php else : ?>
			<fieldset>
				<p>
					<label for="id_mnuName_<?php echo $lang ?>"><?php $plxPlugin->lang('L_MENU_TITLE') ?>&nbsp;:</label>
					<?php plxUtils::printInput('mnuName_'.$lang,$var[$lang]['mnuName'],'text','20-20') ?>
				</p>
			</fieldset>
			<?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<fieldset>
		<p class="in-action-bar">
			<?php echo plxToken::getTokenPostMethod() ?>
			<input type="submit" name="submit" value="<?php $plxPlugin->lang('L_SAVE') ?>" />
		</p>
	</fieldset>
</form>
</div>
<script type="text/javascript" src="<?php echo PLX_PLUGINS."plxMyAllArchive/tabs/tabs.js" ?>"></script>

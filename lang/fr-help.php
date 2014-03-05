<?php
$plxPlugin = $plxAdmin->plxPlugins->getInstance('plxMyAllArchive');
?>
<br />
<strong><?php $plxPlugin->lang('L_HELP1') ?> :</strong><br /><br />
<?php $plxPlugin->lang('L_EXAMPLE') ?> :<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive')) ?>") ?>
</p>

<br />

<strong><?php $plxPlugin->lang('L_HELP2') ?> (#archives_status, #archives_url, #archives_name) :</strong><br /><br />
<?php $plxPlugin->lang('L_EXAMPLE') ?> 1:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">#archives_name</a>')) ?>") ?>
</p>
<?php $plxPlugin->lang('L_EXAMPLE') ?> 2:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">".$plxPlugin->getLang('L_MY_ARCHIVES')."</a>')) ?>") ?>
</p>

<br />

<strong><?php echo $plxPlugin->getLang('L_HELP2').' '.$plxPlugin->getLang('L_BY').' '.$plxPlugin->getLang('L_SORT_BY_YEAR').', '.$plxPlugin->getLang('L_SORT_BY_CATEGORY').', '.$plxPlugin->getLang('L_SORT_BY_AUTHOR') ?><br /></strong>
<p style="padding-left:20px">
'by_year' : <?php $plxPlugin->lang('L_SORT_BY_YEAR') ?><br />
'by_category' : <?php $plxPlugin->lang('L_SORT_BY_CATEGORY') ?><br />
'by_author' : <?php $plxPlugin->lang('L_SORT_BY_AUTHOR') ?><br />
'by_title' : <?php $plxPlugin->lang('L_SORT_BY_TITLE') ?><br />
</p>
<br />
<p style="padding-left:20px">
'asc' : <?php $plxPlugin->lang('L_SORT_ASCENDING_DATE') ?><br />
'desc' : <?php $plxPlugin->lang('L_SORT_DESCENDING_DATE') ?><br />
</p>
<br />
<?php $plxPlugin->lang('L_EXAMPLE') ?> 1:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', array('by_category', 'desc'))) ?>") ?>
</p>
<?php $plxPlugin->lang('L_EXAMPLE') ?> 2:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', array('by_author', 'asc'))) ?>") ?>
</p>
<?php $plxPlugin->lang('L_EXAMPLE') ?> 3:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', array('by_year', 'desc', '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">#archives_name</a>'))) ?>") ?>
</p>
<?php $plxPlugin->lang('L_EXAMPLE') ?> 4:<br />
<p style="color:#000;font-size:12px; background:#fff; padding: 10px 20px 20px 20px; border:1px solid #efefef">
<?php echo htmlspecialchars("<?php eval(\$plxShow->callHook('MyAllArchive', array('by_year', 'asc', '<a href=\"#archives_url\" class=\"#archives_status\" title=\"#archives_name\">".$plxPlugin->getLang('L_MY_ARCHIVES')."</a>'))) ?>") ?>
</p>

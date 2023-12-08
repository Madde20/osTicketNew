<?php
if(!defined('OSTSTAFFINC') || !$faq || !$thisstaff) die('Access Denied');

$category=$faq->getCategory();

?>

                <div class="task-list faq-list">
        <div class="col-md-6 col-sm-4 bcolor"> <h2><?php echo __('Frequently Asked Questions');?></h2></div>
        <div class="col-md-6 col-sm-8 bcolor title-right-icons">
<div class="pull-right flush-right">
<?php
$query = array();
parse_str($_SERVER['QUERY_STRING'], $query);
$query['a'] = 'print';
$query['id'] = $faq->getId();
$query = http_build_query($query); ?>
    <a href="faq.php?<?php echo $query; ?>" class="no-pjax action-button btn btn-default btn-xs">
    <i class="icon-print"></i>
        <?php echo __('Print'); ?>
    </a>
<?php
if ($thisstaff->hasPerm(FAQ::PERM_MANAGE)) { ?>
    <a href="faq.php?id=<?php echo $faq->getId(); ?>&a=edit" class="action-button btn btn-default btn-xs">
    <i class="icon-edit"></i>
        <?php echo __('Edit FAQ'); ?>
    </a>
<?php } ?>
</div></div><div class="clear"></div><br>

</div>

<div id="breadcrumbs">
    <a href="kb.php"><?php echo __('All Categories');?></a>
        &raquo; <a href="kb.php?cid=<?php echo $category->getId(); ?>"><?php
    echo $category->getFullName(); ?></a>
    <span class="faded">(<?php echo $category->isPublic()?__('Public'):__('Internal'); ?>)</span>
</div>

<div class="col-md-9">
        
<div class="faq-content">


<div class="faq-title flush-left"><?php echo $faq->getLocalQuestion() ?>
</div>

<div class="faded"><?php echo __('Last Updated');?>
    <?php echo Format::relativeTime(Misc::db2gmtime($faq->getUpdateDate())); ?>
</div>
<br/>
<div class="thread-body bleed">
<?php echo $faq->getLocalAnswerWithImages(); ?>
</div>
<br>
</div> 
<?php
if ($thisstaff->hasPerm(FAQ::PERM_MANAGE)) { ?>
<form action="faq.php?id=<?php echo  $faq->getId(); ?>" method="post">
    <?php csrf_token(); ?>
    <input type="hidden" name="do" value="manage-faq">
    <input type="hidden" name="id" value="<?php echo  $faq->getId(); ?>">
    <button name="a" class="btn btn-default red button" value="delete"><?php echo __('Delete FAQ'); ?></button>
</form>
<?php }
?>  
    <br>
</div>
    <div class="col-md-3">

<div class="pull-right sidebar faq-meta">
<?php if ($attachments = $faq->getLocalAttachments()->all()) { ?>
<section>
    <header><?php echo __('Attachments');?>:</header>
<?php foreach ($attachments as $att) { ?>
<div>
    <i class="icon-paperclip pull-left"></i>
    <a target="_blank" href="<?php echo $att->file->getDownloadUrl(['id' =>
    $att->getId()]); ?>"
        class="attachment no-pjax">
        <?php echo Format::htmlchars($att->getFilename()); ?>
    </a>
</div>
<?php } ?>
</section>
<?php } ?>

<?php if ($faq->getHelpTopics()->count()) { ?>
<section>
    <header><?php echo __('Help Topics'); ?></header>
<?php foreach ($faq->getHelpTopics() as $T) { ?>
    <div><?php echo $T->topic->getFullName(); ?></div>
<?php } ?>
</section>
<?php } ?>

<?php
$displayLang = $faq->getDisplayLang();
$otherLangs = array();
if ($cfg->getPrimaryLanguage() != $displayLang)
    $otherLangs[] = $cfg->getPrimaryLanguage();
foreach ($faq->getAllTranslations() as $T) {
    if ($T->lang != $displayLang)
        $otherLangs[] = $T->lang;
}
if ($otherLangs) { ?>
<section>
    <div><strong><?php echo __('Other Languages'); ?></strong></div>
<?php
    foreach ($otherLangs as $lang) { ?>
    <div><a href="faq.php?kblang=<?php echo $lang; ?>&id=<?php echo $faq->getId(); ?>">
        <?php echo Internationalization::getLanguageDescription($lang); ?>
    </a></div>
    <?php } ?>
</section>
<?php } ?>

<section>
<div>
    <strong><?php echo $faq->isPublished()?__('Published'):__('Internal'); ?></strong>
</div>
<a data-dialog="ajax.php/kb/faq/<?php echo $faq->getId(); ?>/access" href="#"><?php echo __('Manage Access'); ?></a>
</section>

</div>


<div class="clear"></div>
<br>


</div>
<div class="clear"></div><br>

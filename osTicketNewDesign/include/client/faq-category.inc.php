<?php
if(!defined('OSTCLIENTINC') || !$category || !$category->isPublic()) die('Access Denied');
?>

<div class="row">
    <div class="col-md-12">
        <h1><?php echo $category->getFullName(); ?></h1></div>
<div class="col-md-9">
<p>
<?php echo Format::safe_html($category->getLocalDescriptionWithImages()); ?>
</p>
<?php

if (($subs=$category->getSubCategories(array('public' => true)))) {
    echo '<div>';
    foreach ($subs as $c) {
        echo sprintf('<div><i class="icon-folder-open-alt"></i>
                <a href="faq.php?cid=%d">%s (%d)</a></div>',
                $c->getId(),
                $c->getLocalName(),
                $c->getNumFAQs()
                );
    }
    echo '</div>';
} ?>
<hr>
<?php
$faqs = FAQ::objects()
    ->filter(array('category'=>$category))
    ->exclude(array('ispublished'=>FAQ::VISIBILITY_PRIVATE))
    ->annotate(array('has_attachments' => SqlAggregate::COUNT(SqlCase::N()
        ->when(array('attachments__inline'=>0), 1)
        ->otherwise(null)
    )))
    ->order_by('-ispublished', 'question');

if ($faqs->exists(true)) {
    echo '
         <h4>'.__('Frequently Asked Questions').'</h4>
         <div id="faq" style="margin-top:10px; margin-left: 15px;">
          <div class="rectangle-list">
            <ol>';
foreach ($faqs as $F) {
        $attachments=$F->has_attachments?'<span class="Icon file"></span>':'';
        echo sprintf('
            <li><a href="faq.php?id=%d" >%s &nbsp;%s</a></li>',
            $F->getId(),Format::htmlchars($F->question), $attachments);
    }
    echo '  </ol>
        </div> </div>';
} elseif (!$category->children) {
    echo '<strong>'.__('This category does not have any FAQs.').' <a href="index.php">'.__('Back To Index').'</a></strong>';
}
?>
</div>

<div class="col-md-3">
    <br>
    <div class="sidebar">
  <!--  <div class="searchbar">
        <form method="get" action="faq.php">
        <input type="hidden" name="a" value="search"/>
        <input class="form-control" type="text" name="q" class="search" placeholder="<?php
            echo __('Search our knowledge base'); ?>"/>
        <input type="submit" style="display:none" value="search"/>
        </form> -->
    </div>
    <div class="content">
        
<?php
foreach (Topic::objects()
    ->filter(array('faqs__faq__category__category_id'=>$category->getId()))
    ->distinct('topic_id')    
    as $t) { ?>
        <div class="list-group-item"><a href="?topicId=<?php echo urlencode($t->getId()); ?>"
                                       ><?php echo $t->getFullName(); ?></a></div>
<?php } ?>
       
    </div>
    </div>
</div>
</div>

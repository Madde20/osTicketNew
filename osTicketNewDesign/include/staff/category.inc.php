<?php
if (!defined('OSTSCPINC') || !$thisstaff
        || !$thisstaff->hasPerm(FAQ::PERM_MANAGE))
    die('Access Denied');

$info=array();
$qs = array();
if($category && $_REQUEST['a']!='add'){
    $title=__('Update Category');
    $action='update';
    $submit_text=__('Save Changes');
    $info=$category->getHashtable();
    $info['id']=$category->getId();
    $info['notes'] = $category->getNotes();
    $qs += array('id' => $category->getId());
    $langs = $cfg->getSecondaryLanguages();
    $translations = $category->getAllTranslations();
    foreach ($langs as $tag) {
        foreach ($translations as $t) {
            if (strcasecmp($t->lang, $tag) === 0) {
                $trans = $t->getComplex();
                $info['trans'][$tag] = array(
                    'name' => $trans['name'],
                    'description' => Format::viewableImages($trans['description']),
                );
                break;
            }
        }
    }
}else {
    $title=__('Add New Category');
    $action='create';
    $submit_text=__('Add');
    $qs += array('a' => $_REQUEST['a']);
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info, true);

?>
<form action="categories.php?<?php echo Http::build_query($qs); ?>" method="post" class="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">

 <h2><?php echo $title; ?>
     <?php if (isset($info['name'])) { ?><small>
    — <?php echo $info['name']; ?></small>
     <?php } ?>
    </h2>

<div class="col-md-12"> 
    
    <div><strong><?php echo __('Category Type');?>:</strong>
        <span class="error">*</span></div>
    <div class="col-md-12">     
    <div class="radio">
    <input type="radio" name="ispublic" value="2" <?php echo $info['ispublic']==2?'checked="checked"':''; ?>><b><?php echo __('Featured');?></b> <?php echo __('(on front-page sidebar)');?>
    </div><div class="radio">
    <input type="radio" name="ispublic" value="1" <?php echo $info['ispublic']==1?'checked="checked"':''; ?>><b><?php echo __('Public');?></b> <?php echo __('(publish)');?>
    </div><div class="radio">
    <input type="radio" name="ispublic" value="0" <?php echo !$info['ispublic']?'checked="checked"':''; ?>><?php echo __('Private');?> <?php echo __('(internal)');?>
    </div>
    <div class="error"><?php echo $errors['ispublic']; ?></div>
    </div></div>
<div class="clear"></div>
<div style="margin-top:20px"></div>
<div class="col-md-12"> 
<ul class="clean nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#infos"><?php echo __('Category Information'); ?></a></li>
    <li><a data-toggle="tab" href="#notes"><?php echo __('Internal Notes'); ?></a></li>
</ul>
<div class="tab-content">
<div  id="infos" class="tab-pane fade in active">

<?php
$langs = Internationalization::getConfiguredSystemLanguages();
if (count($langs) > 1) { ?>
    <ul class="alt tabs clean" id="trans">
        <li class="empty"><i class="icon-globe" title="This content is translatable"></i></li>
<?php foreach ($langs as $tag=>$i) {
    list($lang, $locale) = explode('_', $tag);
 ?>
    <li class="<?php if ($tag == $cfg->getPrimaryLanguage()) echo "active";
        ?>"><a href="#lang-<?php echo $tag; ?>" title="<?php
        echo Internationalization::getLanguageDescription($tag);
    ?>"><span class="flag flag-<?php echo strtolower($i['flag'] ?: $locale ?: $lang); ?>"></span>
    </a></li>
<?php } ?>
    </ul>
<?php
} ?>


<?php foreach ($langs as $tag=>$i) {
    $code = $i['code'];
    $cname = 'name';
    $dname = 'description';
    if ($tag == $cfg->getPrimaryLanguage()) {
        $category = $info[$cname];
        $desc = $info[$dname];
    }
    else {
        $category = $info['trans'][$code][$cname];
        $desc = $info['trans'][$code][$dname];
        $cname = "trans[$code][$cname]";
        $dname = "trans[$code][$dname]";
    } ?>
    <div class="tab_content <?php
        if ($code != $cfg->getPrimaryLanguage()) echo "hidden";
      ?>" id="lang-<?php echo $tag; ?>"
      <?php if ($i['direction'] == 'rtl') echo 'dir="rtl" class="rtl"'; ?>
    >
    <div style="padding-bottom:8px;">
                <b><?php echo __('Parent');?></b>:
        <div class="faded"><?php echo __('Parent Category');?></div>
    </div>
    <div style="padding-bottom:8px;">
        <select name="pid">
            <option value="">&mdash; <?php echo __('Top-Level Category'); ?> &mdash;</option>
            <?php
            foreach (Category::getCategories() as $id=>$name) {
                if ($info['id'] && $id == $info['id'])
                    continue; ?>
                <option value="<?php echo $id; ?>" <?php
                    if ($info['category_pid'] == $id) echo 'selected="selected"';
                    ?>><?php echo $name; ?></option>
            <?php
            } ?>
        </select>
        <script>
            $('select[name=pid]').on('change', function() {
                var val = this.value;
                $('select[name=pid]').each(function() {
                    $(this).val(val);
                });
            });
        </script>
    </div>
    <div style="padding-bottom:8px;">
        <b><?php echo __('Category Name');?></b>:
        <span class="error">*</span>
        <div class="faded"><?php echo __('Short descriptive name.');?></div>
    </div>
    <input type="text" size="70" style="font-size:110%;width:100%;box-sizing:border-box"
        name="<?php echo $cname; ?>" value="<?php echo $category; ?>">
    <div class="error"><?php echo $errors['name']; ?></div>

    <div style="padding:8px 0;">
        <b><?php echo __('Category Description');?></b>:
        <span class="error">*</span>
        <div class="faded"><?php echo __('Summary of the category.');?></div>
        <div class="error"><?php echo $errors['description']; ?></div>
    </div>
    <textarea class="richtext" name="<?php echo $dname; ?>" cols="21" rows="12"
        style="width:100%;"><?php
        echo Format::viewableImages($desc); ?></textarea>
    </div>
<?php } ?>
</div>


<div class="tab-pane fade" id="notes">
    <br>
    <b><?php echo __('Internal Notes');?></b>:
    <span class="faded"><?php echo __("Be liberal, they're internal");?></span>
    <textarea class="richtext no-bar" name="notes" cols="21"
        rows="8" style="width: 80%;"><?php echo Format::viewableImages($info['notes']); ?></textarea>
</div>
    </div>

    <br>
<p style="text-align:center">
    <input class="btn btn-default" type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input class="btn btn-default" type="reset"  name="reset"  value="<?php echo __('Reset');?>">
    <input class="btn btn-default" type="button" name="cancel" value="<?php echo __('Cancel');?>" onclick='window.location.href="categories.php"'>
</p>
</form>
<br>
<br>
</div>

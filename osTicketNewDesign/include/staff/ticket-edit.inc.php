<?php
if (!defined('OSTSCPINC')
        || !$ticket
        || !($ticket->checkStaffPerm($thisstaff, Ticket::PERM_EDIT)))
    die('Access Denied');

$info=Format::htmlchars(($errors && $_POST)?$_POST:$ticket->getUpdateInfo(), true);
if ($_POST)
// Reformat duedate to the display standard (but don't convert to local
// timezone)
    $info['duedate'] = Format::date(strtotime($info['duedate']), false, false, 'UTC');
?>
<div class="mobile-lrg">
<form action="tickets.php?id=<?php echo $ticket->getId(); ?>&a=edit" method="post" class="save form-horizontal tedit"  enctype="multipart/form-data">
<?php csrf_token(); ?>
    <input type="hidden" name="do" value="update">
    <input type="hidden" name="a" value="edit">
    <input type="hidden" name="id" value="<?php echo $ticket->getId(); ?>">
    <div class="col-md-12 bcolor"> 
        <div class="pull-left flush-left">
            <h2><?php echo sprintf(__('Update Ticket #%s'), $ticket->getNumber()); ?></h2>
        </div>
    </div>
    <div class="clear"></div>
    <div class="bg-gray padtitle" style="margin-top: 15px; margin-bottom: 15px;">
        <em><strong><?php echo __('User Information'); ?></strong>: <?php echo __('Currently selected user'); ?></em>
    </div>
<?php
if (!$info['user_id'] || !($user = User::lookup($info['user_id'])))
    $user = $ticket->getUser();
?>
    <div class="form-group">
        <label class="control-label col-sm-2"><?php echo __('User'); ?>:</label>
        <div class="col-sm-10">
            <div id="client-info">
                <a href="#" onclick="javascript:
                            $.userLookup('ajax.php/users/<?php echo $ticket->getOwnerId(); ?>/edit',
                                    function (user) {
                                        $('#client-name').text(user.name);
                                        $('#client-email').text(user.email);
                                    });
                    return false;
                   "><i class="icon-user"></i>
                    <span id="client-name"><?php echo Format::htmlchars($user->getName()); ?></span>
                    &lt;<span id="client-email"><?php echo $user->getEmail(); ?></span>&gt;
                </a>
                <a class="btn btn-default btn-xs inline action-button" style="overflow:inherit" href="#"
                   onclick="javascript:
                                $.userLookup('ajax.php/tickets/<?php echo $ticket->getId(); ?>/change-user',
                                        function (user) {
                                            $('input#user_id').val(user.id);
                                            $('#client-name').text(user.name);
                                            $('#client-email').text('<' + user.email + '>');
                                        });
                        return false;
                   "><i class="icon-edit"></i> <?php echo __('Change'); ?></a>
                <input type="hidden" name="user_id" id="user_id"
                       value="<?php echo $info['user_id']; ?>" />
            </div>
        </div></div>
    <div class="clear"></div>
    <div class="bg-gray padtitle" style="margin-top: 15px; margin-bottom: 15px;">
        <em><strong><?php echo __('Ticket Information'); ?></strong>: <?php echo __("Due date overrides SLA's grace period."); ?></em>
    </div>

    <div class="form-group form-inline">
        <label class="control-label col-sm-2">
<?php echo __('Ticket Source'); ?>:</label>
        <div class="col-sm-10">
            <select class="form-control ninty4" name="source">
                <option value="" selected >&mdash; <?php echo __('Select Source'); ?> &mdash;</option>
<?php
$source = $info['source'] ?: 'Phone';
foreach (Ticket::getSources() as $k => $v) {
    echo sprintf('<option value="%s" %s>%s</option>', $k, ($source == $k ) ? 'selected="selected"' : '', $v);
}
?>
            </select>
            <font class="error"><b>*</b>&nbsp;<?php echo $errors['source']; ?></font>
        </div></div>
    <div class="form-group form-inline">
        <label class="control-label col-sm-2">
                <?php echo __('Help Topic'); ?>:</label>
        <div class="col-sm-10">
            <select class="form-control ninty4" name="topicId">
                <option value="" selected >&mdash; <?php echo __('Select Help Topic'); ?> &mdash;</option>
<?php
if ($topics=$thisstaff->getTopicNames()) {
       if($ticket->topic_id && !array_key_exists($ticket->topic_id, $topics)) {
                        $topics[$ticket->topic_id] = $ticket->topic;
                        $errors['topicId'] = sprintf(__('%s selected must be active'), __('Help Topic'));
                      }
    foreach ($topics as $id => $name) {
        echo sprintf('<option value="%d" %s>%s</option>', $id, ($info['topicId'] == $id) ? 'selected="selected"' : '', $name);
    }
}
?>
            </select>
             <?php
                if (!$info['topicId'] && $cfg->requireTopicToClose()) {
                ?><i class="icon-warning-sign help-tip warning"
                    data-title="<?php echo __('Required to close ticket'); ?>"
                    data-content="<?php echo __('Data is required in this field in order to close the related ticket'); ?>"
                ></i><?php
                } ?>
            <font class="error"><b>*</b>&nbsp;<?php echo $errors['topicId']; ?></font><br>
        </div></div>
    <div class="form-group">
        <label class="control-label col-sm-2">
                <?php echo __('SLA Plan'); ?>:</label>
        <div class="col-sm-10">
            <select class="form-control ninty4" name="slaId">
                <option value="0" selected="selected" >&mdash; <?php echo __('None'); ?> &mdash;</option>
<?php
if ($slas = SLA::getSLAs()) {
    foreach ($slas as $id => $name) {
        echo sprintf('<option value="%d" %s>%s</option>', $id, ($info['slaId'] == $id) ? 'selected="selected"' : '', $name);
    }
}
?>
            </select>
            &nbsp;<font class="error">&nbsp;<?php echo $errors['slaId']; ?></font>
        </div></div>
    <div class="form-group">
        <label class="control-label col-sm-2">
                <?php echo __('Due Date'); ?>:</label>
        <div class="col-sm-10 form-inline tedit-duedate">
                <?php
                $duedateField = Ticket::duedateField('duedate', $info['duedate']);
                $duedateField->render();
                ?>
                &nbsp;<font class="error">&nbsp;<?php echo $errors['duedate']; ?></font>
                <em><?php echo __('Time is based on your time zone');?>
                    (<?php echo $cfg->getTimezone($thisstaff); ?>)</em>
        </div></div>
    <div class="dynamic-table">
        <table class="form_table dynamic-forms" width="940" border="0" cellspacing="0" cellpadding="2">
            <?php if ($forms)
                foreach ($forms as $form) {
                   $form->render(array('staff'=>true,'mode'=>'edit','width'=>160,'entry'=>$form));
                }
            ?>
        </table>
    </div>
    <div class="bg-gray padtitle" style="margin-top: 15px; margin-bottom: 15px;">
 <em><strong><?php echo __('Internal Note');?></strong>: <?php echo __('Reason for editing the ticket (optional)');?> <font class="error">&nbsp;<?php echo $errors['note'];?></font></em>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
        <textarea class="richtext no-bar" name="note" cols="21"
                    rows="6" style="width:80%;"><?php echo Format::viewableImages($info['note']);
            ?></textarea>
        </div>   
    </div>
    <p style="text-align:center;">
        <input class="btn btn-default" type="submit" name="submit" value="<?php echo __('Save'); ?>">
        <input class="btn btn-default" type="reset"  name="reset"  value="<?php echo __('Reset'); ?>">
        <input class="btn btn-default" type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick='window.location.href = "tickets.php?id=<?php echo $ticket->getId(); ?>"'>
    </p>
</form>
<div style="display:none;" class="dialog draggable" id="user-lookup">
    <div class="body"></div>
</div>
</div>    
<script type="text/javascript">
    +(function () {
        var I = setInterval(function () {
            if (!$.fn.sortable)
                return;
            clearInterval(I);
            $('table.dynamic-forms').sortable({
                items: 'tbody',
                handle: 'th',
                helper: function (e, ui) {
                    ui.children().each(function () {
                        $(this).children().each(function () {
                            $(this).width($(this).width());
                        });
                    });
                    ui = ui.clone().css({'background-color': 'white', 'opacity': 0.8});
                    return ui;
                }
            });
        }, 20);
    })();
</script>

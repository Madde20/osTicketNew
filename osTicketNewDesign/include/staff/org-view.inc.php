<?php
if(!defined('OSTSCPINC') || !$thisstaff || !is_object($org)) die('Invalid path');

?>
<div class="col-md-12">
<div class="row org-list">
            <div class="col-md-6 col-sm-4 bcolor">
             <h2><a href="orgs.php?id=<?php echo $org->getId(); ?>"
             title="Reload"><i class="icon-refresh"></i> <?php echo $org->getName(); ?></a></h2>
            </div>
            <div class="col-md-6 col-sm-8 bcolor">
            <div class="pull-right">
<?php if ($thisstaff->hasPerm(Organization::PERM_DELETE)) { ?>
            <a id="org-delete" class="btn btn-default btn-xs red button action-button org-action"
            href="#orgs/<?php echo $org->getId(); ?>/delete"><i class="icon-trash"></i>
            <?php echo __('Delete Organization'); ?></a>
<?php } ?>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
            <span class="btn btn-default btn-xs action-button" data-dropdown="#action-dropdown-more">
                <i class="icon-caret-down pull-right"></i>
                <span ><i class="icon-cog"></i> <?php echo __('More'); ?></span>
            </span>
<?php } ?>
            <div id="action-dropdown-more" class="action-dropdown anchor-right">
              <ul>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                <li><a href="#ajax.php/orgs/<?php echo $org->getId();
                    ?>/forms/manage" onclick="javascript:
                    $.dialog($(this).attr('href').substr(1), 201);
                    return false"
                    ><i class="icon-paste"></i>
                    <?php echo __('Manage Forms'); ?></a></li>
<?php } ?>
              </ul>
            </div>
            </div>
            </div> </div></div>
<div class="clear"></div>
<br><br>
<div class="row">
<div class="col-md-6 col-sm-4"> 
    <div class="panel panel-info">
         <div class="panel-body">
            <table class="table table-striped">
                <tr>
                    <th width="150"><?php echo __('Name'); ?>:</th>
                    <td>
<?php if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    <b><a href="#orgs/<?php echo $org->getId();
                    ?>/edit" class="org-action"><i
                        class="icon-edit"></i>
<?php }
                    echo $org->getName();
    if ($thisstaff->hasPerm(Organization::PERM_EDIT)) { ?>
                    </a></b>
<?php } ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Account Manager'); ?>:</th>
                    <td><?php echo $org->getAccountManager(); ?>&nbsp;</td>
                </tr>
            </table>
            </div></div></div>
            <div class="col-md-6 col-sm-8">
                <div class="panel panel-info">
         <div class="panel-body">
            <table class="table table-striped">
                <tr>
                    <th width="150"><?php echo __('Created'); ?>:</th>
                    <td><?php echo Format::datetime($org->getCreateDate()); ?></td>
                </tr>
                <tr>
                    <th><?php echo __('Last Updated'); ?>:</th>
                    <td><?php echo Format::datetime($org->getUpdateDate()); ?></td>
                </tr>
            </table>
            </div></div></div>
 </div>    
<div class="clear"></div>


<br>
<div class="clear"></div>
<ul class="clean nav nav-tabs" id="orgtabs">
    <li class="active"><a data-toggle="tab" href="#users"><i
    class="icon-user"></i>&nbsp;<?php echo __('Users'); ?></a></li>
    <li><a data-toggle="tab" href="#tickets"><i
    class="icon-list-alt"></i>&nbsp;<?php echo __('Tickets'); ?></a></li>
    <li><a data-toggle="tab" href="#notes"><i
    class="icon-pushpin"></i>&nbsp;<?php echo __('Notes'); ?></a></li>
</ul>
<div id="orgtabs_container" class="table-responsive tab-content">
<div class="tab-pane fade in active" id="users">
    <br>
<?php
include STAFFINC_DIR . 'templates/users.tmpl.php';
?>
</div>
<div class="tab-pane fade" id="tickets">
<?php
include STAFFINC_DIR . 'templates/tickets.tmpl.php';
?>
</div>

<div class="tab-pane fade" id="notes">
<?php
$notes = QuickNote::forOrganization($org);
$create_note_url = 'orgs/'.$org->getId().'/note';
include STAFFINC_DIR . 'templates/notes.tmpl.php';
?>
</div>
</div>

<script type="text/javascript">
$(function() {
    $(document).on('click', 'a.org-action', function(e) {
        e.preventDefault();
        var url = 'ajax.php/'+$(this).attr('href').substr(1);
        $.dialog(url, [201, 204], function (xhr) {
            if (xhr.status == 204)
                window.location.href = 'orgs.php';
            else
                window.location.href = window.location.href;
         }, {
            onshow: function() { $('#org-search').focus(); }
         });
        return false;
    });
});
</script>

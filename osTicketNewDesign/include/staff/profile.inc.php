<?php
if (!defined('OSTSTAFFINC') || !$staff || !$thisstaff)
    die('Access Denied');
?>

<form action="profile.php" method="post" class="save form-horizontal profile-form" autocomplete="off">
<?php csrf_token(); ?>
    <input type="hidden" name="do" value="update">
    <input type="hidden" name="id" value="<?php echo $staff->getId(); ?>">
    <div class="col-md-12">
        <h2><?php echo __('My Account Profile'); ?></h2>
        <ul class="clean nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#account"><i class="icon-user"></i> <?php echo __('Account'); ?></a></li>
            <li><a data-toggle="tab" href="#preferences"><?php echo __('Preferences'); ?></a></li>
            <li><a data-toggle="tab" href="#signature"><?php echo __('Signature'); ?></a></li>
        </ul>
    </div>
    <div class="col-md-12">
        <div class="tab-content">
            <div class="tab-pane fade in active tab_content" id="account">
                <div class="row">
                    <div class="col-md-2 col-sm-1 avatar">
                        <?php
                        $avatar = $staff->getAvatar();
                        echo $avatar;
                        if ($avatar->isChangeable()) {
                            ?>
                            <div style="text-align: center">
                                <a class="button no-pjax"
                                   href="#ajax.php/staff/<?php echo $staff->getId(); ?>/avatar/change"
                                   onclick="javascript:
                                    event.preventDefault();
                            var $a = $(this),
                                    form = $a.closest('form');
                            $.ajax({
                                url: $a.attr('href').substr(1),
                                dataType: 'json',
                                success: function (json) {
                                    if (!json || !json.code)
                                        return;
                                    var code = form.find('[name=avatar_code]');
                                    if (!code.length)
                                        code = form.append($('<input>').attr({type: 'hidden', name: 'avatar_code'}));
                                    code.val(json.code).trigger('change');
                                    $a.closest('.avatar').find('img').replaceWith($(json.img));
                                }
                            });
                            return false;"><i class="icon-retweet"></i></a>
                            </div>
    <?php }
?>
                    </div>
                    <div class="col-md-10 col-sm-11"><div class="form-group"><div class="col-sm-2"> <label><?php echo __('Name'); ?>:</label></div>
                            <div class="col-sm-10"> <div class="form-group"> <div class="col-md-4 col-xs-6"><input class="form-control" type="text" size="20" maxlength="64" name="firstname"
                                                                                    autofocus value="<?php echo Format::htmlchars($staff->firstname); ?>"
                                                                                    placeholder="<?php echo __("First Name"); ?>" /></div>
                                    <div class="col-md-4 col-xs-6"><input class="form-control" type="text" size="20" maxlength="64" name="lastname"
                                           value="<?php echo Format::htmlchars($staff->lastname); ?>"
                                           placeholder="<?php echo __("Last Name"); ?>" /></div>
                                    <div class="error"><?php echo $errors['firstname']; ?></div>
                                    <div class="error"><?php echo $errors['lastname']; ?></div>
                                </div> </div></div>
                        <div class="form-group"><div class="col-sm-2"> <label><?php echo __('Email Address'); ?>:</label></div>
                            <div class="col-sm-10"> <input class="form-control" type="email" size="40" maxlength="64" style="width: 300px" name="email"
                                                           value="<?php echo Format::htmlchars($staff->email); ?>"
                                                           placeholder="<?php echo __('e.g. me@mycompany.com'); ?>" />
                                <div class="error"><?php echo $errors['email']; ?></div>
                            </div></div><div class="form-group">
                            <div class="col-sm-2"><?php echo __('Phone Number'); ?>:</div>
                            <div class="col-sm-10"><div class="form-inline"><input class="form-control" type="tel" size="18" name="phone" class="auto phone"
                                                                                   value="<?php echo Format::htmlchars($staff->phone); ?>" />
<?php echo __('Ext'); ?>
                                    <input class="form-control" type="text" size="5" name="phone_ext"
                                           value="<?php echo Format::htmlchars($staff->phone_ext); ?>"></div>
                                <div class="error"><?php echo $errors['phone']; ?></div>
                                <div class="error"><?php echo $errors['phone_ext']; ?></div></div>
                        </div>

                        <div class="form-group"><div class="col-sm-2"><?php echo __('Mobile Number'); ?>:</div>

                            <div class="col-sm-10"> <input class="form-control" type="tel" size="18" maxlength="64" style="width: 300px" name="mobile" class="auto phone"
                                                           value="<?php echo Format::htmlchars($staff->mobile); ?>" />
                                <div class="error"><?php echo $errors['mobile']; ?></div> </div>
                        </div>  

                    </div>
                    <!-- ================================================ -->

                    <div class="col-md-12">

                        <h3> <?php echo __('Authentication'); ?> </h3>
                        <hr>


<?php if ($bk = $staff->getAuthBackend()) { ?>
                            <div>
                                <div><?php echo __("Backend"); ?></div>
                                <div><?php echo $bk->getName(); ?></div>
                            </div>
<?php } ?>
                        <div class="form-group"><div class="col-sm-2">
                                <label><?php echo __('Username'); ?>:</label>
                                <span class="error">*</span></div>
                            <div class="col-sm-10"><div class="form-inline">
                                    <input type="text" size="40" style="width:300px" 
                                           class="staff-username typeahead form-control"
                                           name="username" disabled value="<?php echo Format::htmlchars($staff->username); ?>" />
<?php if (!$bk || $bk->supportsPasswordChange()) { ?>
                                        <button type="button" id="change-pw-button" class="action-button btn" onclick="javascript:
                                $.dialog('ajax.php/staff/' +<?php echo $staff->getId(); ?> + '/change-password', 201);">
                                            <i class="icon-refresh"></i> <?php echo __('Change Password'); ?>
                                        </button>
<?php } ?>
                                    <i class="offset help-tip icon-question-sign" href="#username"></i></div>
                                <div class="error"><?php echo $errors['username']; ?></div>
                            </div> </div>
                        
<?php
if (($bks=Staff2FABackend::allRegistered())) {
    $current = $staff->get2FABackendId();
    $required2fa = $cfg->require2FAForAgents();
    $_config = $staff->getConfig();
?>
         <div class="form-group"><div class="col-sm-2">
          <label <?php if ($required2fa) echo 'class="required"'; ?>><?php echo __('Default 2FA'); ?>:</label>
          </div>
              <div class="col-sm-10"><div class="form-inline">
            <select name="default_2fa" id="default2fa-selection"
              style="width:300px">
              <?php
              if (!$required2fa) { ?>
              <option value="">&mdash; <?php echo __('Disable'); ?> &mdash;</option>
              <?php
              }
             foreach ($bks as $bk) {
                 $configuration = $staff->get2FAConfig($bk->getId());
                 $configured = $configuration['verified'];
                 ?>
              <option id="<?php echo $bk->getId(); ?>"
                      value="<?php echo $bk->getId(); ?>" <?php
                if ($current == $bk->getId() && $configured)
                  echo ' selected="selected" '; ?>
                <?php
                if (!$configured)
                   echo ' disabled="disabled" '; ?>
                 ><?php
                echo $bk->getName(); ?></option>
             <?php } ?>
            </select>
            &nbsp;
            <button type="button" id="config2fa-button" class="action-button" onclick="javascript:
            $.dialog('ajax.php/staff/'+<?php echo $staff->getId();
                    ?>+'/2fa/configure', 201);">
              <i class="icon-gear"></i> <?php echo __('Configure Options'); ?>
            </button>
            <i class="offset help-tip icon-question-sign" href="#config2fa"></i> </div>
            <div class="error"><?php echo $errors['default_2fa']; ?></div>
           </div>
        </div>
<?php
} ?>                        

                        <!-- ================================================ -->

                        <h3><?php echo __('Status and Settings'); ?></h3>
                        <hr>

                        <div class="checkbox"> <label>
                                <input type="checkbox" name="onvacation"
<?php echo ($staff->onvacation) ? 'checked="checked"' : ''; ?> />
<?php echo __('Vacation Mode'); ?>
                            </label></div>
                        <br/>
                    </div>
                </div>
            </div>
            <!-- =================== PREFERENCES ======================== -->

            <div class="tab-pane fade tab_content" id="preferences">

                <h3> <?php echo __('Preferences'); ?> </h3>
                <h5><?php
                    echo __(
                            "Profile preferences and settings"
                    );
?></h5>
                <hr>
                <div class="form-group">
                    <div class="col-sm-4"><?php echo __('Maximum Page size'); ?>:</div>
                    <div class="col-sm-8 form-inline"> <select name="max_page_size" class="form-control">
                            <option value="0">&mdash; <?php echo __('System Default'); ?> &mdash;</option>
                            <?php
                            $pagelimit = $staff->max_page_size ?: $cfg->getPageSize();
                            for ($i = 5; $i <= 50; $i += 5) {
                                $sel = ($pagelimit == $i) ? 'selected="selected"' : '';
                                echo sprintf('<option value="%d" %s>' . __('show %s records') . '</option>', $i, $sel, $i);
                            }
                            ?>
                        </select> <?php echo __('per page.'); ?> </div></div>
                <div class="form-group">
                    <div class="col-sm-4"> <?php echo __('Auto Refresh Rate'); ?>:
                        <div class="faded"><?php echo __('Tickets page refresh rate in minutes.'); ?></div></div>
                    <div class="col-sm-8"> <select name="auto_refresh_rate" class="form-control">
                            <option value="0">&mdash; <?php echo __('Disabled'); ?> &mdash;</option>
                            <?php
                            $y = 1;
                            for ($i = 1; $i <= 30; $i += $y) {
                                $sel = ($staff->auto_refresh_rate == $i) ? 'selected="selected"' : '';
                                     echo sprintf('<option value="%d" %s>%s</option>', $i, $sel,
                                       @sprintf(_N('Every minute', 'Every %d minutes', $i), $i));
                                if ($i > 9)
                                    $y = 2;
                            }
                            ?>
                        </select></div></div>
                <div class="form-group">
                    <div class="col-sm-4"><?php echo __('Default From Name'); ?>:
                        <div class="faded"><?php echo __('From name to use when replying to a thread'); ?></div></div>
                    <div class="col-sm-8">  <select name="default_from_name" class="form-control">
                            <?php
                            $options = array(
                                'email' => __("Email Address Name"),
                                'dept' => sprintf(__("Department Name (%s)"), __('if public' /* This is used in 'Department's Name (>if public<)' */)),
                                'mine' => __('My Name'),
                                '' => '— ' . __('System Default') . ' —',
                            );
                            if ($cfg->hideStaffName())
                                unset($options['mine']);

                            foreach ($options as $k => $v) {
                                echo sprintf('<option value="%s" %s>%s</option>', 
                                         $k,($staff->default_from_name && $staff->default_from_name==$k)?'selected="selected"':'',$v);
                            }
                            ?>
                        </select>
                        <div class="error"><?php echo $errors['default_from_name']; ?></div></div></div>
                        
                                    <div class="form-group">
               <div class="col-sm-4"> <?php echo __('Default Ticket Queue'); ?>:</div>
               <div class="col-sm-8">  <select name="default_ticket_queue_id">
                 <option value="0">&mdash; <?php echo __('system default');?> &mdash;</option>
                 <?php
                 $queues = CustomQueue::queues()
                    ->filter(Q::any(array(
                        'flags__hasbit' => CustomQueue::FLAG_PUBLIC,
                        'staff_id' => $thisstaff->getId(),
                    )))
                    ->all();
                 foreach ($queues as $q) { ?>
                  <option value="<?php echo $q->id; ?>" <?php
                    if ($q->getId() == $staff->default_ticket_queue_id) echo 'selected="selected"'; ?> >
                   <?php echo $q->getFullName(); ?></option>
                 <?php
                 } ?>
                </select>
</div></div>                        
                <div class="form-group">
                    <div class="col-sm-4"> <?php echo __('Thread View Order'); ?>:
                        <div class="faded"><?php echo __('The order of thread entries'); ?></div></div>
                    <div class="col-sm-8"> <select name="thread_view_order" class="form-control">
                            <?php
                            $options = array(
                                'desc' => __('Descending'),
                                'asc' => __('Ascending'),
                                '' => '— ' . __('System Default') . ' —',
                            );
                            foreach ($options as $k => $v) {
                                echo sprintf('<option value="%s" %s>%s</option>', $k
                                        , ($staff->thread_view_order == $k) ? 'selected="selected"' : ''
                                        , $v);
                            }
                            ?>
                        </select>
                        <div class="error"><?php echo $errors['thread_view_order']; ?></div></div></div>
                <div class="form-group">
                    <div class="col-sm-4"> <?php echo __('Default Signature'); ?>:
                        <div class="faded"><?php echo __('This can be selected when replying to a thread'); ?></div></div>
                    <div class="col-sm-8"><select name="default_signature_type" class="form-control">
                            <option value="none" selected="selected">&mdash; <?php echo __('None'); ?> &mdash;</option>
                            <?php
                            $options = array('mine' => __('My Signature'), 'dept' => sprintf(__('Department Signature (%s)'), __('if set' /* This is used in 'Department Signature (>if set<)' */)));
                            foreach ($options as $k => $v) {
                                echo sprintf('<option value="%s" %s>%s</option>', $k, ($staff->default_signature_type == $k) ? 'selected="selected"' : '', $v);
                            }
                            ?>
                        </select>
                        <div class="error"><?php echo $errors['default_signature_type']; ?></div></div></div>
                <div class="form-group">
                    <div class="col-sm-4"><?php echo __('Default Paper Size'); ?>:
                        <div class="faded"><?php echo __('Paper size used when printing tickets to PDF'); ?></div></div>
                    <div class="col-sm-8"> <select name="default_paper_size" class="form-control">
                            <option value="none" selected="selected">&mdash; <?php echo __('None'); ?> &mdash;</option>
                            <?php
                            foreach (Export::$paper_sizes as $v) {
                                echo sprintf('<option value="%s" %s>%s</option>', $v, ($staff->default_paper_size == $v) ? 'selected="selected"' : '', __($v));
                            }
                            ?>
                        </select>
                        <div class="error"><?php echo $errors['default_paper_size']; ?></div></div></div>
                        
                                <div class="form-group">
                                    <div class="col-sm-4"><?php echo __('Reply Redirect'); ?>:
                <div class="faded"><?php echo __('Redirect URL used after replying to a ticket.');?></div></div>
              <div class="col-sm-8"> 
                <select name="reply_redirect">
                  <?php
                  $options=array('Queue'=>__('Queue'),'Ticket'=>__('Ticket'));
                  foreach($options as $key=>$opt) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $key,($staff->reply_redirect==$key)?'selected="selected"':'',$opt);
                  }
                  ?>
                </select>
                <div class="error"><?php echo $errors['reply_redirect']; ?></div></div></div>
                
           <div class="form-group">
           <div class="col-sm-4"><?php echo __('Image Attachment View'); ?>:
                <div class="faded"><?php echo __('Open image attachments in new tab or directly download. (CTRL + Right Click)');?></div>
           </div>
             <div class="col-sm-8">
                <select name="img_att_view">
                  <?php
                  $options=array('download'=>__('Download'),'inline'=>__('Inline'));
                  foreach($options as $key=>$opt) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $key,($staff->img_att_view==$key)?'selected="selected"':'',$opt);
                  }
                  ?>
                </select>
                <div class="error"><?php echo $errors['img_att_view']; ?></div>
            </div></div>
                
             <div class="form-group">
           <div class="col-sm-4"><?php echo __('Editor Spacing'); ?>:
                <div class="faded"><?php echo __('Set the editor spacing to Single or Double when pressing Enter.');?></div>
           </div>
             <div class="col-sm-8">
                <select name="editor_spacing">
                  <?php
                  $options=array('double'=>__('Double'),'single'=>__('Single'));
                  $spacing = $staff->editor_spacing;
                  foreach($options as $key=>$opt) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $key,($spacing==$key)?'selected="selected"':'',$opt);
                  }
                  ?>
                </select>
                <div class="error"><?php echo $errors['editor_spacing']; ?></div>
            </div></div>    
                

                <h3> <?php echo __('Localization'); ?></h3>
                <hr>
                <div class="form-group">
                    <div class="col-sm-2"> <?php echo __('Time Zone'); ?>:</div>
                    <div class="col-sm-10"> <?php
                            $TZ_NAME = 'timezone';
                            $TZ_TIMEZONE = $staff->timezone;
                            include STAFFINC_DIR . 'templates/timezone.tmpl.php';
                            ?>
                        <div class="error"><?php echo $errors['timezone']; ?></div></div></div>
                <div class="form-group">
                    <div class="col-sm-2"><?php echo __('Time Format'); ?>:</div>
                    <div class="col-sm-10"> <select name="datetime_format" class="form-control">
                            <?php
                            $datetime_format = $staff->datetime_format;
                            foreach (array(
                        'relative' => __('Relative Time'),
                        '' => '— ' . __('System Default') . ' —',
                            ) as $v => $name) {
                                ?>
                                <option value="<?php echo $v; ?>" <?php
                                if ($v == $datetime_format)
                                    echo 'selected="selected"';
                                ?>><?php echo $name; ?></option>
<?php } ?>
                        </select> </div></div>

<?php if ($cfg->getSecondaryLanguages()) { ?>
                    <div class="form-group">
                        <div class="col-sm-2">
                        <?php echo __('Preferred Language'); ?>:</div>

                        <?php $langs = Internationalization::getConfiguredSystemLanguages(); ?>
                        <div class="col-sm-10"><select name="lang" class="form-control">
                                <option value="">&mdash; <?php echo __('Use Browser Preference'); ?> &mdash;</option>
                                <?php foreach ($langs as $l) {
                                    $selected = ($staff->lang == $l['code']) ? 'selected="selected"' : '';
                                    ?>
                                    <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                                    ?>><?php echo Internationalization::getLanguageDescription($l['code']); ?></option>
    <?php } ?>
                            </select>
                            <span class="error">&nbsp;<?php echo $errors['lang']; ?></span>
                        </div></div>
                <?php } ?>
<?php if (extension_loaded('intl')) { ?>
                    <div class="form-group">
                        <div class="col-sm-2">
    <?php echo __('Preferred Locale'); ?>:</div>
                        <div class="col-sm-10">    <select name="locale" class="form-control">
                                <option value=""><?php echo __('Use Language Preference'); ?></option>
                                <?php foreach (Internationalization::allLocales() as $code => $name) { ?>
                                    <option value="<?php echo $code; ?>" <?php
                                    if ($code == $staff->locale)
                                        echo 'selected="selected"';
                                    ?>><?php echo $name; ?></option>
                    <?php } ?>
                            </select></div></div>
<?php } ?>
            </div>

            <!-- ==================== SIGNATURES ======================== -->

            <div id="signature" class="tab-pane fade">
                <h3><?php echo __('Signature'); ?></h3>
                <div class="form-group"> 
                <div class="col-sm-12"><small><?php
                        echo __(
                                "Optional signature used on outgoing emails.")
                        . ' ' .
                        __('Signature is made available as a choice, on ticket reply.');
                        ?>
                    </small></div>
                <div class="col-sm-12">
                    <textarea class="richtext no-bar form-control" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?php echo Format::viewableImages(Format::htmlchars($staff->signature, true)); ?></textarea>
                </div></div>
            </div>

            <p style="text-align:center;">
                <button class="button action-button btn btn-default" type="submit" name="submit" ><i class="icon-save"></i> <?php echo __('Save Changes'); ?></button>
                <button class="button action-button btn btn-default" type="reset"  name="reset"><i class="icon-undo"></i>
<?php echo __('Reset'); ?></button>
                <button class="red button action-button btn btn-default" type="button" name="cancel" onclick="window.history.go(-1);"><i class="icon-remove-circle"></i> <?php echo __('Cancel'); ?></button>
            </p>
            <div class="clear"></div>
            </form>
        </div></div>
<?php if ($staff->change_passwd) { ?>
        <script type="text/javascript">
            $(function () {
                $('#change-pw-button').trigger('click'); });
        </script>
        <?php
    }

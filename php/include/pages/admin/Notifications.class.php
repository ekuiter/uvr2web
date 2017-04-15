<?php

/**
 * Contains Notifications class
 *
 * @package AdminPages
 */
 
/**
 * Notifications page
 *
 * Allows to manage notifications.
 *
 * @package AdminPages
 */

class Notifications {

  /**
   * Gets page title 
   */
  public function title() {
    return Loc::t('notifications');
  }
  
  /**
   * Renders the notifications section
   */
  public function render() {
    if ($_POST)
      $this->save();
    $notifications_body = Loc::t('notifications body');
    $notifications_body_2 = Loc::t('notifications body 2');
    $notifications_body_3 = Loc::t('notifications body 3');
    $notifications_body_4 = Loc::t('notifications body 4');
    $notifications_body_5 = Loc::t('notifications body 5');
    $comma_separated = Loc::t('comma-separated');
    $save = Loc::t('save');
    $cancel = Loc::t('cancel');
    $email_number = count(Notifier::get_emails());
    $emails = $email_number == 1 ? Loc::t('email') : Loc::t('emails');
    $emails_string = Notifier::get_emails_string();
    $no_upload_notification = new NoUploadNotification();
    $no_upload_checked = $no_upload_notification->get_enabled() ? 'checked' : '';
    $no_upload_time = $no_upload_notification->get_time();
    $backup_notification = new BackupNotification();
    $backup_checked = $backup_notification->get_enabled() ? 'checked' : '';
    $backup_time = $backup_notification->get_time();
    $trigger_notification = new TriggerNotification();
    $trigger_checked = $trigger_notification->get_enabled() ? 'checked' : '';
    $trigger_time = $trigger_notification->get_time();
    $trigger_time_from = $trigger_time[0];
    $trigger_time_to = $trigger_time[1];
    $trigger_sensor = $trigger_notification->get_sensor();
    $trigger_mode = $trigger_notification->get_mode();
    $below_selected = $trigger_mode === "below" ? "selected" : "";
    $above_selected = $trigger_mode === "above" ? "selected" : "";
    $trigger_bound = $trigger_notification->get_bound();
    echo <<<code
    <p style="margin-bottom:30px">$notifications_body</p>
    <form method="post" action="?p=admin&sub=notifications" class="form-horizontal">
        <div class="control-group">
          <label class="control-label" for="email"><strong>$email_number</strong> $emails</label>
          <div class="controls">
            <input style="margin-right:20px;width:300px" type="text" name="email" id="email" value="$emails_string" /> <em>$comma_separated</em>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
						      <input style="margin:-3px 3px 0 0" type="checkbox" name="no_upload" id="no_upload" value="no_upload" $no_upload_checked />
            <label style="display:inline" for="no_upload">$notifications_body_2 <input style="width:40px;margin:0 5px 0 5px" type="text" name="no_upload_time" id="no_upload_time" value="$no_upload_time" /> $notifications_body_3</label>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input style="margin:-3px 3px 0 0" type="checkbox" name="backup" id="backup" value="backup" $backup_checked />
            <label style="display:inline" for="backup">$notifications_body_4 <input style="width:40px;margin:0 5px 0 5px" type="text" name="backup_time" id="backup_time" value="$backup_time" /> $notifications_body_5</label>
          </div>
        </div>
	<div class="control-group">
          <div class="controls">
            <input style="margin:-3px 3px 0 0" type="checkbox" name="trigger" id="trigger" value="trigger" $trigger_checked />
		<label style="display:inline" for="trigger">
		From <input style="width:40px;margin:0 5px 0 5px" type="text" name="trigger_time_from" id="trigger_time_from" value="$trigger_time_from" />
		  to <input style="width:40px;margin:0 5px 0 5px" type="text" name="trigger_time_to" id="trigger_time_to" value="$trigger_time_to" /> o'clock,
		  notify me when the sensor
		  <input style="width:40px;margin:0 5px 0 5px" type="text" name="trigger_sensor" id="trigger_sensor" value="$trigger_sensor" />
		  is <select style="width:80px;margin:0 5px 0 5px" type="text" name="trigger_mode" id="trigger_mode">
                  <option value="below" $below_selected>below</option><option value="above" $above_selected>above</option></select>
		  the value <input style="width:40px;margin:0 5px 0 5px" type="text" name="trigger_bound" id="trigger_bound" value="$trigger_bound" />.</label>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="submit" value="$save" class="btn btn-primary" />
            <a href="?p=admin&sub=notifications" class="btn">$cancel</a>
          </div>
        </div>
      </form>
code;
  }
  
  /*
   * Saves notification settings
   */
  function save() {
    if (isset($_POST['email']))
      Notifier::set_emails_string($_POST['email']);
    $no_upload_notification = new NoUploadNotification();
    if (isset($_POST['no_upload']))
      $enabled = true;
    else
      $enabled = false;
    $no_upload_notification->set_enabled($enabled);
    if (isset($_POST['no_upload_time']))
      $no_upload_notification->set_time($_POST['no_upload_time']);
    $backup_notification = new BackupNotification();
    if (isset($_POST['backup']))
      $enabled = true;
    else
      $enabled = false;
    $backup_notification->set_enabled($enabled);
    if (isset($_POST['backup_time']))
      $backup_notification->set_time($_POST['backup_time']);
    $trigger_notification = new TriggerNotification();
    if (isset($_POST['trigger']))
      $enabled = true;
    else
      $enabled = false;
    $trigger_notification->set_enabled($enabled);
    if (isset($_POST['trigger_time_from']) && isset($_POST['trigger_time_to']))
      $trigger_notification->set_time(array((int) $_POST['trigger_time_from'], (int) $_POST['trigger_time_to']));
    if (isset($_POST['trigger_sensor']))
      $trigger_notification->set_sensor($_POST['trigger_sensor']);
    if (isset($_POST['trigger_mode']))
      $trigger_notification->set_mode($_POST['trigger_mode']);
    if (isset($_POST['trigger_bound']))
      $trigger_notification->set_bound($_POST['trigger_bound']);
  }

}

?>
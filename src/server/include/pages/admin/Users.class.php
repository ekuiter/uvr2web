<?php

/**
 * Contains Users class
 *
 * @package AdminPages
 */
 
/**
 * Users admin page
 *
 * Allows to manage users.
 *
 * @package AdminPages
 */

class Users {

  /**
   * Error messages
   */
  private $errors = '';
  /**
   * Success messages
   */
  private $success = '';

  /**
   * Gets page title 
   */
  public function title() {
    return Loc::t('users');
  }
  
  /**
   * Renders the user section
   */
  public function render() {
    if (isset($_GET['add']))
      $this->add();
    else if (isset($_GET['edit']) && is_numeric($_GET['edit']))
        $this->edit($_GET['edit']);
      else if (isset($_GET['remove']) && is_numeric($_GET['remove']))
          $this->remove();
        else
          $this->index();
  }
  
  /**
   * Renders the user index
   */
  private function index() {
    $add_user = Loc::t('add user');
    $username = Loc::t('username');
    $password_hash = Loc::t('password hash');
    $role = Loc::t('role');
    $edit = Loc::t('edit');
    $remove = Loc::t('remove');
    echo <<<code
    $this->errors
    $this->success
    <ul class="nav nav-stacked nav-pills">
      <li><a href="?p=admin&sub=users&add">$add_user</a></li>
    </ul>
    <table class="table table-striped">
    <thead>
    <tr>
    <th>$username</th>
    <th>$password_hash</th>
    <th>$role</th>
    </tr>
    </thead>
    <tbody>
code;
    $result = DB::query('SELECT * FROM uvr2web_users');
    foreach ($result as $row) {
      $id = $row['id'];
      $password = substr($row['password'], 0, 5) . '......................' . substr($row['password'], 27, 5);
      $role = $row['role'] == 'admin' ? Loc::t('admin') : ($row['role'] == 'user' ? Loc::t('user') : '-');
      echo <<<code
      <tr>
      <td>$row[username]</td>
      <td>$password</td>
      <td>$role</td>
      <td><a href="?p=admin&sub=users&edit=$id"><i class="icon-pencil"></i> <span class="visible-desktop">$edit</span></a></td>
      <td><a href="?p=admin&sub=users&remove=$id"><i class="icon-remove"></i> <span class="visible-desktop">$remove</span></a></td>
      </tr>
code;
    }
    echo <<<code
    </tbody>
    </table>
code;
  }

  /**
   * Adds a new user
   */
  private function add() {
    $password = DB::escape(md5($this->generate_password()));
    DB::query("INSERT INTO uvr2web_users (username, password, role) VALUES('dummy', '$password', 'user')");
    if (DB::get_rows() > 0) {
      $this->add_success(Loc::t('add 1'));
      $result = DB::query("SELECT id FROM uvr2web_users WHERE username='dummy'");
      $this->edit($result[0]['id']);
    } else {
      $this->add_error(Loc::t('add 2'));
      $this->index();
    }
  }

  /**
   * Edits a user
   * @param int $user
   */
  private function edit($user) {
    if (isset($_POST['username'])) {
      $password = '';
      $username = DB::escape($_POST['username']);
      $role = $_POST['role'] == 'Admin' ? 'admin' : 'user';
      if ($_POST['change_password'] || $_POST['password_confirmation']) {
        if ($_POST['change_password'] == $_POST['password_confirmation'])
          $password = ", password='" . DB::escape(md5($_POST['change_password'])) . "'";
        else
          $this->add_error(Loc::t('passwords dont match'));
      }
      DB::query("UPDATE uvr2web_users SET username='$username', password='$password', role='$role' WHERE id=$user");
      if (DB::get_rows() > 0)
        $this->add_success(Loc::t('edit 1') . "<em>$username</em>" . Loc::t('edit 2'));
    }
    $result = DB::query("SELECT * FROM uvr2web_users WHERE id=$user");
    if ($result) {
      $username = $result[0]['username'];
      $user_selected = $result[0]['role'] == 'user' ? ' selected' : '';
      $admin_selected = $result[0]['role'] == 'admin' ? ' selected' : '';
      $success = $this->errors ? '' : $this->success;
      $edit_3 = Loc::t('edit 3');
      $edit_4 = Loc::t('edit 4');
      $username_loc = Loc::t('username');
      $password = Loc::t('password');
      $password_confirmation = Loc::t('password confirmation');
      $role = Loc::t('role');
      $user_loc = Loc::t('user');
      $admin = Loc::t('admin');
      $save = Loc::t('save');
      $cancel = Loc::t('cancel');
      echo <<<code
      <h3>$edit_3<em>$username</em>$edit_4</h3>
      $this->errors
      $success
      <form method="post" action="?p=admin&sub=users&edit=$user" class="form-horizontal">
        <div class="control-group">
          <label class="control-label" for="username">$username_loc</label>
          <div class="controls">
            <input type="text" name="username" id="username" value="$username" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="change_password">$password</label>
          <div class="controls">
            <input type="password" name="change_password" id="change_password" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="password_confirmation">$password_confirmation</label>
          <div class="controls">
            <input type="password" name="password_confirmation" id="password_confirmation" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="role">$role</label>
          <div class="controls">
            <select name="role" id="role" size="1">
              <option$user_selected>$user_loc</option>
              <option$admin_selected>$admin</option>
            </select>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="submit" value="$save" class="btn btn-primary" />
            <a href="?p=admin&sub=users" class="btn">$cancel</a>
          </div>
        </div>
      </form>
code;
    } else
      $this->index();

  }

  /**
   * Removes a user
   */
  private function remove() {
    if (isset($_POST['remove'])) {
      $result = DB::query("SELECT * FROM uvr2web_users WHERE id=$_GET[remove]");
      if ($result[0]['role'] == 'admin') {
        DB::query("SELECT * FROM uvr2web_users WHERE role='admin'");
        if (DB::get_rows() > 1) {
          DB::query("DELETE FROM uvr2web_users WHERE id=$_GET[remove]");
          $this->add_success(Loc::t('admin deleted'));
        } else
          $this->add_error(Loc::t('last admin'));
      } else {
        DB::query("DELETE FROM uvr2web_users WHERE id=$_GET[remove]");
        $this->add_success(Loc::t('user deleted'));
      }
      $this->index();
    } else {
      $result = DB::query("SELECT * FROM uvr2web_users WHERE id=$_GET[remove]");
      if ($result) {
        $username = $result[0]['username'];
        $remove_1 = Loc::t('remove 1');
        $remove_2 = Loc::t('remove 2');
        $remove_3 = Loc::t('remove 3');
        $remove_4 = Loc::t('remove 4');
        $remove_5 = Loc::t('remove 5');
        $sure = Loc::t('sure');
        $cancel = Loc::t('cancel');
        echo <<<code
      <h3>$remove_4<em>$username</em>$remove_5</h3>
      <form method="post" action="?p=admin&sub=users&remove=$_GET[remove]">
        <p>$remove_1<em>$username</em>$remove_2</p>
        <p>$remove_3</p>
        <input type="hidden" name="remove" />
        <input type="submit" value="$sure" class="btn btn-primary" />
        <a href="?p=admin&sub=users" class="btn">$cancel</a>
      </form>
code;
      } else
        $this->index();
    }
  }

  /**
   * Adds error message
   * @param string $message
   */
  private function add_error($message) {
    $this->errors .= <<<code
<div class="alert alert-error">
  $message
  <a href="#" class="close" data-dismiss="alert">&times;</a>
</div>
code;
  }
  
  /**
   * Adds success message
   * @param string $message
   */
  private function add_success($message) {
    $this->success .= <<<code
<div class="alert alert-success">
  $message
  <a href="#" class="close" data-dismiss="alert">&times;</a>
</div>
code;
  }

  /**
   * Generates a random password
   * @param int $length
   */
  function generate_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }
    return $result;
  }

}

?>
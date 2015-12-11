<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_plugin_page('guestbook','','plugin_guestbook');
register_plugin_page('guestbook','edit','plugin_guestbook_edit');
LoadPluginLang('guestbook', 'main', '', '', '#');

function plugin_guestbook() {
  global $template, $tpl, $twig, $userROW, $ip, $config, $mysql, $SYSTEM_FLAGS, $TemplateCache, $lang;

  $SYSTEM_FLAGS['info']['title']['group'] = $lang['guestbook']['title'];

  require_once(root . "/plugins/guestbook/lib/recaptchalib.php");
  $publickey = pluginGetVariable('guestbook','public_key');
  $privatekey = pluginGetVariable('guestbook','private_key');

  if (isset($_POST['submit'])) {

    $req_fields = explode(",", pluginGetVariable('guestbook','req_fields'));
    $errors = array();

    // anonymous user
    if (!is_array($userROW)) {

      $_POST['author'] = secure_html(convert(trim($_POST['author'])));
      if(!strlen($_POST['author']) && in_array("author", $req_fields)) $errors[] .= $lang['guestbook']['error_req_name'];

      // Check captcha
      if (pluginGetVariable('guestbook','ecaptcha')) {

        $resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
          // What happens when the CAPTCHA was entered incorrectly
          $errors[] .= $lang['guestbook']['error_req_code'];
        }
      }
    }

    $message = secure_html(convert(trim($_POST['content'])));

    // check for links
    preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:ru|su|com|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&?+=\~/-]*)?(?:#[^ '\"&]*)?$~i", $message, $find_url);
    if (isset($find_url[0])) {
      $errors[] .=  $lang['guestbook']['error_nolinks'];
    }

    preg_match_all("@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@", $message, $find_url);
    if ($find_url[0] ) {
      $errors[] .= $lang['guestbook']['error_nolinks'];
    }

    // check message length
    $minl = pluginGetVariable('guestbook','minlength');
    $maxl = pluginGetVariable('guestbook','maxlength');

    if (!strlen(trim($_POST['content'])) && in_array("content", $req_fields)) {
      $errors[] = $lang['guestbook']['error_req_text'] . ' ' . str_replace(array('{minl}', '{maxl}'), array($minl, $maxl), $lang['guestbook']['error_length_text']);
    }

    if ((strlen($message) < $minl || strlen($message) > $maxl) && in_array("content", $req_fields)) {
      $errors[] .= str_replace(array('{minl}', '{maxl}'), array($minl, $maxl), $lang['guestbook']['error_length_text']);
    }

    $message = str_replace("\r\n", "<br />", $message);

    // author
    $author = (is_array($userROW)) ? $userROW['name'] : $_POST['author'];

    // status
    $status = pluginGetVariable('guestbook','approve_msg');

    // get fields
    $data = $mysql->select("select * from " . prefix . "_guestbook_fields");
    $fields = array();

    foreach ($data as $num => $value) {
      $fields[$value['id']] = intval($value['required']);
    }

    $time = time() + ($config['date_adjust'] * 60);

    $new_rec = array(
      'postdate'  => db_squote($time),
      'message'   => db_squote($message),
      'author'    => db_squote($author),
      'ip'        => db_squote($ip),
      'status'    => db_squote($status)
    );

    foreach($fields as $fid => $freq) {
      if (!empty($_POST[$fid])) {
        $new_rec[$fid] = db_squote($_POST[$fid]);
      }
      elseif ($freq === 1) {
        $errors[] = $lang['guestbook']['error_field_required'];
      }
      else {
        $new_rec[$fid] = '';
      }
    }

    if (!count($errors)) {

      $mysql->query("INSERT INTO " . prefix . "_guestbook (" . implode(', ', array_keys($new_rec)) . ") values (" . implode(', ', array_values($new_rec)) . ")");

      $success_msg = ($status == 1) ? $lang['guestbook']['success_add_wo_approve'] : $success_msg = $lang['guestbook']['success_add'];

      $success_add[] .= $success_msg;

      // send email
      $tpath = locatePluginTemplates(array('mail_success'), 'guestbook', 1);
      $xt = $twig->loadTemplate($tpath['mail_success'] . 'mail_success.tpl');

      $send_email = pluginGetVariable('guestbook','send_email');

      $tVars = array(
        'time'  => $time,
        'message'  => $message,
        'author' => $author,
        'ip' => $ip
      );

      $mailBody = $xt->render($tVars);
      $mailSubject = $lang['guestbook']['mailSubject'];

      $send_email_array = explode(",", $send_email);
      foreach ( $send_email_array as $email ) {
        sendEmailMessage($email, $mailSubject, $mailBody, $filename = false, $mail_from = false, $ctype = 'text/html');
      }

      @header("Refresh: 2; url=" . generatePluginLink('guestbook', null, array(), array(), false, true));
    }
  }

  if ($_REQUEST['mode'] == 'del') {
    if (is_array($userROW) && ($userROW['status'] == "1")) {
      if (!is_array($mysql->record("SELECT id FROM " . prefix . "_guestbook WHERE id=" . db_squote(intval($_REQUEST['id']))))) {
        $template['vars']['mainblock'] = $lang['guestbook']['error_entry_notfound'];
        return;
      }
      $mysql->query("DELETE FROM " . prefix . "_guestbook WHERE id = " . intval($_REQUEST['id']));
    }
  }

  // pagination
  $perpage = intval(pluginGetVariable('guestbook', 'perpage'));

  if (($perpage < 1) or ($perpage > 5000)) {
    $perpage = 10;
  }

  $page = isset($params['page']) ? intval($params['page']) : intval($_REQUEST['page']);
  $page = isset($page) ? $page : 0;
  if ($page < 1)  $page = 1;
  if (!$start)  $start = ($page - 1) * $perpage;

  $total_count = $mysql-> result("SELECT COUNT(*) AS num FROM ".prefix."_guestbook WHERE status = 1");

  $PagesCount = ceil($total_count / $perpage);

  $paginationParams = checkLinkAvailable('guestbook', '')?
      array('pluginName' => 'guestbook', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
      array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'guestbook'), 'xparams' => array(), 'paginator' => array('page', 1, false));

  $tpath = locatePluginTemplates(array(':'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
  $navigations = parse_ini_file($tpath[':'] . '/variables.ini', true);

  $order = pluginGetVariable('guestbook', 'order');

  // get fields
  $fields = $mysql->select("select * from " . prefix . "_guestbook_fields");
  $tEntries = array();

  foreach ($fields as $fNum => $fRow) {
    $tEntry = array(
      'id'              => $fRow['id'],
      'name'            => $fRow['name'],
      'placeholder'     => $fRow['placeholder'],
      'default_value'   => $fRow['default_value'],
      'required'        => intval($fRow['required'])
    );
    $tEntries[] = $tEntry;
  }

  // Notify about `EDIT COMPLETE` if editComplete parameter is passed
  if (isset($_REQUEST['editComplete']) && $_REQUEST['editComplete']) {
    $success_add[] = $lang['guestbook']['success_edit'];
  }

  $tVars = array(
    'comments'    => guestbook_records($order, $start, $perpage),
    'pages'       => generatePagination($page, 1, $PagesCount, 10, $paginationParams, $navigations),
    'total_count' => $total_count,
    'perpage'     => $perpage,
    'errors'      => $errors,
    'success'     => $success_add,
    'author'      => $author,
    'ip'          => $ip,
    'message'     => $message,
    'smilies'     => (pluginGetVariable('guestbook', 'usmilies')) ? InsertSmilies('', 10) :"",
    'bbcodes'     => (pluginGetVariable('guestbook', 'ubbcodes')) ? BBCodes() :"",
    'use_captcha' => (pluginGetVariable('guestbook', 'ecaptcha')),
    'captcha'     => (pluginGetVariable('guestbook', 'ecaptcha') && !(is_array($userROW))) ? recaptcha_get_html($publickey) : '',
    'use_guests'  => (!is_array($userROW) && !pluginGetVariable('guestbook','guests')),
    'fields'      => $tEntries
  );

  $tpath = locatePluginTemplates(array('guestbook'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
  $xt = $twig->loadTemplate($tpath['guestbook'] . 'guestbook.tpl');
  $template['vars']['mainblock'] = $xt->render($tVars);
}


function guestbook_records($order, $start, $perpage) {
  global $mysql, $tpl, $userROW, $config, $parse;

  foreach ($mysql->select("SELECT * FROM ".prefix."_guestbook WHERE status = 1 ORDER BY id {$order} LIMIT {$start}, {$perpage}") as $row) {
    if (pluginGetVariable('guestbook','usmilies')) { $row['message'] = $parse -> smilies($row['message']); }
    if (pluginGetVariable('guestbook','ubbcodes'))  { $row['message'] = $parse -> bbcodes($row['message']); }

    $editlink = generateLink('core', 'plugin', array('plugin' => 'guestbook', 'handler' => 'edit'), array('id' => $row['id']));
    $dellink = generateLink('core', 'plugin', array('plugin' => 'guestbook'), array('mode' => 'del', 'id' => $row['id']));
    $comnum++;

    // get fields
    $data = $mysql->select("select * from " . prefix . "_guestbook_fields");
    $fields = array();

    foreach ($data as $num => $value) {
      $fields[$value['id']] = $value['name'];
    }

    $comment_fields = array();
    foreach($fields as $fid => $fname) {
      $comment_fields[] = array(
        'id'      => $fid,
        'name'    => $fname,
        'value'   => $row[$fid],
      );
    }

    // set date format
    $date_format = pluginGetVariable('guestbook', 'date');
    if (empty($date_format)) {
      $date_format = 'j Q Y';
    }

    $comments[] = array(
      'date'    => LangDate($date_format, $row['postdate']),
      'message' => $row['message'],
      'answer'  => $row['answer'],
      'author'  => $row['author'],
      'ip'      => $row['ip'],
      'comnum'  => $comnum,
      'edit'    => $editlink,
      'del'     => $dellink,
      'fields'  => $comment_fields
    );

  }
  return $comments;
}

function plugin_guestbook_edit($mid) {
  global $template, $tpl, $userROW, $ip, $config, $mysql, $twig, $lang;

  $id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : $mid;
  if (empty($id)) return FALSE;

  $tpath = locatePluginTemplates(array('edit'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
  $xt = $twig->loadTemplate($tpath['edit'] . 'edit.tpl');

  // admin permission is required to edit messages
  if (is_array($userROW) && $userROW['status'] == "1") {

    // get fields
    $fdata = $mysql->select("SELECT * FROM " . prefix . "_guestbook_fields");

    // submit edit
    if (isSet($_REQUEST['go'])) {
      $author   = secure_html(convert(trim($_REQUEST['author'])));
      $message  = secure_html(convert(trim($_REQUEST['content'])));
      $answer   = secure_html(convert(trim($_REQUEST['answer'])));
      $message  = str_replace("\r\n", "<br />", $message);

      if (empty($author) || empty($message) ) {
        $errors[] = $lang['guestbook']['error_field_required'];
      }

      $upd_rec = array(
        'message' => db_squote($message),
        'answer'  => db_squote($answer),
        'author'  => db_squote($author)
      );

      foreach ($fdata as $fnum => $frow) {
        if (!empty($_REQUEST[$frow['id']])) {
          $upd_rec[$frow['id']] = db_squote($_REQUEST[$frow['id']]);
        }
        elseif (intval($frow['required']) === 1) {
          $errors[] = $lang['guestbook']['error_field_required'];
        }
        else {
          $upd_rec[$frow['id']] = "''";
        }
      }

      // prepare query
      $upd_str = '';
      $count = 0;
      foreach ($upd_rec as $k => $v) {
        $upd_str .= $k . '=' . $v;
        $count++;
        if ($count < count($upd_rec)) {
          $upd_str .= ', ';
        }
      }

      if (!count($errors)) {
        $mysql->query('UPDATE ' . prefix . '_guestbook SET ' . $upd_str . ' WHERE id = \'' . intval($id) . '\' ');
        @header("Location: " . generatePluginLink('guestbook', null, array(), array('editComplete' => 1)));
        exit;
      } else {
        msg(array("type" => "error", "text" => implode($errors)));
        return;
      }
    }
    else {

      if (!is_array($row = $mysql->record("SELECT * FROM " . prefix . "_guestbook WHERE id=" . db_squote(intval($id))))) {
        $tVars = array(
          'error'    =>  $lang['guestbook']['error_no_entry']
        );

        $template['vars']['mainblock'] = $xt->render($tVars);
        return;
      }

      $row['message'] = str_replace("<br />", "\r\n", $row['message']);
      $row['answer']  = str_replace("<br />", "\r\n", $row['answer']);

      // output fields data
      $tFields = array();
      foreach ($fdata as $fnum => $frow) {
        $tField = array(
          'id'              => $frow['id'],
          'name'            => $frow['name'],
          'placeholder'     => $frow['placeholder'],
          'default_value'   => $frow['default_value'],
          'required'        => intval($frow['required']),
          'value'           => $row[$frow['id']]
        );
        $tFields[] = $tField;
      }

      $tVars = array(
        'author'    => $row['author'],
        'answer'    => $row['answer'],
        'message'   => $row['message'],
        'id'        => $row['id'],
        'fields'    => $tFields,
        'error'     => ''
      );

      $template['vars']['mainblock'] = $xt->render($tVars);
    }
  }
  else {

    $tVars = array(
      'error'    =>  $lang['guestbook']['error_no_permission']
    );

    $template['vars']['mainblock'] = $xt->render($tVars);
  }

}

?>

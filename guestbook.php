<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

register_plugin_page('guestbook','','plugin_guestbook');
register_plugin_page('guestbook','edit','plugin_guestbook_edit');
LoadPluginLang('guestbook', 'main', '', '', '#');

function plugin_guestbook()
{global $template, $tpl, $twig, $userROW, $ip, $config, $mysql, $SYSTEM_FLAGS, $TemplateCache, $lang;

  $SYSTEM_FLAGS['info']['title']['group'] = $lang['guestbook']['title'];

  require_once(root."/plugins/guestbook/lib/recaptchalib.php");
  $publickey = pluginGetVariable('guestbook','public_key');
  $privatekey = pluginGetVariable('guestbook','private_key');

  //var_dump( $privatekey );
  //var_dump( $_SERVER["REMOTE_ADDR"] );

  if(isset($_POST['submit']))
  {

    $req_fields = explode(",", pluginGetVariable('guestbook','req_fields'));

    if (!is_array($userROW))
    {
      $_POST['author'] = secure_html(convert(trim($_POST['author'])));
      if(!strlen($_POST['author']) && in_array("author", $req_fields)) $errors[] .= $lang['guestbook']['error_req_name'];
      // Check captcha

      if (pluginGetVariable('guestbook','ecaptcha'))
      {

                $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

                if (!$resp->is_valid) {
                    // What happens when the CAPTCHA was entered incorrectly
                    $errors[] .= $lang['guestbook']['error_req_code'];
                }

      }
      //$_SESSION['captcha'] = rand(00000, 99999);
    }

    //handle message
    $message = secure_html(convert(trim($_POST['content'])));
    preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:ru|su|com|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&?+=\~/-]*)?(?:#[^ '\"&]*)?$~i", $message, $find_url);
    if( isset($find_url[0]) ) { $errors[] .=  $lang['guestbook']['error_nolinks']; }

    preg_match_all("@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@", $message, $find_url);
    if( $find_url[0] ) { $errors[] .= $lang['guestbook']['error_nolinks']; }

    $minl = pluginGetVariable('guestbook','minlength');
    $maxl = pluginGetVariable('guestbook','maxlength');

        if(!strlen(trim($_POST['content'])) && in_array("content", $req_fields)) { $errors[] = $lang['guestbook']['error_req_text'].' '.str_replace(array('{minl}', '{maxl}'), array($minl, $maxl), $lang['guestbook']['error_length_text']); }

    if ((strlen($message) < $minl || strlen($message) > $maxl)&& in_array("content", $req_fields))
    {
      $errors[] .= str_replace(array('{minl}', '{maxl}'), array($minl, $maxl), $lang['guestbook']['error_length_text']);
    }
    $message = str_replace("\r\n", "<br />", $message);

    if(is_array($userROW))
    {
      $author = $userROW['name'];
    } else {
      $author = $_POST['author'];
    }

    $phone = secure_html(trim($_POST['phone']));
        /*
        if (!strlen($phone) && in_array("phone", $req_fields))
    {
      $errors[] .= $lang['guestbook']['error_req_phone'];
    }*/

    if (!(strlen($phone)==11 || strlen($phone)==12) && in_array("phone", $req_fields))
    {
      $errors[] .= $lang['guestbook']['error_length_phone'];
    }


    $status = pluginGetVariable('guestbook','approve_msg');

    if(!is_array($errors))
    {
      $time = time() + ($config['date_adjust'] * 60);
      $mysql->query("INSERT INTO ".prefix."_guestbook (postdate, message, phone, author, ip, status) values (".db_squote($time).", ".db_squote($message).", ".db_squote($phone).",  ".db_squote($author).", ".db_squote($ip).", ".db_squote($status).")");

            if($status == 1) {
                $success_msg = $lang['guestbook']['success_add_wo_approve'];
            }
            else {
                $success_msg = $lang['guestbook']['success_add'];
            }
      $success_add[] .= $success_msg;

          $tpath = locatePluginTemplates(array('mail_success'), 'guestbook', 1);
          $xt = $twig->loadTemplate($tpath['mail_success'].'mail_success.tpl');

          $send_email = pluginGetVariable('guestbook','send_email');

          $tVars = array(
            'time'  => $time,
            'message'  => $message,
            'author' => $author,
            'phone' => $phone,
            'ip' => $ip
            );

          $mailBody = $xt->render($tVars);
          $mailSubject = $lang['guestbook']['mailSubject'];


          $send_email_array = explode(",", $send_email);
          foreach ( $send_email_array as $email ) {
            sendEmailMessage($email, $mailSubject, $mailBody, $filename = false, $mail_from = false, $ctype = 'text/html');
          }

      @header("Refresh: 2; url=".generatePluginLink('guestbook', null, array(), array(), false, true));
    }
  }

  if($_REQUEST['mode']== 'del')
  {
    if(is_array($userROW) && ($userROW['status'] == "1"))
    {
      if (!is_array($mysql->record("SELECT id FROM ".prefix."_guestbook WHERE id=".db_squote(intval($_REQUEST['id'])))))
      {
        $template['vars']['mainblock'] = $lang['guestbook']['error_entry_notfound'];
        return;
      }
      $mysql->query("DELETE FROM ".prefix."_guestbook WHERE id = ".intval($_REQUEST['id']));
    }
  }

    /*
    if (!is_array($userROW))
    {
      $tfvars['vars']['admin_url'] = admin_url;
      @session_register('captcha');
      $_SESSION['captcha'] = mt_rand(00000, 99999);
      $tfvars['vars']['captcha'] = '';
    }
  */


  //comments
  $perpage = intval(pluginGetVariable('guestbook', 'perpage'));

  if (($perpage < 1) or ($perpage > 5000)) { $perpage = 10; }

  $page = isset($params['page'])?intval($params['page']):intval($_REQUEST['page']);
  $page    = isset($page)?$page:0;
  if ($page < 1)  $page = 1;
  if (!$start)  $start = ($page - 1)* $perpage;

  $total_count = $mysql-> result("SELECT COUNT(*) AS num FROM ".prefix."_guestbook WHERE status = 1");

  $PagesCount = ceil($total_count / $perpage);

  $paginationParams = checkLinkAvailable('guestbook', '')?
      array('pluginName' => 'guestbook', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
      array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'guestbook'), 'xparams' => array(), 'paginator' => array('page', 1, false));
  /*
  templateLoadVariables(true);
  $navigations = $TemplateCache['site']['#variables']['navigation'];
    */
    $tpath = locatePluginTemplates(array(':'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
  $navigations =  parse_ini_file($tpath[':'].'/variables.ini', true);

  $order = pluginGetVariable('guestbook', 'order');

    $tVars = array(
        'comments'      =>  guestbook_records($order, $start, $perpage),
        'pages'     => generatePagination($page, 1, $PagesCount, 10, $paginationParams, $navigations),
        'total_count'  =>  $total_count,
        'perpage'       =>  $perpage,
        'errors'      => $errors,
        'success'      => $success_add,

        'author'    =>  $author,
        'phone'      =>  $phone,
        'ip'         =>  $ip,
        'message'      =>  $message,
        'smilies'    => (pluginGetVariable('guestbook','usmilies')) ? InsertSmilies('', 10) :"",
        'bbcodes'    => (pluginGetVariable('guestbook','ubbcodes')) ? BBCodes() :"",
        'use_captcha'  => (pluginGetVariable('guestbook','ecaptcha')),
        'captcha'     =>   (pluginGetVariable('guestbook','ecaptcha') && !(is_array($userROW)))?recaptcha_get_html($publickey):'',
        'use_guests'     =>  (!is_array($userROW) && !pluginGetVariable('guestbook','guests'))
    );

    $tpath = locatePluginTemplates(array('guestbook'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
    $xt = $twig->loadTemplate($tpath['guestbook'].'guestbook.tpl');
  $template['vars']['mainblock'] = $xt->render($tVars);
}


function guestbook_records($order, $start, $perpage) {
  global $mysql, $tpl, $userROW, $config, $parse;

  foreach ($mysql->select("SELECT * FROM ".prefix."_guestbook WHERE status = 1 ORDER BY id {$order} LIMIT {$start}, {$perpage}") as $row)
  {
    if (pluginGetVariable('guestbook','usmilies')) { $row['message'] = $parse -> smilies($row['message']); }
    if (pluginGetVariable('guestbook','ubbcodes'))  { $row['message'] = $parse -> bbcodes($row['message']); }

    $editlink = generateLink('core', 'plugin', array('plugin' => 'guestbook', 'handler' => 'edit'), array('id' => $row['id']));
    $dellink = generateLink('core', 'plugin', array('plugin' => 'guestbook'), array('mode' => 'del', 'id' => $row['id']));
    $comnum++;

    $comments[] = array (
            'date' => $row['postdate'],
            'message' => $row['message'],
            'answer' => $row['answer'],
            'phone' => $row['phone'],
            'author' => $row['author'],
            'ip' => $row['ip'],
            'comnum' => $comnum,
            'edit' => $editlink,
            'del' => $dellink
    );

        /*
    $tpl -> template('comments', extras_dir.'/guestbook/tpl');
    $tpl -> vars('comments', $tvars);
    $comments .= $tpl -> show('comments');
        */
  }
  return $comments;
}

function plugin_guestbook_edit()
{
  global $template, $tpl, $userROW, $ip, $config, $mysql, $twig, $lang;

    $tpath = locatePluginTemplates(array('edit'), 'guestbook', pluginGetVariable('guestbook', 'localsource'));
    $xt = $twig->loadTemplate($tpath['edit'].'edit.tpl');

   if(is_array($userROW) && $userROW['status'] == "1")
   {
     if(isSet($_REQUEST['go']))
     {
        $author = secure_html(convert(trim($_REQUEST['author'])));
       $phone = secure_html(convert(trim($_REQUEST['phone'])));
       $message = secure_html(convert(trim($_REQUEST['content'])));
             $answer = secure_html(convert(trim($_REQUEST['answer'])));
         $message = str_replace("\r\n", "<br />", $message);

         $mysql->query("UPDATE ".prefix."_guestbook SET author =".db_squote($author).", message=".db_squote($message).", answer=".db_squote($answer).", phone=".db_squote($phone)." WHERE id=".$_REQUEST['id']);
         header("Location: ../");

     }
     else
     {
        if(!is_array($row = $mysql->record("SELECT * FROM ".prefix."_guestbook WHERE id=".db_squote(intval($_REQUEST['id'])))))
     {

            $tVars = array(
                'error'    =>  $lang['guestbook']['error_no_entry']
            );

            $template['vars']['mainblock'] = $xt->render($tVars);
            return;
       }

     $row['message'] = str_replace("<br />", "\r\n", $row['message']);
         $row['answer'] = str_replace("<br />", "\r\n", $row['answer']);
         $tVars = array(
            'author'    =>  $row['author'],
            'phone'      =>  $row['phone'],
            'answer'         =>  $row['answer'],
            'message'      =>  $row['message'],
            'id'         =>  $row['id'],
            'error'         => ''
        );

        $template['vars']['mainblock'] = $xt->render($tVars);

    }

   }
   else
   {

        $tVars = array(
            'error'    =>  $lang['guestbook']['error_no_permission']
        );

        $template['vars']['mainblock'] = $xt->render($tVars);
   }

}

?>

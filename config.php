<?php

if(!defined('NGCMS'))
{
  exit('HAL');
}

plugins_load_config();
LoadPluginLang('guestbook', 'config', '', '', '#');

switch ($_REQUEST['action']) {
  case 'options': show_options();    break;
  case 'show_messages': show_messages();    break;
  case 'edit_message': edit_message();      break;
  case 'modify': modify(); show_messages();  break;
  default: show_options();
}



function show_options()
{
global $tpl, $mysql, $lang, $twig;


  $tpath = locatePluginTemplates(array('config/main', 'config/general.from'), 'guestbook', 1);

  if (isset($_REQUEST['submit']))
  {
    pluginSetVariable('guestbook', 'usmilies', secure_html($_REQUEST['usmilies']));
    pluginSetVariable('guestbook', 'ubbcodes', secure_html($_REQUEST['ubbcodes']));
    pluginSetVariable('guestbook', 'minlength', intval($_REQUEST['minlength']));
    pluginSetVariable('guestbook', 'maxlength', intval($_REQUEST['maxlength']));
    pluginSetVariable('guestbook', 'guests', secure_html($_REQUEST['guests']));
    pluginSetVariable('guestbook', 'ecaptcha', secure_html($_REQUEST['ecaptcha']));
    pluginSetVariable('guestbook', 'public_key', secure_html($_REQUEST['public_key']));
    pluginSetVariable('guestbook', 'private_key', secure_html($_REQUEST['private_key']));
    pluginSetVariable('guestbook', 'perpage', intval($_REQUEST['perpage']));
    pluginSetVariable('guestbook', 'order', secure_html($_REQUEST['order']));
    pluginSetVariable('guestbook', 'date',  secure_html($_REQUEST['date']));
    pluginSetVariable('guestbook', 'send_email',  secure_html($_REQUEST['send_email']));
        pluginSetVariable('guestbook', 'req_fields',  secure_html($_REQUEST['req_fields']));
        pluginSetVariable('guestbook', 'approve_msg',  secure_html($_REQUEST['approve_msg']));

    pluginSetVariable('guestbook', 'admin_count', intval($_REQUEST['admin_count']));

    pluginsSaveConfig();

    redirect_guestbook('?mod=extra-config&plugin=guestbook');
  }

  $usmilies = pluginGetVariable('guestbook', 'usmilies');
  $ubbcodes = pluginGetVariable('guestbook', 'ubbcodes');
  $minlength = pluginGetVariable('guestbook', 'minlength');
  $maxlength = pluginGetVariable('guestbook', 'maxlength');
  $guests = pluginGetVariable('guestbook', 'guests');
  $ecaptcha = pluginGetVariable('guestbook', 'ecaptcha');
  $public_key = pluginGetVariable('guestbook', 'public_key');
  $private_key = pluginGetVariable('guestbook', 'private_key');
  $perpage = pluginGetVariable('guestbook', 'perpage');
  $order = pluginGetVariable('guestbook', 'order');
  $date = pluginGetVariable('guestbook', 'date');
  $send_email = pluginGetVariable('guestbook', 'send_email');
    $req_fields = pluginGetVariable('guestbook', 'req_fields');
    $approve_msg = pluginGetVariable('guestbook', 'approve_msg');

  $admin_count = pluginGetVariable('guestbook', 'admin_count');


  $xt = $twig->loadTemplate($tpath['config/general.from'].'config/general.from.tpl');

  $tVars = array(
      'skins_url'    =>  skins_url,
      'home'      =>  home,
      'tpl_home' => admin_url,

      'usmilies' => $usmilies,
      'ubbcodes' => $ubbcodes,
      'minlength' => $minlength,
      'maxlength' => $maxlength,
      'guests' => $guests,
      'ecaptcha' => $ecaptcha,
      'public_key' => $public_key,
      'private_key' => $private_key,
      'perpage' => $perpage,
      'order' => $order,
      'date' => $date,
      'send_email' => $send_email,
            'req_fields' => $req_fields,
            'approve_msg' => $approve_msg,

      'admin_count' => $admin_count,
    );

  $xg = $twig->loadTemplate($tpath['config/main'].'config/main.tpl');

  $tVars = array(
    'entries' => $xt->render($tVars),

  );

  print $xg->render($tVars);

}

function show_messages()
{
global $tpl, $mysql, $lang, $twig, $config;

  $tpath = locatePluginTemplates(array('config/main', 'config/show_messages'), 'guestbook', 1);

  $tVars = array();

  $news_per_page = pluginGetVariable('guestbook', 'admin_count');

  $fSort = "ORDER BY id DESC";
  $sqlQPart = "from ".prefix."_guestbook ".$fSort;
  $sqlQCount = "select count(id) ".$sqlQPart;
  $sqlQ = "select * ".$sqlQPart;

  $pageNo    = intval($_REQUEST['page'])?$_REQUEST['page']:0;
  if ($pageNo < 1)  $pageNo = 1;
  if (!$start_from)  $start_from = ($pageNo - 1)* $news_per_page;

  $count = $mysql->result($sqlQCount);
  $countPages = ceil($count / $news_per_page);

  foreach ($mysql->select($sqlQ.' LIMIT '.$start_from.', '.$news_per_page) as $row)
  {
    $tEntry[] = array (
      'id' => $row['id'],
      'postdate' => $row['postdate'],
      'message' => $row['message'],
            'answer' => $row['answer'],
      'phone' => $row['phone'],
      'author' => $row['author'],
      'ip' => $row['ip'],
      'status' => $row['status'],
    );
  }


  $xt = $twig->loadTemplate($tpath['config/show_messages'].'config/show_messages.tpl');

  $tVars = array(
    'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=guestbook&action=show_messages&page=%page%')),
    'entries' => isset($tEntry)?$tEntry:'',
    'php_self'    =>  $PHP_SELF,
    'skins_url'    =>  skins_url,
    'home'      =>  home,
  );

  $xg = $twig->loadTemplate($tpath['config/main'].'config/main.tpl');

  $tVars = array(
    'entries' => $xt->render($tVars),

  );

  print $xg->render($tVars);
}


function edit_message()
{
global $tpl, $mysql, $lang, $twig;

  $tpath = locatePluginTemplates(array('config/main', 'config/edit_message'), 'guestbook', 1);

  $id = intval($_REQUEST['id']);

  if (!empty($id))
  {
    $row = $mysql->record('SELECT * FROM '.prefix.'_guestbook WHERE id = '.db_squote($id).' LIMIT 1');

    if (isset($_REQUEST['submit']))
    {
      $author = $_REQUEST['author'];
      $phone = $_REQUEST['phone'];
      $status = $_REQUEST['status'];
      $message = str_replace(array("\r\n", "\r"), "\n",convert($_REQUEST['message']));
            $answer = str_replace(array("\r\n", "\r"), "\n",convert($_REQUEST['answer']));

      if(empty($author) || empty($phone) || empty($message) )
      {
        $error_text[] = 'Вы заполнили не все обязательные поля';
      }

      if(empty($error_text))
      {
        $mysql->query('UPDATE '.prefix.'_guestbook SET
            message = '.db_squote($message).',
                        answer = '.db_squote($answer).',
            phone = '.db_squote($phone).',
            author = '.db_squote($author).',
            status = '.db_squote($status).'
          WHERE id = \''.intval($id).'\' ');

        redirect_guestbook('?mod=extra-config&plugin=guestbook&action=show_messages');
      }
    }

    if(!empty($error_text))
    {
      foreach($error_text as $error)
      {
        $error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
      }
    } else {
      $error_input ='';
    }


    $xt = $twig->loadTemplate($tpath['config/edit_message'].'config/edit_message.tpl');

    $tVars = array(
        'skins_url'    =>  skins_url,
        'home'      =>  home,
        'tpl_home' => admin_url,
        'message' => $row['message'],
                'answer' => $row['answer'],
        'phone' => $row['phone'],
        'author' => $row['author'],
        'status' => $row['ip'],
        'ip' => $row['ip'],
        'postdate' => $row['postdate'],
        'error' => $error_input,
      );


  } else {
    msg(array("type" => "error", "text" => "Не передан id"));
  }

    $tVars = array(
        'skins_url'    =>  skins_url,
        'home'      =>  home,
        'tpl_home' => admin_url,
        'message' => $row['message'],
                'answer' => $row['answer'],
        'phone' => $row['phone'],
        'author' => $row['author'],
        'status' => $row['status'],
        'ip' => $row['ip'],
        'postdate' => $row['postdate'],
        'error' => $error_input,
      );

  $xg = $twig->loadTemplate($tpath['config/main'].'config/main.tpl');

  $tVars = array(
    'entries' => $xt->render($tVars),

  );

  print $xg->render($tVars);
}

function modify()
{
global $mysql;

  $selected_news = $_REQUEST['selected_message'];
  $subaction  =  $_REQUEST['subaction'];

  $id = implode( ',', $selected_news );

  if( empty($id) )
  {
    return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали ни одного сообщения"));
  }

  switch($subaction) {
    case 'mass_approve'      : $active = 'status = 1'; break;
    case 'mass_forbidden'    : $active = 'status = 0'; break;
    case 'mass_delete'       : $del = true; break;
  }
  if(isset($active))
  {
    $mysql->query("update ".prefix."_guestbook
        set {$active}
        WHERE id in ({$id})
        ");
    msg(array("type" => "info", "info" => "Сообщения с ID ${id} Активированы/Деактивированы"));
  }
  if(isset($del))
  {
    $mysql->query("delete from ".prefix."_guestbook where id in ({$id})");
    msg(array("type" => "info", "info" => "Сообщения с ID ${id} удалены"));
  }
}

function redirect_guestbook($url)
{
  if (headers_sent()) {
    echo "<script>document.location.href='{$url}';</script>\n";
  } else {
    header('HTTP/1.1 302 Moved Permanently');
    header("Location: {$url}");
  }
}


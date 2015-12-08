<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
//LoadPluginLang($plugin, 'main');

function plugin_guestbook_install($action) {
  global $lang;

  if ($action != 'autoapply')
    loadPluginLang('guestbook', 'config', '', '', ':');
$db_update = array(
  array(
    'table'    =>  'guestbook',
    'action'  =>  'create',
    'key'    =>  'primary key(`id`)',
    'fields'  =>  array(
      array('action' => 'create', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
      array('action' => 'create', 'name' => 'postdate',  'type' => 'int', 'params' => "not null default '0'"),
      array('action' => 'create', 'name' => 'message', 'type' => 'text', 'params' => 'not null'),
            array('action' => 'create', 'name' => 'answer', 'type' => 'text', 'params' => "not null default ''"),
      array('action' => 'create', 'name' => 'phone', 'type' => 'text', 'params' => "not null default ''"),
      array('action' => 'create', 'name' => 'author', 'type' => 'varchar(50)', 'params' => "not null default ''"),
      array('action' => 'create', 'name' => 'ip', 'type' => 'varchar(40)', 'params' => "not null default ''"),
      array('action' => 'create', 'name' => 'status', 'type' => 'int', 'params' => "not null default '0'"),
    )
  ),
);

  switch ($action) {
    case 'confirm':generate_install_page('guestbook', '������ ��������� ������������ �������� ����� �� ����� �����<br />');break;
    case 'autoapply':
    case 'apply':
      if (fixdb_plugin_install('guestbook', $db_update, 'install', ($action=='autoapply')?true:false)) {
        plugin_mark_installed('guestbook');
      } else {
        return false;
      }

      // Now we need to set some default params
      $params = array(
        'usmilies' => 1,
        'ubbcodes' => 1,
        'minlength' => 3,
        'maxlength' => 500,
        'guests' => 0,
        'ecaptcha' => 1,
        'perpage' => '1',
        'order' => 'ASC',
        'date' => 'j.m.Y - H:i',
        'send_email' => '',
                'req_fields' => 'content,author,phone',
                'approve_msg' => 1,
        'admin_count' => 5,
      );
      foreach ($params as $k => $v) {
        extra_set_param('guestbook', $k, $v);
      }
      extra_commit_changes();
      break;
  }
  return true;
}
?>

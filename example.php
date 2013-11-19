<?php

  require_once 'ActiveCollab/autoload.php';

  use \ActiveCollab\Client as API;
  use \ActiveCollab\Connectors\Curl as CurlConnector;

  API::setUrl('https://myaccount.manageprojects.com/api.php');
  API::setKey('MY-API-TOKEN');
  API::setConnector(new CurlConnector);

  print '<pre>';

  print "API info:\n\n";

  var_dump(API::info());

  print "Defined project templates:\n\n";

  var_dump(API::call('projects/templates'));

  print "Task creation example:\n\n";

  var_dump(API::call('projects/65/tasks/add', null, array(
    'task[name]' => 'This is a task name',
    'task[assignee_id]' => 48,
    'task[other_assignees]' => array(3, 1),
  ), array(
    '/Library/WebServer/Documents/BZHI6GtCQAEEMz-.jpg-large.jpeg'
  )));

  print '</pre>';
<?php

  require_once __DIR__.'/src/ActiveCollab/SDK/autoload.php';

  use \ActiveCollab\SDK\Client as API;

  API::setUrl('https://myaccount.manageprojects.com/api.php');
  API::setKey('MY-API-TOKEN');

  print '<pre>';

  print "API info:\n\n";

  var_dump(API::info());

  print "Defined project templates:\n\n";

  var_dump(API::get('projects/templates'));

  print "Task creation example:\n\n";

  var_dump(API::post('projects/65/tasks', [
    'task[name]' => 'This is a task name',
    'task[assignee_id]' => 48,
    'task[other_assignees]' => [ 3, 1 ],
  ], [
    '/Library/WebServer/Documents/BZHI6GtCQAEEMz-.jpg-large.jpeg'
  ]));

  print '</pre>';

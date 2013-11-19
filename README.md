# activeCollab SDK

This is super simple PHP library that makes communication with [activeCollab API](https://www.activecollab.com/docs/manuals/developers/api) easy. 

## First Connection

In order to connect, you will need API URL and API token. 

To get these details, go to your user profile in your activeCollab and select **API Subscriptions** from **Options** drop-down. Click on **New Subscription** button and fill the form (client name is name of your app that will communicate with activeCollab via API). After you create API subscription for your application, click on the magnifier glass icon to open a dialog that will show you API URL and token for that subscription.

Now that you have API token and URL, you can test out this simple example:

    <?php
    
      require_once 'ActiveCollab/autoload.php';
    
      use \ActiveCollab\Client as API;
      use \ActiveCollab\Connectors\Curl as CurlConnector;
    
      API::setUrl('MY-API-URL');
      API::setKey('MY-API-TOKEN');
      API::setConnector(new CurlConnector);
    
      print '<pre>';
      print_r(API::info());
      print '</pre>';

This example will contact activeCollab and ask for application and user info. Response is a simple associative array with a lot of details about the system that you are communicating with.

# Making API Calls

This example shows how you can create a new task in a selected project:

    API::call('projects/65/tasks/add', null, array(
      'task[name]' => 'This is a task name',
      'task[assignee_id]' => 48,
      'task[other_assignees]' => array(3, 1),
    ), array(
      '/attach.jpeg'
    ));

``call()`` method can take four parameters:

1. ``command`` (required) - API command,
2. ``extra command parameters`` (optional) - Extra variables that will be attached to the command. Most commands don't require any extra command parameters, but some do (like ``dont_limit_result `` param for [Tracked time and expenses listing](https://www.activecollab.com/docs/manuals/developers/api/time#tracking) command),
3. ``POST variables`` - array of POST variables. Note that you do not need to add ``submitted`` variable (it is automatically added for you),
4. ``attachments`` - List of file paths that should be attached to the object that is being created or updated.

For full list of available API command, please check [activeCollab API documentation](https://www.activecollab.com/docs/manuals/developers).
# activeCollab SDK

This is a simple PHP library that makes communication with [activeCollab API](https://www.activecollab.com/docs/manuals/developers/api) easy. 

## First Connection

In order to connect, you will need API URL and API token. 

To get these details, go to your user profile in your activeCollab and select **API Subscriptions** from **Options** drop-down. Click on **New Subscription** button and fill the form (client name is name of your app that will communicate with activeCollab via API). After you create API subscription for your application, click on the magnifier glass icon to open a dialog that will show you API URL and token for that subscription.

Now that you have API token and URL, you can test out this simple example:

    <?php
    
      require_once 'ActiveCollab/autoload.php';
    
      use \ActiveCollab\Client as API;
      use \ActiveCollab\Exceptions\AppException;
    
      API::setUrl('MY-API-URL');
      
      // Use issueToken() method to get a new token. Store it for later use
      try {
        $token = API::issueToken('my@email.com', 'MY-PASSWORD', 'NAME-OF-MY-APP', 'NAME-OF-MY-COMPANY');
      } catch (Exception $e) {
        die($e->getMessage());
      }
      
      // Set token before making calls
      API::setKey($token);
    
      print '<pre>';
      print_r(API::info());
      print '</pre>';

This example will contact activeCollab and ask for application and user info. Response is a simple associative array with a lot of details about the system that you are communicating with.

# Making API Calls

Listing all tasks in project #65 is easy. Just call:

    API::call('projects/65/tasks');

This example shows how you can create a new task in a selected project:

    try {
      API::call('projects/65/tasks/add', null, [
        'task[name]' => 'This is a task name',
        'task[assignee_id]' => 48,
        'task[other_assignees]' => array(3, 1),
      ], [
        '/attach.jpeg'
      ]);
    } catch(AppException $e) {
      print $e->getMessage() . '<br><br>';
      // var_dump($e->getServerResponse()); (need more info?)
    }

``call()`` method can take four parameters:

1. ``command`` (required) - API command,
2. ``extra command parameters`` (optional) - Extra variables that will be attached to the command. Most commands don't require any extra command parameters, but some do (like ``dont_limit_result `` param for [Tracked time and expenses listing](https://www.activecollab.com/docs/manuals/developers/api/time#tracking) command),
3. ``POST variables`` - array of POST variables. Note that you do not need to add ``submitted`` variable (it is automatically added for you),
4. ``attachments`` - List of file paths that should be attached to the object that is being created or updated.

For full list of available API command, please check [activeCollab API documentation](https://www.activecollab.com/docs/manuals/developers).
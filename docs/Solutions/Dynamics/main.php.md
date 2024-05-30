# main.php
This PHP script is a command-line interface for interacting with Microsoft Graph API. It provides a simple menu to perform actions like displaying an access token, listing users, and making a Graph API call.

## Dependencies
This script requires the following dependencies:

Composer for managing PHP dependencies.
Dotenv for loading environment variables.
GraphHelper.php, a custom helper file for handling Graph API operations.
## Initialization
At the start, the script loads Composer dependencies and the GraphHelper file. It then prints a welcome message and loads environment variables from a .env.local file. It requires the CLIENT_ID, CLIENT_SECRET, and TENANT_ID environment variables to be set.

The initializeGraph() function is then called to set up the Graph API for app-only authentication.

## Main Loop
The script enters a loop where it presents a menu to the user with the following options:

0. Exit: Exits the script.
1. Display access token: Calls the displayAccessToken() function which retrieves and prints the app-only access token.
2. List users: Calls the listUsers() function which retrieves and prints a list of users from the Graph API.
3. Make a Graph call: Calls the makeGraphCall() function which makes a custom Graph API call.
The loop continues until the user chooses to exit.

## Functions
initializeGraph(): Calls the initializeGraphForAppOnlyAuth() function from the GraphHelper file to set up the Graph API for app-only authentication.
displayAccessToken(): Retrieves the app-only access token using the getAppOnlyToken() function from the GraphHelper file and prints it.
listUsers(): Retrieves a list of users using the getUsers() function from the GraphHelper file and prints their details.
makeGraphCall(): Makes a custom Graph API call using the makeGraphCall() function from the GraphHelper file.
Each function is wrapped in a try-catch block to handle any exceptions that may occur during the Graph API operations.

## Through the file noteworthy mentions
we read the env.local file to get the environment variables, using this 

```
// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.local');
```

our important variables are the CLIENT_ID, the CLIENT_SECRET, and the TENANT_ID, which are used to authenticate the app with the Microsoft Graph API.

we call the function initializeGraph() to set up the Graph API for app-only authentication. This function calls the initializeGraphForAppOnlyAuth() function from the GraphHelper file.

To do this, it uses the ClientCredentialContext class from vendor/microsoft/kiota-authentication-phpleague/src/Oauth/ClientCredentialContext.php
and it also uses the GraphServiceClient class from vendor/microsoft/microsoft-graph/src/GraphServiceClient.php

for now, the choice displayAccessToken works, as well as the listUsers choice. However, the makeGraphCall choice throws errors.

the GraphHelper works for getting the getAppOnlyToken() function
the GraphHelper file is located in the same directory as the main.php file, and it contains the helper functions for interacting with the Microsoft Graph API.
getUsers() function is also working, it retrieves a list of users from the Graph API and prints their details.

this part of the code

```
$nextLink = $users->getOdataNextLink();
        $moreAvailable = isset($nextLink) && $nextLink != '' ? 'True' : 'False';
        print(PHP_EOL.'More users available? '.$moreAvailable.PHP_EOL.PHP_EOL);
```

shows whether there are more users available in the list. If there are more users available, the script will prompt the user to press Enter to continue fetching more users. In our case, there are no more users available, so the script will show False.

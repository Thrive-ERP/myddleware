# dynamics.php
This PHP script is part of the Myddleware project and provides a class for interacting with Microsoft's Dynamics 365 using the Graph API.

## Dependencies
This script requires the following dependencies:

Microsoft Graph SDK for PHP for interacting with the Graph API.
Symfony's Form component for creating form fields.
Symfony's Exception component for handling exceptions.


## Classes
### DynamicsCore
This class extends the Solution class and provides methods for logging into Dynamics 365 and getting an access token.

#### Class Variables
##### $graphClient: 
An instance of GraphServiceClient for interacting with the Graph API.
static $tenantId, static $clientId, static $clientSecret: Static variables for storing the tenant ID, client ID, and client secret for the Azure AD app.
##### $last_error: 
A string for storing the last error message.
#### Methods
##### getFieldsLogin()
This method returns an array of form fields for the login form. Each field is an associative array with keys for the name, type, and label of the field.

##### login($paramConnexion)
This method attempts to log into Dynamics 365 using the provided connection parameters. It sets the static variables for the tenant ID, client ID, and client secret, creates a ClientCredentialContext for the client credentials OAuth flow, and initializes the GraphServiceClient with this context. It then tries to get an access token and returns true if successful or false if an exception occurs.

##### getAppOnlyToken()
This method retrieves an app-only access token from the Graph API. It calls the getAccessToken() method of the authentication provider of the GraphServiceClient.

## Dynamics
This class extends the DynamicsCore class. It currently does not add any additional functionality.

## Usage
To use this script, you would first create an instance of the Dynamics class, call the getFieldsLogin() method to get the form fields for the login form, and then call the login() method with the form data to log into Dynamics 365. You can then use the getAppOnlyToken() method to get an access token for making Graph API calls.
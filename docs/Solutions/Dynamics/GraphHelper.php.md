# GraphHelper.php
This PHP class, GraphHelper, is a utility class for interacting with the Microsoft Graph API. It provides methods for initializing the Graph API client, getting an access token, listing users, and making a custom Graph API call.

## Class Variables
$clientId, $clientSecret, $tenantId: These are static variables that store the client ID, client secret, and tenant ID for the Azure AD app.
$tokenContext: This is a static variable that stores the context for the client credentials OAuth flow. It uses the ClientCredentialContext class from the Microsoft Graph SDK for PHP. It takes the 3 parameters: $clientId, $clientSecret, and $tenantId, and an optional array of additional parameters that are strings, but we don't use that in our case.
$appClient: This is a static variable that stores the Graph API client.

## Methods
### initializeGraphForAppOnlyAuth()
This static method initializes the Graph API client for app-only authentication. It reads the client ID, client secret, and tenant ID from environment variables, creates a ClientCredentialContext for the client credentials OAuth flow, and initializes the GraphServiceClient with this context.

### getAppOnlyToken()
This static method retrieves an app-only access token. It creates a GraphPhpLeagueAccessTokenProvider with the client credentials context and calls its getAuthorizationTokenAsync() method to get the access token.

### getUsers()
This static method retrieves a list of users from the Graph API. It creates a UsersRequestBuilderGetRequestConfiguration and a UsersRequestBuilderGetQueryParameters to specify the request parameters. It sets the select parameter to only request the displayName, id, and mail properties of the users, the orderby parameter to sort the results by displayName, and the top parameter to limit the results to 25. It then calls the get() method of the users() service on the GraphServiceClient with this configuration and returns the result.

### makeGraphCall()
This static method makes a custom Graph API call. It creates a ContactsRequestBuilderGetRequestConfiguration and a ContactsRequestBuilderGetQueryParameters to specify the request parameters. It sets the select parameter to only request the displayName, id, and mail properties of the contacts, the orderby parameter to sort the results by displayName, and the top parameter to limit the results to 25. It then calls the get() method of the contacts() service on the GraphServiceClient with this configuration and prints the result.


## PHP League
The PHP League (or PHPLeague) is a group of developers who have banded together to develop some of the most widely used PHP packages. They follow PHP-FIG standards and ensure that their packages are framework-agnostic, meaning they can be used in any PHP project regardless of the framework used.

The GraphPhpLeagueAccessTokenProvider you're seeing in your code is a part of the Microsoft Graph SDK for PHP. This class is used to provide access tokens for authentication with the Microsoft Graph API. It uses the PHP League's OAuth 2.0 Client under the hood to handle the OAuth 2.0 flow and obtain access tokens.


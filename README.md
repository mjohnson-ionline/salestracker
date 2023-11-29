# Application

## Clients

## Resellers

## Users

## Permissions

## Webhooks
- there are webhooks setup to listen to events from PipeDrive and Xero
- these webhooks are located under the Webhooks controller under two methods
- everything is logged in a database
- depending on the webhook, it will trigger a specific method and make the various calls
- 

### PipeDrive
- in pipedrive added a bunch of simple webhooks to listen to events at https://ionline2.pipedrive.com/settings/webhooks/pipedrive/
- they follow this format - https://proposal.staging-sites.com.au/webhook/pipedrive
  - added.organization
  - updated.organization
  - added.person
  - updated.person
  - updated.deal

### Xero
- in the developer.xero.com portal for the account xo@ionline.com.au there are a series of webhooks generated
- they follow this format - https://proposal.staging-sites.com.au/webhook/xero
- when intially setup, there must be an intent to receive setup as per this guide - https://developer.xero.com/documentation/guides/webhooks/configuring-your-server/#intent-to-receive
- it will send correctly signed events and non correctly signed events - essentially in the api method, i am checking this each time
- we are listening for when a xero invoice is updated - https://developer.xero.com/documentation/guides/webhooks/overview/

## Xero
- multiple packages are used:- 
  - this package will make the connection - https://github.com/XeroAPI/xero-php-oauth2-app/blob/master/example.php
  - this package package will actually do the api reads/writes - https://github.com/webfox/laravel-xero-oauth2
- an app must then be registered with xero - https://developer.xero.com/myapps - this was done under accounts@ionline.com.au
- need to adjust the config file to include the scopes for xero
- need to make a connection to the xero using the app credentials

## PipeDrive
- uses the main package - https://github.com/IsraelOrtuno/pipedrive#installation
- the package is a bit old so doesnt take into account the subscription api, so we are justing plain guzzle for this instance - https://developers.pipedrive.com/docs/api/v1/Subscriptions#addSubscriptionInstallment
- https://pipedrive.readme.io/docs
- For api access, just generate a personal token - stored in the env file

## Database
- in the users table, there is a 'type' column 0=admin, 1=user, 2=client, 3=reseller

## Todo
- make sure that we are testing for a connection to xero in the UI and send an email if it fails

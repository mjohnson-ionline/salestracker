<?php
    // todo items
    // DONE
	// need to import all products from pipedrive to app - done
    // change the trigger that the deal is only synced when the deal is won - done
    // if the invoice is already made, dont do it again - done
    // when making a deal, if the accounts email is set us that - done
    // when making a deal - do tests for the single/retainer type projects - done
    // when making a deal - account for the terms (in months) - done
    // need to fix issue where there are multiple syncs from pipedrive - move it all to a cron - done see below under Automated CRONS
    // generate deals, invoices and line items from pipedrive logs using a command - done
    // add resellers to invoices - done
    // percentage per code - done
    // percent per role - done
    // get the reffer email as the reseller in the laravel app - done
    // enable cron to clean duplicates every minute - done
    // DEALS -> view invoices - done
    // DEALS -> disable deleting and editing - done
    // INVOICES -> edit invoice details - done
    // LINE ITEMS -> SETUP CRUD - done
    // LINE ITEMS -> review and view from invoice
    // INVOICE - calculate total amount - done
    // INVOICES -> send INVOICES
        // create an invoice preview using Operations - done
        // syncs the invoice to XERO  using Operations - done
    // its very weird that i am testing against TYPE in the logs table with PIPEDRIVE and actions with XERO - make it the same using actions or Logs - done
    // remove the synced pipedrive deals stuff - done
    // make sure that we are only accepting payments for existing deals - done
    // when a payment arrives from xero, create it in the system, test the parsing of xero logs - done
    // payments CRUD - done
    // when payments arrives, calculate the comission - done
    // top 5 perfomring resellers - done
    // date range filter - done
    // comissions table - done
    // download as csv - single - download as csv overall - done
    // make the start and end times work on the single filters and prepopuulate the single report generation side of things - done
    // make it so the start and end date appear all the time using html - done
    // make the dashboard go to comissions and vice versa - done
    // INVOICES -> View payments - done
    // open up editing on the line items - no
    // reports should be based off payments not invoices - done
    // get a report of all monthly reports - done
    // customised email templates - done
    // moving all automated functions to scheduler - done
    // set the timing for the various items / enable cron to run for imported every 5 minutes - done
    // on the crud view, the date isnt displaying correctly, it should be using date() not carbon
    // invoices need a meaning full name - done
    // invoices dont have a xero_invoice_id
    // add 'sales' type users - done
    // make it that the seller has some additional fields including 'monthly target' - done
    // the sales person is the pipedrive user - done

    // DOING


    // TO DO
    // generate reports for sales people

    // 2fa - https://medium.com/@maulanayusupp/how-to-create-two-factor-authentication-with-laravel-a44e58f69319
    // edit the views for different roles
    // write a good spec document
    // lock down the app using gates

    // TESTING
        // new pipedrive user with normal email - done
        // new pipedrive user accounts emails - done
        // new pipedrive sale
            // single invoice deal
            // multiple invoice deal
    // send invoices to resellers

    // QUESTIONS
    // what information do you want on the emails?
    // comissions CRUD?
    // admin needs to mark comissions as paid or not
    // do we need to pull across the details from pipedrive like address / phone too? app/Http/Controllers/Admin/Operations/SendXeroInvoiceOperation.php:57
    // send welcome email for resellers?

    // AFTER
    // enable throttle again - app/Http/Kernel.php
    // make it send a nice email with logs
    // do we need a name on the invoice? - how will that be made
    // new type of user called "sales" - must have a monthly target - must be able to see all deals and invoices
    // refactor the models so I can quicky get the information desired
    // use the default values for comission, or the per reseller values - do this later on
    // update the pipedrive deal with the invoice number(s)

    // Automated CRONS
    // function to import products nightly from pipedrive - still works
    // function every minute to check for new logs that are created by pipedrive and have the action deal.updated to remove duplicates as pipedrive is sending duplicated api requests microseconds part -

    // https://ionline2.pipedrive.com/deal/182
?>

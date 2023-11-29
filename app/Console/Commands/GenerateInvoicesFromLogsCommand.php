<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Log;
use App\Models\User;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\LineItem;
use DB;
use App\Models\XeroSync;
use App\Models\Products;
use Devio\Pipedrive\Pipedrive;
use App\Mail\SendInvoiceGeneratedNoticeMail;
use App\Models\Transaction;

class GenerateInvoicesFromLogsCommand extends Command
{
    protected $signature = 'generate:invoices-from-logs';

    protected $description = 'Retrives all the logs that have been created from the pipedrive webhook and generates invoices locally ready for syncing.';

    // write the logs to a file
    public function write_local_log_file($data)
    {
        $file = fopen('/home/ionline_admin/webapps/proposal-app/public/logs.txt', 'w');
        fwrite($file, $data);
        fclose($file);
    }

    public function generateRandomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }

    public function handle(): void
    {

        // get all the deals to turn into invoices
        $logs = Log::where('type', 'deal.updated')
            ->where('platform', 'pipedrive')
            ->where('pipedrive_duplicate_parsed', true)
            ->where('pipedrive_log_parsed_to_invoice', false)
            ->orderBy('created_at', 'asc')
            ->get();
        $this->info('Preparing Log Data');

        $pipedrive = new Pipedrive(config('app.pipedrive_api_token'));

        foreach ($logs as $log) {

            $deal = $pipedrive->deals->find($log->payload['meta']['id']);
            $deal_data = $deal->getContent()->data; // object not arrayect not array
            $this->write_local_log_file(serialize($deal_data));
            $this->info('Preparing Deal Data');

            // if the deal is marked as 'won' with its status
            if ($deal_data->status == 'won') {
                Transaction::create([
                    'details' => 'Deal Updated Webhook in Final Step: ' . $deal_data->title,
                    'log_id' => $log->id,
                ]);

                // gather and prepare organisation data
                $organisation = $pipedrive->organizations->find($deal_data->org_id->value);
                $organisation_data = $organisation->getContent()->data; // object not array
                $xero_organisation_name = $organisation_data->name;
                $xero_address_line_1 = $organisation_data->address_street_number . ' ' . $organisation_data->address_route;
                $xero_address_suburb = $organisation_data->address_locality;
                $xero_address_state = $organisation_data->address_admin_area_level_1;
                $xero_address_postcode = $organisation_data->address_postal_code;
                $xero_address_country = $organisation_data->address_country;
                Transaction::create([
                    'details' => 'Prepared Organisation Data. Organisation Name: ' . $xero_organisation_name . ' Address: ' . $xero_address_line_1 . ' ' . $xero_address_suburb . ' ' . $xero_address_state . ' ' . $xero_address_postcode . ' ' . $xero_address_country,
                    'log_id' => $log->id,
                ]);
                $this->write_local_log_file(serialize($organisation));
                $this->info('Preparing Organisation Data');

                // gather and prepare contact data
                $contact = $pipedrive->persons->find($deal_data->person_id->value);
                $contact_data = $contact->getContent()->data;
                $xero_contact_first_name = $contact_data->first_name;
                $xero_contact_last_name = $contact_data->last_name;
                $xero_contact_email = $contact_data->email[0]->value;
                $xero_contact_phone = $contact_data->phone[0]->value;
                Transaction::create([
                    'details' => 'Prepared Contact Data. Contact Name: ' . $xero_contact_first_name . ' ' . $xero_contact_last_name . ' Email: ' . $xero_contact_email . ' Phone: ' . $xero_contact_phone,
                    'log_id' => $log->id,
                ]);
                $this->write_local_log_file(serialize($contact));
                $this->info('Preparing Contact Data');

                // get all products associated with the deal /v1/deals/{id}/products
                $client = new \GuzzleHttp\Client();
                $res = $client->request('GET', 'https://api.pipedrive.com/v1/deals/' . $log->payload['meta']['id'] . '/products/?api_token=' . config('app.pipedrive_api_token'));
                $results = json_decode($res->getBody()->getContents());
                $products_data = $results->data;
                Transaction::create([
                    'details' => 'Prepared Products Deal Data ' . $log->payload['meta']['id'],
                    'log_id' => $log->id,
                ]);
                foreach ($products_data as $key => $product) {
                    $product_data = $pipedrive->products->find($product->product_id);
                    $product_data = $product_data->getContent()->data;
                    $products_data[$key]->code = $product_data->code;
                }
                $this->write_local_log_file(serialize($products_data));
                Transaction::create([
                    'details' => 'Prepared Products Deal Data ' . serialize($products_data),
                    'log_id' => $log->id,
                ]);
                $this->info('Preparing Products Data');

                // accounts email - f0f06c1ce73d8b8a76b4cba4fd0ab2b4b56fff04
                // https://pipedrive.readme.io/docs/core-api-concepts-custom-fields
                $xero_accounts_contact_email = null;
                if ($deal_data->{'f0f06c1ce73d8b8a76b4cba4fd0ab2b4b56fff04'}) {
                    $xero_accounts_contact_email = $deal_data->{'f0f06c1ce73d8b8a76b4cba4fd0ab2b4b56fff04'};
                    Transaction::create([
                        'details' => 'Found Accounts Email - Applying as the receipent for the invoices ' . $xero_contact_email,
                        'log_id' => $log->id,
                    ]);
                    $this->write_local_log_file(serialize($deal_data));
                    $this->info('Preparing Alternative Accounts Email');
                }

                // get the deal supplemental information relating to the invoicing frequency and terms
                // you will need to adjust the custom fields and look at the log file
                // https://pipedrive.readme.io/docs/core-api-concepts-custom-fields
                // invoicing terms - c71781d660e1cf1f35343ddb6f86e97d21af55b0
                // invoicing frequency - 1085cddbc564a749f5c21380f7ed60729884b346
                // 879 - Single Project (100%)
                // 880 - Single Project (50% / 50%)
                // 881 - Single Project (50% / 25% / 25%)
                // 882 - Retainer
                $invoicing = [];
                $invoicing['terms'] = $deal_data->{'c71781d660e1cf1f35343ddb6f86e97d21af55b0'};
                $invoicing['frequency'] = $deal_data->{'1085cddbc564a749f5c21380f7ed60729884b346'};
                Transaction::create([
                    'details' => 'Prepared Invoicing Data. Terms: ' . $invoicing['terms'] . ' Frequency: ' . $invoicing['frequency'],
                    'log_id' => $log->id,
                ]);
                $this->write_local_log_file(serialize($invoicing));
                $this->info('Preparing Invoicing Data');

                // test it
                if (!is_null($products_data)) {
                    if (count($products_data) > 0) {

                        Transaction::create([
                            'details' => 'Iterating Payments Data: ' . serialize($products_data),
                            'log_id' => $log->id,
                        ]);

                        Transaction::create([
                            'details' => 'Preparing Xero Sync Information. Organisation Name: ' . $xero_organisation_name . ' Contact Name: ' . $xero_contact_first_name . ' ' . $xero_contact_last_name . ' Email: ' . $xero_contact_email . ' Phone: ' . $xero_contact_phone,
                            'log_id' => $log->id,
                        ]);

                        if ($xero_organisation_name) {

                            Transaction::create([
                                'details' => 'Xero Connection Established.',
                                'log_id' => $log->id,
                            ]);

                            // check to see if there is a contact with the same email address locally and if it has a xero id
                            $local_user = User::where('email', '=', $xero_contact_email)->first();
                            if (is_null($local_user)) {
                                $local_user = User::create([
                                    'role' => 'client',
                                    'organisation_name' => $xero_organisation_name,
                                    'first_name' => $xero_contact_first_name,
                                    'last_name' => $xero_contact_last_name,
                                    'email' => $xero_contact_email,
                                    'accounts_email' => $xero_accounts_contact_email,
                                    'phone' => $xero_contact_phone,
                                    'address_line_1' => $xero_address_line_1,
                                    'address_line_2' => '',
                                    'address_suburb' => $xero_address_suburb,
                                    'address_state' => $xero_address_state,
                                    'address_postcode' => $xero_address_postcode,
                                    'address_country' => $xero_address_country,
                                    'password' => bcrypt($this->generateRandomPassword()),
                                ]);
                                Transaction::create([
                                    'details' => 'Local User Not Found. Created Local User. User ID: ' . $local_user->id,
                                    'log_id' => $log->id,
                                ]);

                            } else {
                                Transaction::create([
                                    'details' => 'Local User Found. User ID: ' . $local_user->id,
                                    'log_id' => $log->id,
                                ]);
                            }
                            $this->write_local_log_file($local_user);
                            $this->info('Preparing Local User Data');

                            // make sure there isnt already a deal with this pipedrive_deal_id
                            // turn this back on!
                            /*$deal = Deal::where('pipedrive_deal_id', '=', $deal_data->id)->first();
                            if (!is_null($deal)) {
                                Transaction::create([
                                    'details' => 'Deal Not Found. Creating Deal.',
                                    'log_id' => $log->id,
                                ]);
                                exit();
                            }*/

                            // reffered - 7353a365fc6895cb7aea91b3f315ea296aeea358
                            // https://pipedrive.readme.io/docs/core-api-concepts-custom-fields

                            if ($deal_data->{'7353a365fc6895cb7aea91b3f315ea296aeea358'}) {
                                // get the refferer from pipedrive
                                $reseller_data = $deal_data->{'7353a365fc6895cb7aea91b3f315ea296aeea358'};
                                $reseller_info = $pipedrive->persons->find($reseller_data->value);
                                $reseller_info = $reseller_info->getContent()->data;

                                // check to see a user with this email exists, if not create
                                $reseller = User::where('email', '=', $reseller_info->email[0]->value)->first();
                                if (is_null($reseller)) {
                                    $reseller = User::create([
                                        'organisation_name' => $reseller_info->name,
                                        'first_name' => $reseller_info->first_name,
                                        'last_name' => $reseller_info->last_name,
                                        'email' => $reseller_info->email[0]->value,
                                        'accounts_email' => null,
                                        'phone' => $reseller_info->phone[0]->value,
                                        'address_line_1' => null,
                                        'address_line_2' => null,
                                        'address_suburb' => null,
                                        'address_state' => null,
                                        'address_postcode' => null,
                                        'address_country' => null,
                                        'password' => bcrypt($this->generateRandomPassword()),
                                    ]);
                                    Transaction::create([
                                        'details' => 'Refferer Not Found. Created Refferer. User ID: ' . $reseller->id,
                                        'log_id' => $log->id,
                                    ]);

                                    DB::table('model_has_roles')->insert([
                                        'role_id' => 3,
                                        'model_type' => 'App\\Models\\User',
                                        'model_id' => $reseller->id,
                                    ]);
                                    Transaction::create([
                                        'details' => 'Add Refferer Role to User: ' . $reseller->id,
                                        'log_id' => $log->id,
                                    ]);


                                } else {
                                    Transaction::create([
                                        'details' => 'Refferer Found. User ID: ' . $reseller->id,
                                        'log_id' => $log->id,
                                    ]);
                                }
                                $this->info('Preparing Reseller Data Data');
                            }

                            // create a deal
                            $deal = new Deal();
                            $deal->name = $deal_data->title;
                            $deal->amount = $deal_data->formatted_value;
                            $deal->status = 'PipeDrive Deal Created';
                            $deal->user_id = $local_user->id;
                            $deal->pipedrive_deal_id = $deal_data->id;
                            $deal->reseller_id = $reseller->id ?? null;
                            $deal->save();
                            $this->info('$deal created - ' . $deal->id);

                            Transaction::create([
                                'details' => 'Created Deal. Deal ID: ' . $deal->id,
                                'log_id' => $log->id,
                            ]);

                            $this->info('$invoicing[\'frequency\'] - ' . $invoicing['frequency']);
                            $this->info('$products_data count - ' . count($products_data));

                            // good so now generate the invoices locally
                            // 879 - Single Project (100%)
                            if ($invoicing['frequency'] == 879) {
                                $invoice = new Invoice();
                                $invoice->user_id = $local_user->id;
                                $invoice->deal_id = $deal->id;
                                $invoice->name = $deal_data->title . ' - Total Amount';
                                $invoice->save();
                                $this->info('$invoice created - ' . $invoice->id);
                                foreach ($products_data as $line_item) {
                                    $interal_line_item = new LineItem();
                                    $interal_line_item->invoice_id = $invoice->id;
                                    $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                    $interal_line_item->name = $line_item->name;
                                    $interal_line_item->code = $line_item->code;
                                    $interal_line_item->amount = $line_item->sum;
                                    $interal_line_item->quantity = $line_item->quantity;
                                    $interal_line_item->save();
                                }
                            }

                            // 880 - Single Project (50% / 50%)
                            if ($invoicing['frequency'] == 880) {
                                for ($i = 0; $i <= 1; $i++) {
                                    if ($i == 0) {
                                        $invoice = new Invoice();
                                        $invoice->user_id = $local_user->id;
                                        $invoice->deal_id = $deal->id;
                                        $invoice->name = $deal_data->title . ' - 50% Deposit';
                                        $invoice->save();
                                        $this->info('$invoice created - ' . $invoice->id);
                                        foreach ($products_data as $line_item) {
                                            $interal_line_item = new LineItem();
                                            $interal_line_item->invoice_id = $invoice->id;
                                            $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                            $interal_line_item->name = 'Deposit - ' . $line_item->name;
                                            $interal_line_item->code = $line_item->code;
                                            $interal_line_item->amount = $line_item->sum / 2;
                                            $interal_line_item->quantity = $line_item->quantity;
                                            $interal_line_item->save();
                                        }
                                    }

                                    $this->info('$products_data count - ' . count($products_data));
                                    if ($i == 1) {
                                        $invoice = new Invoice();
                                        $invoice->user_id = $local_user->id;
                                        $invoice->deal_id = $deal->id;
                                        $invoice->name = $deal_data->title . ' - 50% Balance';
                                        $invoice->save();
                                        $this->info('$invoice created - ' . $invoice->id);
                                        foreach ($products_data as $line_item) {
                                            $interal_line_item = new LineItem();
                                            $interal_line_item->invoice_id = $invoice->id;
                                            $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                            $interal_line_item->name = 'Balance - ' . $line_item->name;
                                            $interal_line_item->code = $line_item->code;
                                            $interal_line_item->amount = $line_item->sum / 2;
                                            $interal_line_item->quantity = $line_item->quantity;
                                            $interal_line_item->save();
                                        }
                                    }
                                }
                            }


                            // 881 - Single Project (50% / 25% / 25%)
                            if ($invoicing['frequency'] == 881) {
                                for ($i = 0; $i <= 2; $i++) {
                                    if ($i == 0) {
                                        $line_items_array = [];
                                        $invoice = new Invoice();
                                        $invoice->user_id = $local_user->id;
                                        $invoice->deal_id = $deal->id;
                                        $invoice->name = $deal_data->title . ' - 50% Deposit';
                                        $invoice->save();
                                        $this->info('$invoice created - ' . $invoice->id);
                                        foreach ($products_data as $line_item) {
                                            $interal_line_item = new LineItem();
                                            $interal_line_item->invoice_id = $invoice->id;
                                            $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                            $interal_line_item->name = 'Deposit - ' . $line_item->name;
                                            $interal_line_item->code = $line_item->code;
                                            $interal_line_item->amount = $line_item->sum / 2;
                                            $interal_line_item->quantity = $line_item->quantity;
                                            $interal_line_item->save();
                                        }
                                    }
                                    if ($i == 1) {
                                        $invoice = new Invoice();
                                        $invoice->user_id = $local_user->id;
                                        $invoice->deal_id = $deal->id;
                                        $invoice->name = $deal_data->title . ' - 25% Progress Payment';
                                        $invoice->save();
                                        $this->info('$invoice created - ' . $invoice->id);
                                        foreach ($products_data as $line_item) {
                                            $interal_line_item = new LineItem();
                                            $interal_line_item->invoice_id = $invoice->id;
                                            $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                            $interal_line_item->name = 'Progress Payment - ' . $line_item->name;
                                            $interal_line_item->code = $line_item->code;
                                            $interal_line_item->amount = $line_item->sum / 4;
                                            $interal_line_item->quantity = $line_item->quantity;
                                            $interal_line_item->save();
                                        }
                                    }
                                    if ($i == 2) {
                                        $invoice = new Invoice();
                                        $invoice->user_id = $local_user->id;
                                        $invoice->deal_id = $deal->id;
                                        $invoice->name = $deal_data->title . ' - 25% Balance';
                                        $invoice->save();
                                        $this->info('$invoice created - ' . $invoice->id);
                                        foreach ($products_data as $line_item) {
                                            $interal_line_item = new LineItem();
                                            $interal_line_item->invoice_id = $invoice->id;
                                            $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                            $interal_line_item->name = 'Balance - ' . $line_item->name;
                                            $interal_line_item->code = $line_item->code;
                                            $interal_line_item->amount = $line_item->sum / 4;
                                            $interal_line_item->quantity = $line_item->quantity;
                                            $interal_line_item->save();
                                        }
                                    }
                                }
                            }

                            // 882 - Retainer
                            if ($invoicing['frequency'] == 882) {
                                for ($i = 1; $i <= $invoicing['terms']; $i++) {
                                    $invoice = new Invoice();
                                    $invoice->user_id = $local_user->id;
                                    $invoice->deal_id = $deal->id;
                                    $invoice->name = $deal_data->title . ' - Payment ' . $i . ' of ' . $invoicing['terms'];
                                    $invoice->save();
                                    $this->info('$invoice created - ' . $invoice->id);
                                    foreach ($products_data as $line_item) {
                                        $interal_line_item = new LineItem();
                                        $interal_line_item->invoice_id = $invoice->id;
                                        $interal_line_item->product_pipedrive_id = $line_item->product_id;
                                        $interal_line_item->name = 'Retainer - ' . $line_item->name;
                                        $interal_line_item->code = $line_item->code;
                                        $interal_line_item->amount = $line_item->sum;
                                        $interal_line_item->quantity = $line_item->quantity;
                                        $interal_line_item->save();
                                    }
                                }
                            }

                            // record the transactions
                            Transaction::create([
                                'details' => 'Generated Invoices Locally. Deal ID: ' . $deal->id,
                                'log_id' => $log->id,
                            ]);

                            $log->pipedrive_log_parsed_to_invoice = true;
                            $log->save();


                        } else {
                            $this->write_local_log_file(serialize([$xero_organisation_name, $xero_address, $xero_contact_name, $xero_contact_email, $xero_contact_phone, $products_data]));
                        }

                    }
                }

            } else {
                $this->info('Deal Not Won - ' . $deal_data->title);
            }

        }


    }
}

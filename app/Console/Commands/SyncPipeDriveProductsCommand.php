<?php

namespace App\Console\Commands;

use App\Models\Products;
use Devio\Pipedrive\Pipedrive;
use Illuminate\Console\Command;

class SyncPipeDriveProductsCommand extends Command
{
    protected $signature = 'sync:pipe-drive-products';

    protected $description = 'Queries all the pipedrive products and updates/creates them in the app';

    public function handle(): void
    {
        // make a connection to pipedrive
        $pipedrive = new Pipedrive(config('app.pipedrive_api_token'));

        // get all products from pipedrive of page 1
        $products = $pipedrive->products->all(['start' => 0, 'limit' => 500]);

        // update or create them
        if (!is_null($products)) {
            if (is_array($products->getContent()->data) && count($products->getContent()->data) > 0) {
                foreach ($products->getContent()->data as $key => $data) {
                    $product = Products::updateOrCreate(['pipedrive_id' => $data->id], ['pipedrive_id' => $data->id, 'name' => $data->name, 'price' => $data->prices[0]->price, 'xero_code' => $data->code,]);
                }
            }
        }

        // go through all the products and remove them if their code is null or empty
        $products = Products::all();
        foreach ($products as $key => $product) {
            if (is_null($product->xero_code) || empty($product->xero_code)) {
                $product->delete();
            }
        }
    }
}

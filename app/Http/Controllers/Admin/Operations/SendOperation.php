<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Mail\SendProposal;
use App\Models\Clients;
use App\Models\User;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

trait SendOperation
{
    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function send()
    {
        CRUD::hasAccessOrFail('send');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = CRUD::getTitle() ?? 'Send ' . $this->crud->entity_name;
        $this->data['entry'] = $this->crud->getCurrentEntry();

        // get the client details
        $client = Clients::findOrfail($this->data['entry']->client_id);
        if (is_null($client)) {
            \Alert::add('error', 'Client not found, please try again.')->flash();
            return \Redirect::to($this->crud->route);
        }

        // test to make sure the email is a valid email
        if (!filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
            \Alert::add('error', 'Client email is not valid, please try again.')->flash();
            return \Redirect::to($this->crud->route);
        }

        // test to make sure that the canva link is valid
        if (!filter_var($this->data['entry']->canva_link, FILTER_VALIDATE_URL)) {
            \Alert::add('error', 'Presenational Sales Deck is not valid, please try again.')->flash();
            return \Redirect::to($this->crud->route);
        }

        // test to make sure that the first name and the last name exist for the client
        if (empty($client->first_name) || empty($client->last_name)) {
            \Alert::add('error', 'Client first name and last name are required, please try again.')->flash();
            return \Redirect::to($this->crud->route);
        }

        // get the assigned staff member of the client / proposal
        $client = Clients::where('id', $this->data['entry']->client_id)->with('user')->first();

        // send the email
        Mail::to($this->data['entry']->client->email)->send(new SendProposal($this->data['entry'], $client, $client->user));

        // success and redirect
        \Alert::add('success', '<strong>Proposal Sent!</strong><br>Good luck!')->flash();
        return \Redirect::to($this->crud->route);

    }

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupSendRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/send', [
            'as' => $routeName . '.send',
            'uses' => $controller . '@send',
            'operation' => 'send',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupSendDefaults()
    {
        CRUD::allowAccess('send');

        CRUD::operation('send', function () {
            CRUD::loadDefaultOperationSettingsFromConfig();
        });

        CRUD::operation('list', function () {
            // CRUD::addButton('top', 'send', 'view', 'crud::buttons.send');
            // CRUD::addButton('line', 'send', 'view', 'crud::buttons.send');
        });
    }

}

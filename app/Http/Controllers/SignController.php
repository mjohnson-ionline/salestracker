<?php

namespace App\Http\Controllers;

use App\Models\Proposals;

class SignController extends Controller
{
	public function index($proposal, $token)
	{
		// get the proposal with the matching id and token
		$client_proposal = Proposals::where('id', $proposal)
			->where('id', $proposal)
			->where('token', $token)
			->first();

		if (!is_null($client_proposal)) {
			$client = $client_proposal->client;
			return view('sign.index', compact('client_proposal', 'client', 'token'));
		} else {
			return view('sign.notfound', compact('client_proposal'));
		}

	}

	public function thankyou($proposal, $token)
	{
		return view('sign.thankyou');

	}

	public function store($proposal)
	{
		// get the proposal
		$client_proposal = Proposals::where('id', $proposal)->first();
		$image = json_encode(request()->signature);

		// if the proposal is not null
		if (!is_null($client_proposal)) {

			//  then save the image against it
			$client_proposal->image = $image;

			// invalidate the token by removing it
			$client_proposal->token = null;

			// add the ip address of the client
			$client_proposal->signed_ip = request()->ip();

			// save the timestamp
			$client_proposal->signed_at = now();

			// save the status as 'signed'
			$client_proposal->status = 'Signed';

			if ($client_proposal->save()) {
				return response()->json(['success' => true]);
			} else {
				return response()->json(['success' => false]);
			}

		}
	}
}
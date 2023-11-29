<?php

use App\Models\User;
use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

function get_title_string($request)
{
	dd($request->path());
}

function makeSafe($value)
{
	return strtolower(preg_replace('/[^a-z|^A-Z|^0-9]/', '', $value));
}

function displayTaxType($value)
{
	switch ($value) {
		case 1:
			echo 'incGST';
			break;
		case 2:
			echo 'exGST';
			break;
		case 3:
			echo '';
			break;
	}
}


function testExistingAgentExists($user_id, $seller_id)
{
	$existing_assignments = DB::table('user_seller')
		->where('user_id', $user_id)
		->where('seller_id', $seller_id)
		->get();


	$return_exist = false;
	foreach ($existing_assignments as $k => $v) {
		if ($v->seller_id == $seller_id) {
			$return_exist = true;
		}
	}

	if ($return_exist == true) {
		return true;
	} else {
		return false;
	}
}

// returns true if the auction is found in a sale
function testAuctionExistsInSale($auction_id, $sale_id, $user_id)
{

	$existing_sales = DB::table('auction_sale')
		->where('user_id', $user_id)
		->where('sale_id', $sale_id)
		->get();


	// massage it baby
	$return_exist = [];
	foreach ($existing_sales as $o_k => $o_v) {
		foreach ($o_v as $ook => $oov) {
			$return_exist[$o_k][$ook] = $oov;
		}
	}

	// return true if it is found
	if (count($return_exist) > 0) {
		foreach ($return_exist as $item) {
			if ($item['auction_id'] == $auction_id) {
				return true;
			}
		}
	} else {
		return false;
	}
}


// tests to see if this auction is in this group and returns true if it does
function testAuctionInGroup($auction_id = null, $group_id = null)
{
	if (!is_numeric($auction_id)) {
		return false;
	}
	if (!is_numeric($group_id)) {
		return false;
	}

	$return_exist = false;
	$auction = Auction::where('id', $auction_id)->with('groups')->first();
	$groups = $auction->groups;
	foreach ($groups as $auction_g_key => $auction_g_value) {
		if ($auction_g_value->id == $group_id) {
			$return_exist = true;
		}
	}

	return $return_exist;
}

// tests to see if this auction is in this group and returns true if it does
function testAuctionInGroupAndPromo($auction_id = null, $group_id = null)
{
	if (!is_numeric($auction_id)) {
		return false;
	}
	if (!is_numeric($group_id)) {
		return false;
	}

	$return_exist = false;
	$auction = Auction::where('id', $auction_id)->with('groups')->first();
	$groups = $auction->groups;
	foreach ($groups as $auction_g_key => $auction_g_value) {
		if ($auction_g_value->id == $group_id) {
			// now check the promotion status of this link
			$link = DB::table('auction_group')
				->where('auction_id', '=', $auction_id)
				->where('group_id', '=', $group_id)
				->first();
			if ($link->promotion == 1) {
				$return_exist = true;
			}
		}
	}

	return $return_exist;
}

function money($value)
{
	if (is_null($value)) return null;
	return number_format($value, '2', '.', '');
}

function testAuctionExistsInSaleForEdit($auction_id, $sale_id, $user_id)
{

	$existing_sales = DB::table('auction_sale')
		->where('user_id', $user_id)
		->get();

	// massage it baby
	$return_exist = [];
	foreach ($existing_sales as $o_k => $o_v) {
		foreach ($o_v as $ook => $oov) {
			$return_exist[$o_k][$ook] = $oov;
		}
	}

	// return true if it is found
	if (count($return_exist) > 0) {
		foreach ($return_exist as $item) {
			if ($item['auction_id'] == $auction_id) {
				return true;
			}
		}
	} else {
		return false;
	}
}

function make_pretty_names($string)
{
	return strip_tags(html_entity_decode($string));
}

/**
 * @param $auction
 * @return false|string
 *
 * Return a nice string of the tax and price
 */
function returnPriceAndTax($auction)
{
	if (isset($auction)) {
		return $auction->tax_type_name . ' $' . number_format($auction->start_price / config('app.gst_divider') * 10, 2) . ' GST';
	} else {
		return false;
	}

}

/**
 * @param Auction $auction
 * @return bool
 *
 * Tests top see if the auction is passed in. This is also added as an attribute on the auction mode, but I want to use it elsewhere in paginated results
 */
function getPassedInStatusRaw(Auction $auction)
{
	if (($auction->bids()->count() == null) && ($auction->end_time < Carbon::now())) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param Auction $auction
 * @return bool
 *
 * Tests top see if the auction is passed in. This is also added as an attribute on the auction mode, but I want to use it elsewhere in paginated results
 */
function getNegotiatingStatusRaw(Auction $auction)
{
	if (($auction->bids()->count() > 1) && ($auction->current_bid < $auction->reserve_price) && ($auction->end_time < Carbon::now())) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param Auction $auction
 * @return bool
 *
 * Tests top see if the auction is passed in. This is also added as an attribute on the auction mode, but I want to use it elsewhere in paginated results
 */
function getIsSoldStatus(Auction $auction)
{
	if (($auction->bids()->count() >= 0) && ($auction->current_bid >= $auction->reserve_price) && ($auction->end_time < Carbon::now())) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param Auction $auction
 * @return bool
 *
 * Tests top see if the auction is passed in. This is also added as an attribute on the auction mode, but I want to use it elsewhere in paginated results
 */
function getAuctionEndedStatus(Auction $auction)
{
	if ($auction->end_time < Carbon::now()) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param Auction $auction
 * @return bool
 *
 * Tests top see if the auction is passed in. This is also added as an attribute on the auction mode, but I want to use it elsewhere in paginated results
 */
function getIsOnMarketStatus(Auction $auction)
{
	if (($auction->current_bid >= $auction->reserve_price) && ($auction->bids()->count() >= 1)) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param $string
 * @return array|false|string|string[]|null
 *
 * Only return alphanumberic characters
 */
function returnOnlyAplha($string)
{

	if (!is_string($string)) {
		return false;
	}

	return preg_replace('/[^A-Za-z0-9 ]/', '', $string);
}

/**
 * @param $string
 * @return array|false|string|string[]|null
 *
 * Extracts the YouTube embed code from a string/youtube url
 */
function exTractYouTube($string)
{

	if (!is_string($string)) {
		return '';
	}

	if ($parts = explode('?v=', $string)) {
		if (!empty($parts[1])) {
			return $parts[1];
		} else {
			return '';
		}
	} else {
		return '';
	}

}

/**
 * @param $name
 * @return false|string
 *
 * Returns a pretty name for the livestream auction status
 */
function makePrettyLiveStreamName($name)
{

	if (!isset($name)) {
		return false;
	}

	if ($name == 'livestream') {
		$name = 'Live Stream';
	}

	if ($name == 'expressions_of_interest') {
		$name = 'Register Interest';
	}

	if ($name == 'timed') {
		$name = 'Timed Auction';
	}

	return ucwords(str_replace('_', ' ', $name));
}
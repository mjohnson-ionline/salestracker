	<?php

	use App\Models\Sale;
	use App\Models\User;
	use App\Models\Auction;
	use App\Models\Invoice;

	function success($message)
	{
		Session::flash('message', $message);
	}

	function error($message)
	{
		Session::flash('error', $message);
	}

	// used to convert protected objects from xero and grab the contact id
	function getProtectedValue($obj, $name)
	{
		$array = (array)$obj;
		$prefix = chr(0) . '*' . chr(0);
		return $array[$prefix . $name];
	}

	/**
	 * @param int $sale_id
	 * @param int $auction_order_of_sale
	 * @return false|\Illuminate\Support\Collection
	 *
	 * Sorts and slices the relates auctions in a sale
	 */
	function returnSortedAndSlicedSaleAuctions($sale_id = '', $auction_order_of_sale = '')
	{

		// get the sale, the associated auctions and sort them
		$sale = Sale::where('id', $sale_id)->with('auctions')->first();
		$related_auctions = collect($sale->auctions)->sortBy('order_of_sale', SORT_NUMERIC, false);

		// remove the items with no order of sale, that are not published, then append users
		foreach ($related_auctions as $index => $related_auction) {
			if (empty($related_auction->order_of_sale)) {
				unset($related_auctions[$index]);
			}
			if ($related_auction->status != 'published') {
				unset($related_auctions[$index]);
			}
			$related_auction->users = User::find($related_auction->user_id)->first();
		}

		// slice the next auctions
		return $related_auctions->slice($auction_order_of_sale, 4);

	}

	/**
	 * @param $sale_id
	 * @param $auction_order_of_sale
	 * @param $auction_id
	 * @return \Illuminate\Support\Collection|void
	 *
	 * Gets the next 4 auction items in a clearance sale
	 *
	 */
	function returnSortedAndSlicedSaleAuctionsTimedAndLiveStream($sale_id = '', $auction_order_of_sale = '', $auction_id = '')
	{
		$auctions_arry = [];
		$related_auctions = collect();
		$paginated = collect();
		$sales = Sale::where('id', $sale_id)->with('auctions')->first();
		if (!empty($sales)) {
			foreach ($sales->auctions as $auction) {
				if (!is_null($auction->order_of_sale)) {
					$auctions_arry[$auction->order_of_sale]['order'] = $auction->order_of_sale;
					$auctions_arry[$auction->order_of_sale]['id'] = $auction->id;
				}
			}
		}

		if (count($auctions_arry) > 0) {

			$pointer = null;
			ksort($auctions_arry); // sort by order of sale id
			$reset_array = array_values($auctions_arry);
			foreach ($reset_array as $key => $ordered_auctions) {
				if ($ordered_auctions['id'] == $auction_id) {
					$current = $reset_array[$key]; // current spot in array
					$next_four = array_slice($reset_array, $key + 1, 4); // now slice out the next 4
				}
			}

			if (isset($next_four)) {
				foreach ($next_four as $auction) {
					$related_auctions[] = Auction::where('id', $auction['id'])->first();
				}
			}

			if (isset($related_auctions)) {
				return $related_auctions;
			}
		}


	}

	/**
	 * Returns the previous or next clearance sale item given the current lot order in this sale
	 */
	function returnPreviousNextClearanceSaleItem($auction, $direction)
	{

		// get the sale, the associated auctions and sort them
		$sale = Sale::where('id', $auction->sale[0]['id'])->with('auctions')->first();
		$related_auctions = collect($sale->auctions)->sortBy('order_of_sale', SORT_NUMERIC);

		// remove the items with no order of sale, that are not published, then append users
		foreach ($related_auctions as $index => $related_auction) {
			if (empty($related_auction->order_of_sale)) {
				unset($related_auctions[$index]);
			}
			if ($related_auction->status != 'published') {
				unset($related_auctions[$index]);
			}
			$related_auction->users = User::find($related_auction->user_id)->first();
		}

		// slide the prev/next auction items, getting the auction items in the same sale @todo - need to query the same sale
		$slice = $related_auctions->slice($auction->order_of_sale, 1);

		if (isset($slice->flatten()->first()->order_of_sale)) {
			if ($direction == 'previous') {
				$return_auction = Auction::where('order_of_sale', $slice->flatten()->first()->order_of_sale - 1)
					->first();
			} elseif ($direction == 'next') {
				$return_auction = Auction::where('order_of_sale', $slice->flatten()->first()->order_of_sale + 1)
					->first();
			}
		}

		if (isset($return_auction)) {
			return $return_auction->id;
		} else {
			return false;
		}

	}

	/**
	 * ionline specific dd that will only render results locally from the ionline offices
	 */
	function idd($anything)
	{
		if ($_SERVER['REMOTE_ADDR'] == '120.88.118.188') {
			dd($anything);
		}
	}

	/**
	 * Gets all invoices with against and auction, if something is found then the invoice has been generated already
	 *
	 * @param $auction_id
	 * @return Invoice|false
	 */
	function hasInvoiceGenerated($auction_id)
	{
		$invoice = Invoice::where('auction_id', $auction_id)->first();
		if (!is_null($invoice)) {
			return $invoice->id;
		} else {
			return false;
		}
	}

	/**
	 * @param $phone
	 * @return false|mixed|string
	 *
	 * Tests the phone string to see if it in the correct international string, and if not, manipulate it.
	 */
	function testAndMutatePhone($phone)
	{

		if (!isset($phone)) {
			return false;
		}

		if (!stristr($phone, '+61')) {
			return '+61' . substr($phone, 1); // remove the 0
		} else {
			return $phone;
		}

	}

    // write a function that will turn the standard australian mobile phone number into the international format
    function testAndMutatePhoneFromGH($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) == 10) {
            $phone = '+61' . $phone;
        }
        return $phone;
    }

    /*function existsAndIterable($var)
    {
        if (!is_null($var) && !is_iterable($var)) {
            return true;
        } else {
            return false;
        }
    }*/

    /*function existsAndIterable($obj): bool
    {
        if ($obj instanceof \Illuminate\Support\Collection || is_array($obj)) {
            return count($obj) > 0;
        } else if (is_iterable($obj) && count($obj) > 0) {
            return true;
        } else  {
            return false;
        }
    }*/

    /**
     * Checks if an object or array is iterable, exists, and is not empty.
     *
     * @param mixed $obj The object or array to check.
     * @return bool Returns true if the object or array is iterable, exists, and is not empty, false otherwise.
     */
    /*function isIterableAndNotEmpty(mixed $obj): bool
    {
        if (is_array($obj)) {
            return count($obj) > 0;
        } elseif ($obj instanceof \Illuminate\Support\Collection) {
            return $obj->isNotEmpty();
        } elseif (is_object($obj)) {
            try {
                $reflection = new ReflectionClass($obj);
                if ($reflection->implementsInterface(\Iterator::class)) {
                    return $obj->valid();
                }
            } catch (Throwable $e) {
                return false;
            }
        }
        return false;
    }*/

    /**
     * Checks if an object or array is not iterable, does not exist, or is empty.
     *
     * @param mixed $obj The object or array to check.
     * @return bool Returns true if the object or array is not iterable, does not exist, or is empty, false otherwise.
     */
    /*function isNotIterableOrEmpty(mixed $obj): bool
    {
        if (!isset($obj)) {
            return true;
        }
        if (is_array($obj)) {
            return count($obj) === 0;
        } elseif ($obj instanceof \Illuminate\Support\Collection) {
            return $obj->isEmpty();
        } elseif (is_object($obj)) {
            try {
                $reflection = new ReflectionClass($obj);
                if ($reflection->implementsInterface(\Iterator::class)) {
                    return !$obj->valid();
                }
            } catch (Throwable $e) {
                return true;
            }
        }
        return true;
    }*/

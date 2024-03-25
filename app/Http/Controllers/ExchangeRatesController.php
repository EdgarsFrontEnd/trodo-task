<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurrencyExchangeRates;
use Illuminate\Support\Facades\Http;

class ExchangeRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $exchangeRates = CurrencyExchangeRates::orderBy('id', 'ASC')
        ->skip(($page - 1) * $limit)
        ->take($limit)
        ->get();

        $totalCount = CurrencyExchangeRates::count();

        return response()->json([
            'data' => $exchangeRates,
            'total' => $totalCount,
        ]);
    }

    public function get($from, $to)
    {
        $exchangeRates = CurrencyExchangeRates::where('base_currency', $from)
            ->where('target_currency', $to)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($exchangeRates) {
            return response()->json($exchangeRates);
        }

        return "No exchange rates found";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newExchangeRate = new CurrencyExchangeRates();
        $newExchangeRate['base_currency'] = $request->exchangeRates['base_currency'];
        $newExchangeRate['target_currency'] = $request->exchangeRates['target_currency'];
        $newExchangeRate['exchange_rate'] = $request->exchangeRates['exchange_rate'];
        $newExchangeRate->save();

        return $newExchangeRate;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $exchangeRate = CurrencyExchangeRates::find($id);

        if ($exchangeRate) {
            $exchangeRate['base_currency'] = $request->exchangeRates['base_currency'];
            $exchangeRate['target_currency'] = $request->exchangeRates['target_currency'];
            $exchangeRate['exchange_rate'] = $request->exchangeRates['exchange_rate'];
            $exchangeRate->save();

            return $exchangeRate;
        }

        return "Exchange rate not found";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exchangeRate = CurrencyExchangeRates::find($id);

        if ($exchangeRate) {
            $exchangeRate->delete();
            return "Exchange rate successfully deleted.";
        }

        return "Exchange rate not found";
    }

    public function fetchExchangeRates(Request $request)
    {
        $apiKey = env('ANYAPI_API_KEY');
        $apiEndpoint = 'https://anyapi.io/api/v1/exchange/rates';
        $baseParam = $request->input('base') ? '&base=' . $request->input('base') : '';

        try {
            $response = Http::get("$apiEndpoint?apiKey=$apiKey" . $baseParam);
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch exchange rates'], 500);
        }
    }

    public function fetchConvertedCurrency(Request $request)
    {
        $apiKey = env('ANYAPI_API_KEY');
        $apiEndpoint = 'https://anyapi.io/api/v1/exchange/convert';

        try {
            $response = Http::get($apiEndpoint, [
                'base' => $request->input('base'),
                'to' => $request->input('to'),
                'amount' => $request->input('amount'),
                'apiKey' => $apiKey,
            ]);
            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch converted currency'], 500);
        }
    }
}

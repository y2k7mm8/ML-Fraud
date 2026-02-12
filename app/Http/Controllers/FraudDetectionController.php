<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FraudDetectionController extends Controller
{
    public function index()
    {
        return view('fraud_detection');
    }

    public function checkTransaction(Request $request)
    {
        $data = $request->all();

        // Accept both frontend field names and internal names
        $validated = $request->validate([
            'amount' => 'nullable|numeric',
            // frontend uses `transactions_last_hour`, legacy/internal uses `tx_last_hour`
            'tx_last_hour' => 'nullable|integer',
            'transactions_last_hour' => 'nullable|integer',
            // frontend uses `distance_from_last_location`, internal uses `distance_km`
            'distance_km' => 'nullable|numeric',
            'distance_from_last_location' => 'nullable|numeric',
        ]);

        $amount = $validated['amount'] ?? 0;
        $txLastHour = $validated['tx_last_hour'] ?? ($validated['transactions_last_hour'] ?? 0);
        $distanceKm = $validated['distance_km'] ?? ($validated['distance_from_last_location'] ?? 0);

        // Example logic for fraud detection
        $fraudScore = ($amount / 1000) + ($txLastHour * 2) + ($distanceKm / 100);
        $isFraud = $fraudScore > 50;

        return response()->json([
            'fraud_score' => $fraudScore,
            'is_fraud' => $isFraud,
        ]);
    }
}

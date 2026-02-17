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

        // Sanitize inputs
        $amount = max(0.0, floatval($amount));
        $txLastHour = max(0, intval($txLastHour));
        $distanceKm = max(0.0, floatval($distanceKm));

        // Weighted scoring with sensible normalization:
        // - amount: logarithmic scale to reduce impact of extremely large values
        // - txLastHour: linear, capped to avoid runaway scores
        // - distanceKm: linear scaled down
        $amountScore = log10($amount + 1) * 10; // small amounts -> small score, big amounts grow slowly
        $txScore = min($txLastHour, 100) * 1.5; // cap at 100 transactions
        $distanceScore = min($distanceKm, 1000) / 10; // cap distance at 1000 km, scale down

        $fraudScore = $amountScore + $txScore + $distanceScore;
        $threshold = 60; // adjustable threshold for flagging
        $isFraud = $fraudScore >= $threshold;

        return response()->json([
            'fraud_score' => $fraudScore,
            'is_fraud' => $isFraud,
            'components' => [
                'amount_score' => $amountScore,
                'tx_score' => $txScore,
                'distance_score' => $distanceScore,
                'threshold' => $threshold,
            ],
        ]);
    }
}

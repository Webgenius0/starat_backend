<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PostBoost;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\FuncCall;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    use apiresponse;
    public function __construct()
    {
        // Ensure the secret API key is being used on the server-side
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }


    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'nullable|exists:posts,id',
            'budget' => 'nullable|numeric|min:1',
            'boost_start_date' => 'nullable|date|after:today', // Ensure it's a future date
            'boost_duration_days' => 'nullable|integer|min:1', // The duration for the boost in days
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            $payment = Payment::create([
                'user_id' => auth()->user()->id,
                'amount' => $request->budget,
                'post_id' => $request->post_id,
                'payment_method' => 'stripe',
                'status' => 'pending',
            ]);

            // If boosting a post, process the budget and start date
            if ($request->has('post_id') && $request->has('budget')) {
                $postId = $request->input('post_id');
                $budget = $request->input('budget');
                $boostStartDate = $request->input('boost_start_date');
                $boostDurationDays = $request->input('boost_duration_days');

                // Store the boost details, including the start date and duration
                $postBoost = PostBoost::create([
                    'user_id' => auth()->user()->id,
                    'post_id' => $postId,
                    'budget' => $budget,
                    'status' => 'pending',
                    'payment_id' => $payment->id,
                    'boost_start_date' => $boostStartDate,
                    'boost_duration_days' => $boostDurationDays, // Store the duration
                ]);
            }

            $line_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) round($budget * 100), // Amount in cents (Stripe expects this in the smallest unit)
                    'product_data' => [
                        'name' => 'Boost for Post', // A description of the one-time product
                        'description' => 'Boosting a post for better reach', // Optional description
                    ],
                ],
                'quantity' => 1, // Only 1 item being sold (the one-time payment)
            ];

            // Create Stripe Checkout session (same as before)
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => $line_items,
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&order=' . $payment->id,
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'user_id' => auth()->user()->id,
                    'payment_id' => $payment->id,
                    'boost_id' =>  $postBoost->id,
                ],
            ]);
            return $this->success($session->url, 'Stripe Session created. Redirect to this URL', 200);
        } catch (\Exception $e) {
            // Log the error
            return $this->error([],'Something went wrong. Please try again later.',500);
        }
    }

    public function successs()
    {
        return response()->json('success');
    }

    public function cancel()
    {
        return response()->json('cancel');
    }
}

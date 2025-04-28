<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PostBoost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;


class WebhookController extends Controller
{
    public function __construct()
    {
        // Ensure the secret API key is being used on the server-side
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function handle(Request $request)
    {
        $stripeApiKey = config('services.stripe.secret');
        Stripe::setApiKey($stripeApiKey);
        $endpoint_secret = config('services.stripe.webhook_secret');

        $sig_header = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Signature Verification Failed', [
                'error_message' => $e->getMessage(),
                'payload' => $payload,
                'signature' => $sig_header
            ]);
            return response()->json(['status' => 'error', 'message' => 'Webhook signature verification failed'], 400);
        }

        // Handle different webhook event types
        if ($event) {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleOneTimePaymentCompleted($session);
                    break;
                default:
                    Log::info("Unhandled event type: " . $event->type);
                    break;
            }
        }

        return response()->json(['status' => 'success']);
    }

    private function handleOneTimePaymentCompleted($session)
    {
        $payment = Payment::find($session->metadata->payment_id);
        $user = $session->metadata->user_id;
        $boost = PostBoost::find($session->metadata->boost_id);
        Log::info($boost);
        if ($payment) {
            $payment->status = 'success';
            $payment->save();
        } else {
            Log::warning('Payment not found', ['payment_id' => $session->metadata->payment_id]);
        }

        if ($boost) {
            $boost->status = 'running';
            $boost->save();
        } else {
            Log::warning('Boost not found', ['boost_id' => $session->metadata->boost_id]);
        }
    }
}

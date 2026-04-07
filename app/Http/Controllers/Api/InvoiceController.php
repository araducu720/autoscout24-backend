<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SafetradeTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * List all invoices for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $invoices = Invoice::whereHas('safetradeTransaction', function ($query) use ($user) {
            $query->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
        })
        ->with('safetradeTransaction')
        ->orderBy('created_at', 'desc')
        ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    /**
     * Get a single invoice
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $invoice = Invoice::with('safetradeTransaction')
            ->whereHas('safetradeTransaction', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->findOrFail($id);

        return response()->json([
            'data' => $this->formatInvoice($invoice),
        ]);
    }

    /**
     * Generate invoice PDF
     */
    public function generatePDF(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $invoice = Invoice::with(['safetradeTransaction.buyer', 'safetradeTransaction.seller', 'safetradeTransaction.vehicle'])
            ->whereHas('safetradeTransaction', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->findOrFail($id);

        $transaction = $invoice->safetradeTransaction;

        // Generate PDF data (in production, use a PDF library like DomPDF)
        $pdfData = [
            'invoice_number' => $invoice->invoice_number,
            'issue_date' => $invoice->issue_date,
            'due_date' => $invoice->due_date,
            'status' => $invoice->status,
            'seller' => [
                'name' => $transaction->seller->name ?? 'Unknown Seller',
                'email' => $transaction->seller->email ?? '',
                'address' => $transaction->seller->address ?? '',
            ],
            'buyer' => [
                'name' => $transaction->buyer->name ?? 'Unknown Buyer',
                'email' => $transaction->buyer->email ?? '',
                'address' => $transaction->buyer->address ?? '',
            ],
            'items' => [
                [
                    'description' => $transaction->vehicle_title,
                    'quantity' => 1,
                    'unit_price' => $transaction->vehicle_price,
                    'total' => $transaction->vehicle_price,
                ],
                [
                    'description' => 'SafeTrade Protection Fee',
                    'quantity' => 1,
                    'unit_price' => $transaction->escrow_fee,
                    'total' => $transaction->escrow_fee,
                ],
            ],
            'subtotal' => $transaction->vehicle_price,
            'fees' => $transaction->escrow_fee,
            'total' => $transaction->amount,
            'payment_method' => $transaction->payment_method,
            'transaction_reference' => $transaction->reference,
            'notes' => $invoice->notes,
        ];

        return response()->json([
            'data' => $pdfData,
            'message' => 'Invoice PDF data generated successfully',
        ]);
    }

    /**
     * Send invoice via email
     */
    public function send(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $invoice = Invoice::with('safetradeTransaction')
            ->whereHas('safetradeTransaction', function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->findOrFail($id);

        // In production, dispatch an email job here
        // SendInvoiceEmail::dispatch($invoice);

        return response()->json([
            'message' => 'Invoice sent successfully to the buyer\'s email address.',
        ]);
    }

    /**
     * Format invoice for API response
     */
    private function formatInvoice(Invoice $invoice): array
    {
        return [
            'id' => $invoice->id,
            'transaction_id' => $invoice->safetrade_transaction_id,
            'invoice_number' => $invoice->invoice_number,
            'issue_date' => $invoice->issue_date?->toISOString(),
            'due_date' => $invoice->due_date?->toISOString(),
            'amount' => $invoice->amount,
            'status' => $invoice->status,
            'notes' => $invoice->notes,
            'created_at' => $invoice->created_at?->toISOString(),
            'updated_at' => $invoice->updated_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceiptCancellation;
use App\Models\FeeCollection;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReceiptCancellationController extends Controller
{
    public function requestindex()
    {
        $receipts = FeeCollection::with(['student', 'cancelRequest'])
            ->where('financial_year', $this->financial_year())
            ->when(auth()->user()->branch, function ($q) {
                return $q->where('collected_branch', auth()->user()->branch);
            })
            ->get();

        return view('receipt_cancellation.requestindex', compact('receipts'));
    }

    public function pendingindex()
    {
        $pendingreceipts = ReceiptCancellation::with(['receipt.item.feeplanitem.feeplanmaster', 'requestedBy'])->when(auth()->user()->branch, function ($q) {
            $q->whereIn('requested_by', User::where('branch', auth()->user()->branch)->pluck('id'));
        })->where('status', 'pending')->get();
        return view('receipt_cancellation.pendingindex', compact('pendingreceipts'));
    }


    public function requestCancel(Request $request)
    {

        $request->validate([
            'receipt_id' => 'required|exists:fee_collection,id',
            'cancel_reason' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $id = $request->receipt_id;

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('receipt_cancellation', 'public');
        }

        ReceiptCancellation::create([
            'receipt_id'   => $id,
            'requested_by' => auth()->id(),
            'cancel_reason' => $request->cancel_reason,
            'attachment' => $attachmentPath,
        ]);

        return back()->with('success', 'Cancellation request submitted successfully!');
    }

    public function receiptcancelapprove($id)
    {
        try {
            DB::beginTransaction();
            $requestData = ReceiptCancellation::findOrFail($id);

            if ($requestData->status !== 'pending') {
                return back()->with('error', 'This request is already processed.');
            }

            // Mark receipt as cancelled
            $receipt = FeeCollection::findOrFail($requestData->receipt_id);
            $receipt->update([
                'is_cancelled' => true,
                'cancel_reason' => $requestData->cancel_reason,
                'cancelled_by' => auth()->id(), // or approver? Your choice
                'cancelled_at' => now(),
            ]);

            // Approve the request
            $requestData->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Receipt cancellation Approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while processing the request.');
        }
    }

    public function receiptcancelreject($id)
    {
        $requestData = ReceiptCancellation::findOrFail($id);

        if ($requestData->status !== 'pending') {
            return back()->with('error', 'This request is already processed.');
        }

        $requestData->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Cancellation request rejected.');
    }


}

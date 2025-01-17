<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends BaseController
{
    /**
     * Display a listing of payments.
     */
    public function index(): JsonResponse
    {
        try {
            $payments = Payment::with(['work', 'source'])->get();
            return $this->sendResponse($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            logError('PaymentController', 'index', $e->getMessage());
            return $this->sendError('Error retrieving payments', [], 500);
        }
    }

    /**
     * Store a newly created payment.
     */
    public function store(PaymentRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $payment = Payment::create($validated);
            return $this->sendResponse($payment, 'Payment created successfully');
        } catch (\Exception $e) {
            logError('PaymentController', 'store', $e->getMessage());
            return $this->sendError('Error creating payment', [], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function details($id): JsonResponse
    {
        try {
            $payment = Payment::with(['work'])->findOrFail($id);
            return $this->sendResponse($payment, 'Payment details retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Payment not found');
        } catch (\Exception $e) {
            logError('PaymentController', 'details', $e->getMessage());
            return $this->sendError('Error retrieving payment details', [], 500);
        }
    }

    /**
     * Update the specified payment.
     */
    public function update(PaymentRequest $request, $id): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($id);
            $validated = $request->validated();
            $payment->update($validated);
            return $this->sendResponse($payment, 'Payment updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Payment not found');
        } catch (\Exception $e) {
            logError('PaymentController', 'update', $e->getMessage());
            return $this->sendError('Error updating payment', [], 500);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            return $this->sendResponse([], 'Payment deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Payment not found');
        } catch (\Exception $e) {
            logError('PaymentController', 'destroy', $e->getMessage());
            return $this->sendError('Error deleting payment', [], 500);
        }
    }
}

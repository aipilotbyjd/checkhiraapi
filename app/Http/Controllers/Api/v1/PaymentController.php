<?php

namespace App\Http\Controllers\Api\v1;

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
            $payments = Payment::with(['user', 'work'])->get();
            return $this->sendResponse($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            logError('PaymentController', 'index', $e->getMessage());
            return $this->sendError('Error retrieving payments', [], 500);
        }
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'category' => 'required|string',
                'description' => 'nullable|string',
                'source' => 'required|string',
                'date' => 'required|date',
                'work_id' => 'required|exists:works,id',
                'user_id' => 'required|exists:users,id',
                'is_active' => 'nullable|in:0,1'
            ]);

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
            $payment = Payment::with(['user', 'work'])->findOrFail($id);
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
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'amount' => 'sometimes|numeric',
                'category' => 'sometimes|string',
                'description' => 'nullable|string',
                'source' => 'sometimes|string',
                'date' => 'sometimes|date',
                'work_id' => 'sometimes|exists:works,id',
                'user_id' => 'sometimes|exists:users,id',
                'is_active' => 'nullable|in:0,1'
            ]);

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

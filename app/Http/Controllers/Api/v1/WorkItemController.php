<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\WorkItem;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkItemController extends BaseController
{
    /**
     * Display a listing of work items.
     */
    public function index(): JsonResponse
    {
        try {
            $workItems = WorkItem::with('work')->get();
            return $this->sendResponse($workItems, 'Work items retrieved successfully.');
        } catch (\Exception $e) {
            logError('WorkItemController', 'index', $e->getMessage());
            return $this->sendError('Error retrieving work items.', [], 500);
        }
    }

    /**
     * Store a new work item.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string',
                'diamond' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'work_id' => 'required|exists:works,id',
                'is_active' => 'nullable|in:0,1'
            ]);

            $workItem = WorkItem::create($validated);
            return $this->sendResponse($workItem, 'Work item created successfully.');
        } catch (\Exception $e) {
            logError('WorkItemController', 'store', $e->getMessage());
            return $this->sendError('Error creating work item.', [], 500);
        }
    }

    /**
     * Display the specified work item.
     */
    public function details($id): JsonResponse
    {
        try {
            $workItem = WorkItem::with('work')->findOrFail($id);
            return $this->sendResponse($workItem, 'Work item retrieved successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work item not found.', [], 404);
        } catch (\Exception $e) {
            logError('WorkItemController', 'details', $e->getMessage());
            return $this->sendError('Error retrieving work item.', [], 500);
        }
    }

    /**
     * Update the specified work item.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $workItem = WorkItem::findOrFail($id);

            $validated = $request->validate([
                'type' => 'sometimes|string',
                'diamond' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'work_id' => 'sometimes|exists:works,id',
                'is_active' => 'nullable|in:0,1'
            ]);

            $workItem->update($validated);
            return $this->sendResponse($workItem, 'Work item updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work item not found.', [], 404);
        } catch (\Exception $e) {
            logError('WorkItemController', 'update', $e->getMessage());
            return $this->sendError('Error updating work item.', [], 500);
        }
    }

    /**
     * Remove the specified work item.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $workItem = WorkItem::findOrFail($id);
            $workItem->delete();
            return $this->sendResponse([], 'Work item deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work item not found.', [], 404);
        } catch (\Exception $e) {
            logError('WorkItemController', 'destroy', $e->getMessage());
            return $this->sendError('Error deleting work item.', [], 500);
        }
    }
}

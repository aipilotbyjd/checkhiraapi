<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Work;
use App\Http\Requests\WorkRequest;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkController extends BaseController
{
    /**
     * Display a listing of the works.
     */
    public function index(): JsonResponse
    {
        try {
            $works = Work::with(['workItems'])->get();
            return $this->sendResponse($works, 'Works fetched successfully');
        } catch (\Exception $e) {
            logError('WorkController', 'index', $e->getMessage());
            return $this->sendError('Failed to fetch works', [], 500);
        }
    }

    /**
     * Store a newly created work.
     */
    public function store(WorkRequest $request): JsonResponse
    {
        try {
            $work = Work::create($request->validated());
            $work->workItems()->createMany($request->entries);
            return $this->sendResponse($work->load(['workItems']), 'Work created successfully');
        } catch (\Exception $e) {
            logError('WorkController', 'store', $e->getMessage());
            return $this->sendError('Failed to create work', [], 500);
        }
    }

    /**
     * Display the specified work.
     */
    public function details($id): JsonResponse
    {
        try {
            $work = Work::with(['workItems'])->findOrFail($id);
            return $this->sendResponse($work, 'Work fetched successfully');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work not found', [], 404);
        } catch (\Exception $e) {
            logError('WorkController', 'details', $e->getMessage());
            return $this->sendError('Failed to fetch work', [], 500);
        }
    }

    /**
     * Update the specified work.
     */
    public function update(WorkRequest $request, $id): JsonResponse
    {
        try {
            $work = Work::findOrFail($id);
            $work->update($request->validated());
            $work->workItems()->delete();
            $work->workItems()->createMany($request->entries);
            return $this->sendResponse($work->load(['workItems']), 'Work updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work not found', [], 404);
        } catch (\Exception $e) {
            logError('WorkController', 'update', $e->getMessage());
            return $this->sendError('Failed to update work', [], 500);
        }
    }

    /**
     * Remove the specified work.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $work = Work::findOrFail($id);
            if ($work->delete()) {
                $work->workItems()->delete();
                return $this->sendResponse([], 'Work deleted successfully');
            }
            return $this->sendError('Failed to delete work', [], 500);
        } catch (ModelNotFoundException $e) {
            return $this->sendError('Work not found', [], 404);
        } catch (\Exception $e) {
            logError('WorkController', 'destroy', $e->getMessage());
            return $this->sendError('Failed to delete work', [], 500);
        }
    }
}

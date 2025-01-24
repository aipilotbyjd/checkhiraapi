<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Work;
use App\Http\Requests\WorkRequest;
use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkController extends BaseController
{
    /**
     * Display a listing of the works.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $query = Work::query()->with(['workItems']);
            $filter = $request->input('filter', 'all');

            // Apply filters if provided
            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->input('is_active'));
            }

            switch ($filter) {
                case 'today':
                    $query->where('date', date('Y-m-d'));
                    break;
                case 'week':
                    $query->whereBetween('date', [date('Y-m-d', strtotime('last Monday')), date('Y-m-d', strtotime('next Sunday'))]);
                    break;
                case 'month':
                    $query->whereMonth('date', date('m'));
                    break;
                default:
                    break;
            }

            $works = $query->latest()->paginate($perPage);

            $total = Work::getTotalWorks($filter);

            return $this->sendResponse([
                'works' => $works,
                'total' => $total
            ], 'Works fetched successfully');
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
            $work->total = $work->workItems->sum(function ($item) {
                return $item->price * $item->diamond;
            });
            $work->save();
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
            $work->total = $work->workItems->sum(function ($item) {
                return $item->price * $item->diamond;
            });
            $work->save();
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
            // Delete child records first to avoid foreign key constraint violation
            $work->workItems()->delete();
            if ($work->delete()) {
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

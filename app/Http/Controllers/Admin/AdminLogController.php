<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    /**
     * Display a listing of admin activity logs.
     * Requirements: 12.4
     */
    public function index(Request $request): View
    {
        $query = AdminLog::with('admin')
            ->orderBy('created_at', 'desc');

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Get unique admins for filter dropdown
        $admins = \App\Models\User::where('role', 'admin')
            ->orderBy('name')
            ->get();

        // Get unique model types
        $modelTypes = AdminLog::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type),
                ];
            });

        return view('admin.logs.index', compact('logs', 'admins', 'modelTypes'));
    }

    /**
     * Display the specified admin log.
     */
    public function show(AdminLog $log): View
    {
        $log->load('admin');

        return view('admin.logs.show', compact('log'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizePermission('system.view');

        $logs = AuditLog::query()
            ->with('user')
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->string('module')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'modules' => AuditLog::query()->distinct()->orderBy('module')->pluck('module'),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['module', 'user_id', 'from', 'to']),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        $this->authorizePermission('system.view');

        $auditLog->load('user');

        return view('admin.audit-logs.show', ['log' => $auditLog]);
    }

    private function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()?->hasPermission($permission), 403);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class AdminController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        return view('admin.index', compact('tenants'));
    }

    public function suspend(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->suspended = true;
        $tenant->save();
        return back()->with('success', "Blog '{$id}' suspendido.");
    }

    public function unsuspend(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->suspended = false;
        $tenant->save();
        return back()->with('success', "Blog '{$id}' reactivado.");
    }
}

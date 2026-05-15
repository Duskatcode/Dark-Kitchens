<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasRole(Role::ADMIN)) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->hasRole(Role::CLIENT)) {
            return redirect()->route('client.dashboard');
        }

        if ($user?->hasRole(Role::COOK)) {
            return redirect()->route('cook.dashboard');
        }

        abort(403);
    }
}

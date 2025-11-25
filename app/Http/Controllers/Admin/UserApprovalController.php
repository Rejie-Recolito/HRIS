<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        if (! $user || ! $user->is_admin) {
            abort(403);
        }

        $pending = User::where('is_approved', false)->get();

        return view('admin.users', ['pendingUsers' => $pending]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->is_admin) {
            abort(403);
        }

        $user->is_approved = true;
        $user->save();

        // Redirect to login with approval message for the user
        return redirect()->route('login')->with('status', 'approved');
    }
}

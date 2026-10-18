<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(): View
    {
        $agents = User::whereIn('role', [UserRole::Admin, UserRole::Agent])
            ->withCount(['assignedTickets as active_tickets_count' => function ($q) {
                $q->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress]);
            }])
            ->withCount(['assignedTickets as resolved_tickets_count' => function ($q) {
                $q->where('status', TicketStatus::Resolved);
            }])
            ->orderBy('name')
            ->get();

        return view('agents.index', compact('agents'));
    }

    public function create(): View
    {
        return view('agents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', new Enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('agents.index')
            ->with('success', "Team member {$validated['name']} registered successfully.");
    }

    public function edit(User $agent): View
    {
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($agent->id)],
            'role' => ['required', new Enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', Password::defaults()],
        ]);

        $agent->name = $validated['name'];
        $agent->email = $validated['email'];
        $agent->role = UserRole::from($validated['role']);
        $agent->phone = $validated['phone'] ?? null;
        $agent->is_active = (bool) $validated['is_active'];

        if (! empty($validated['password'])) {
            $agent->password = Hash::make($validated['password']);
        }

        $agent->save();

        return redirect()->route('agents.index')
            ->with('success', "Agent {$agent->name} updated successfully.");
    }

    public function destroy(User $agent): RedirectResponse
    {
        if ($agent->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $agent->delete();

        return redirect()->route('agents.index')
            ->with('success', 'Agent account removed.');
    }
}

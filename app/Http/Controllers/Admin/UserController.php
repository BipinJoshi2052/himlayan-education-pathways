<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\SaveUserAction;
use App\DTOs\Admin\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

final class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('roles')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User,
            'allRoles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request, SaveUserAction $action): RedirectResponse
    {
        $user = $action->handle(UserData::fromArray($request->validated()));

        return redirect()->route('admin.users.edit', $user)->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
            'allRoles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user, SaveUserAction $action): RedirectResponse
    {
        $action->handle(UserData::fromArray($request->validated()), $user);

        return redirect()->route('admin.users.edit', $user)->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => "You can't delete your own account."]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}

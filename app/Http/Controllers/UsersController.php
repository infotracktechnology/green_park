<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Options;
use Session;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $users = User::where([['id', '!=', auth()->user()->id], ['type', '!=', 'admin']])->get();
        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {

        return view('users.create');
    }

    public function store(Request $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'branch_ids' => implode(',', $request->branch ?? []),
            'password' => bcrypt($request->password),
            'type' => $request->type,
            'branch' => $request->branch[0] ?? 1,
            'menu' => [],
        ]);

        return to_route('users.index')->with('success', 'User created successfully');
    }

    public function MenuAssign(User $user, Request $request)
    {

        $menus = collect(Options::where('type', 'admin menu')->value('value') ?? []);

        if ($request->isMethod('POST')) {
            $input = $request->input('menus', []);
            $final_menu = $menus->map(function ($item) use ($input) {
                $title = $item['title'];
                if (!isset($input[$title])) return null;
                if (isset($item['submenu'])) {
                    $item['submenu'] = collect($item['submenu'])->filter(fn($sub) => isset($input[$title]['submenu'][$sub['title']]))->values()->all();
                    return count($item['submenu']) ? $item : null;
                }
                return isset($input[$title]['self']) ? $item : null;
            })->filter()->values();
            $user->update(['menu' => $final_menu->toArray()]);
            return redirect()->back()->with('success', 'User Menu Permissions Updated');
        }

        $authorized_titles = collect($user->menu)->flatMap(function ($item) {
            return collect([$item['title']])->merge(collect($item['submenu'] ?? [])->pluck('title'));
        })->all();
        return view('users.menu', compact('user', 'menus', 'authorized_titles'));
    }



    public function edit(User $user)
    {

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->username = $request->username;
        $user->email = $request->email;
        $user->branch_ids = implode(',', $request->branch ?? []);
        $user->branch = $request->branch[0] ?? 1;
        if (isset($request->reset_password)) {
            $user->password = bcrypt($request->password);
        }
        $user->type = $request->type;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function BranchSwitch(?User $user, Request $request) 
    {
        $user = ($user && $user->exists) ? $user : auth()->user();

        if ($request->isMethod('POST')) {
            $user->branch = $request->branch;
            $user->save();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => true,
                    'message' => 'Branch switched successfully',
                    'active_branch' => $user->branch,
                    'user' => $user
                ], 200);
            }

            return redirect()->route('admin.home')->with('success', 'Branch switched successfully');
        }

        $branchIds = array_filter(explode(',', $user->branch_ids ?? ''));
        $branches_list = Branch::whereIn('id', $branchIds)->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => true,
                'user' => $user,
                'active_branch' => $user->branch,
                'branches_list' => $branches_list
            ], 200);
        }

        return view('users.branch_switch', compact('user', 'branches_list'));
    }



    public function destroy(User $user)
    {
        $user->delete();
        return to_route('users.index')->with('success', 'User deleted successfully');
    }
}

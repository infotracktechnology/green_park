<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Session;
class UsersController extends Controller{

    public function index(Request $request){
        $users = User::where('id','!=', auth()->user()->id)->get();
        return view('users.index',compact('users'));
    }

    public function create(Request $request){
        return view('users.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'string', 'min:8','same:password'],
        ]);
        $request['password'] = bcrypt($request->password);
        $user = User::Create($request->all());
        return to_route('users.index');
    }

    public function edit(User $user){
        return view('users.edit',compact('user'));
    }

    public function update(Request $request, User $user){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        $user->update($request->all());
        return to_route('users.index');
    }

    public function destroy(User $user){
        $user->delete();
        return to_route('users.index')->with('success', 'User deleted successfully');
    }
}

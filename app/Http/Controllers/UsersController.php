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
use Session;
class UsersController extends Controller{

    public function index(Request $request){
        $users = User::where([['id', '!=', auth()->user()->id],['type', '!=', 'admin']])->get();
        return view('users.index',compact('users'));
    }

    public function create(Request $request){
       
        return view('users.create');
    }

    public function store(Request $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => $request->type,
            'branch' => $request->branch,
            'menu' => "[]",
        ]);

        return to_route('users.index')->with('success', 'User created successfully');
    }
    
    

    public function edit(User $user)
    {
      
        return view('users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        $user->username = $request->username;
        $user->email = $request->email;
        $user->branch = $request->branch;
        if(isset($request->reset_password)){
        $user->password = bcrypt($request->password);
        }
        $user->type = $request->type;
        $user->save();
    
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    
    

    public function destroy(User $user){
        $user->delete();
        return to_route('users.index')->with('success', 'User deleted successfully');
    }
}

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
        $users = User::where('id','!=', auth()->user()->id)->get();
        $branches = DB::table('branch')->select('id', 'name')->get();
        return view('users.index',compact('users' ,'branches'));
    }

    public function create(Request $request){
       
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'type' => ['required', 'in:1,2'],
        ]);
    
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => $request->type,
            'branch' => $request->branch,
        ]);
        $user->save();
        return to_route('users.index');
    }
    
    

    public function edit(User $user)
    {
      
        return view('users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
           
        ]);
    
        $user->username = $request->username;
        $user->email = $request->email;
        $user->branch = $request->branch;
        $user->password = bcrypt($request->password);
        $user->save();
    
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    
    

    public function destroy(User $user){
        $user->delete();
        return to_route('users.index')->with('success', 'User deleted successfully');
    }
}

<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Session;
class SettingsController extends Controller{

    public function index(Request $request){
        $settings = Setting::all(); 
        return view('settings.index',compact('settings'));
    }

    public function create(Request $request){
        return view('settings.create');
    }

    public function store(Request $request){

        $request->validate([
            'attribute' => ['required', 'string'],
        ]);
        $data = $request->except('_token','_method');
        
        if($request->hasFile('value')){
            $data['value'] = $request->file('value')->store('setting','public');
        }

        $setting = Setting::Create($data);
        return to_route('settings.index');
    }

    public function edit(Request $request, Setting $setting){
        return view('settings.edit',compact('setting'));
    }

    public function update(Request $request,Setting $setting){
        $request->validate([
            'attribute' => ['required', 'string'],
            'value' => ['required', 'string'],
        ]);

        $setting->update($request->all());
        return to_route('settings.index');
    }

    public function destroy(Setting $setting){
        $setting->delete();
        return to_route('settings.index')->with('success', 'Setting deleted successfully');
    }
}

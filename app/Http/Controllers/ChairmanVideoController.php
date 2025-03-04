<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Chairmanvideo;
use Illuminate\Support\Facades\DB;

class ChairmanVideoController extends Controller
{
    public function index()
    {
        $chairmanvideos = Chairmanvideo::all();
        $branches = Branch::all();
        $branchList = DB::table('branch')->pluck('name', 'id')->toArray();
        return view('chairmanvideo.index', compact('chairmanvideos', 'branches', 'branchList'));
    }
    public function create()
    {
        $branches = Branch::all();
        return view('chairmanvideo.create', compact('branches'));
    }
    public function store(Request $request)
    {
        $chairmanvideos = new Chairmanvideo();
        $chairmanvideos->branch_id = implode(',', $request->branch_id);
    $chairmanvideos->coaching_type = implode(',', $request->coaching_type);
        $chairmanvideos->gender = $request->gender;
        $chairmanvideos->title = $request->title;
        $chairmanvideos->link = $request->link;
        if ($request->hasFile('attachment')) {
            $fileName = time() . '.' . $request->attachment->extension();
            $request->attachment->move(public_path('chairman/video'), $fileName);
            $chairmanvideos->attachment = 'chairman/video/' . $fileName;
        } else {
            $chairmanvideos->attachment = null;
        }
        $chairmanvideos->save();
        return redirect()->route('chairmanvideo.index')
            ->with('success', 'Chairman video created successfully.');
    }
    public function destroy(Request $request, Chairmanvideo $chairmanvideo)
    {
        $chairmanvideo->delete();
        session()->flash('success', 'Video deleted successfully');
        return to_route('chairmanvideo.index');
    }
    public function chairmanvideo(Request $request)
    {
        $chairmanvideo = auth()->user()->chairmanvideo();
       
       return view('student.chairmanvideo', compact('chairmanvideo'));
    }

    public function video(Request $request, $id){
        return view('layouts.video', compact('id'));
    }
}

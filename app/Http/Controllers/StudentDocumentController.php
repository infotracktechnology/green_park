<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Options;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentDocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('student_id', auth()->user()->student_id)->latest()->get();
        $options = Options::where('type', 'Document Option')->first();
        $options = $options->value ?? [];
        return view('student.documentupload', compact('documents', 'options'));
    }


    public function store(Request $request)
    {

        $studentId = auth()->user()->student_id;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = $studentId.'_'.$request->document_name.'.'.$file->getClientOriginalExtension();
            $file->move('uploads/documents', $fileName);
            Document::create([
                'student_id' => $studentId,
                'document_name' => $request->document_name,
                'file' => 'uploads/documents/'.$fileName,
            ]);
        }

        return redirect()->route('document.index')->with('success', 'Document uploaded successfully.');
    }


    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $filePath = base_path('uploads/documents/' . basename($document->file));
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $document->delete();

        return redirect()->route('document.index')->with('success', 'Document deleted successfully.');
    }
}

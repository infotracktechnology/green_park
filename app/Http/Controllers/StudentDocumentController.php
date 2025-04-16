<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class StudentDocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->get(); // Fetch documents from DB
        return view('student.documentupload', compact('documents'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
          
            'file_name' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf|max:2048',
        ]);
    
        $studentId = auth()->user()->student_id;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move('documents', $fileName);
            Document::create([
                'student_id' => $studentId,
                'file_name' => $request->file_name,
                'file' => 'documents/' . $fileName, 
            ]);
        }
    
        return redirect()->route('document.upload')->with('success', 'Document uploaded successfully.');
    }
    

    
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        if (file_exists(public_path($document->file))) {
            unlink(public_path($document->file)); // Delete the file from server
        }
        $document->delete(); // Delete the record from DB
        return redirect()->route('document.upload')->with('success', 'Document deleted successfully.');
    }

}

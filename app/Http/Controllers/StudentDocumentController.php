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
        $originalName = $file->getClientOriginalName();
        $fileName = $studentId . '_' . $originalName;
        $file->move('documents', $fileName);
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
        $filePath = base_path('documents/' . basename($document->file)); // Get the full path of the file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        $document->delete();
    
        return redirect()->route('document.upload')->with('success', 'Document deleted successfully.');
    }
   

}

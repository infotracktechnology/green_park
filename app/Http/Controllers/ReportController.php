<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;


class ReportController extends Controller
{
    public function section_exam(Request $request)
    {
        $sections = DB::table('student')->select('section')->distinct()->orderBy('section')->get();
        $tests = Exam::groupBy('name')->get();
        $test_name = $request->test_name ?? 0;
        if($request->query('type') == 'overall') {
            $testIds = Exam::where('name', $test_name)->pluck('id')->toArray();
            $testIds = $testIds != '' ? $testIds : 0;
            $section = $request->section;
            $results = DB::table('exam_answer as a')->join('student as b', 'a.student_id', '=', 'b.id')->join('branch as c', 'b.campus', '=', 'c.id')->whereIn('a.test_id', $testIds)->where('b.section', $request->section)->select('a.test_id','a.student_id','a.mode as stmode',DB::raw('GROUP_CONCAT(DISTINCT a.subject) as subjects'),DB::raw('SUM(a.mark) as mark'),'b.student_name','c.name','b.coaching_type','b.gender','b.section','a.test_id')->groupBy('a.student_id')->orderBy('test_id')->orderBy('student_name')->get();
            if(count($results) == 0) {
                return back()->with('error', 'No data found');
            }
            $testids = implode(',', $testIds);
            return view('report.overall_print', compact('results','test_name','section','testids'));
        }

        if($request->query('type') == 'omr') {
            $testIds = Exam::where('name', $test_name)->pluck('id')->toArray();
            $testIds = $testIds != '' ? $testIds : 0;
            $section = $request->section;
            $answers = DB::table('exam_answer as a')->join('student as b', 'a.student_id', '=', 'b.id')->whereIn('a.test_id', $testIds)->where('b.section', $request->section)->selectRaw("a.*,b.section,b.student_name")->orderBy('test_id')->orderBy('student_name')->orderBy('q_no')->get();
            if(count($answers) == 0) {
                return back()->with('error', 'No data found');
            }
            $studentAnswers = [];
            foreach ($answers as $answer) {
                $studentAnswers[$answer->student_id][] = $answer;
            }
            $formattedData = [];
            foreach ($studentAnswers as $studentId => $studentData) {
                $chunks = array_chunk($studentData, ceil(count($studentData) / 4));
                $pages = array_chunk($chunks, 4);

                foreach ($pages as $page) {
                    $formattedData[] = $page;
                }
            }


            return view('report.omr_print', compact('formattedData','test_name','section'));
        }

        return view('report.section_exam', compact('sections', 'tests', 'test_name'));
    }

    // function GenerateOverall($results, $test_name, $section, $testIds) {
    //     $testids = implode(',', $testIds);
    //     $pdf = new TCPDF();
    //     $pdf->SetCreator('PDF-Laravel');
    //     $pdf->SetTitle('Overall Report');
    //     $pdf->SetSubject('Overall Report');
    //     $pdf->SetKeywords('example, test, guide');
    //     $pdf->SetMargins(3, 10, 15);
    //     $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //     $pdf->AddPage();
    //     $html = '<!DOCTYPE html><html><head><style>body { font-size: 9.5px; font-family:Verdana, Geneva, sans-serif;margin: 0; padding: 0; } table { border-collapse: collapse;border-color: black; } table th { font-weight:bold;text-align: center;}</style></head><body><h3>GREEN PARK COACHING CENTRE, NAMAKKAL</h3><h4>'.$test_name.'</h4><h4>Sec: '.$section.'</h4><table cellspacing="0" cellpadding="1" border="1" nobr="true">';
    //     $html .= '<thead><tr>';
    //     $html .= '<th>S.no</th><th>Roll No</th><th>Q Type</th><th width="20%">Name</th>';
      
    //     $subjects = explode(',', $results[0]->subjects);
    //     foreach($subjects as $subject) {
    //         $html .= '<th colspan="4" width="16%">'.$subject.'</th>';
    //     }
    //     $html .= '<th>Total</th></tr><tr><th></th><th></th><th></th><th></th>';
    //     foreach($subjects as $subject) {
    //         $html .= '<th width="4%">R</th width="4%"><th>W</th width="4%"><th>L</th><th width="4%">T</th>';
    //     }
    //     $html .= '<th></th></tr></thead>';
    //     $html .= '<tbody>';
    //     foreach($results as $i => $result) {
    //         $html .= '<tr>';
    //         $html .= '<td style="text-align: center;">'.($i+1).'</td><td style="text-align: center;">'.$result->student_id.'</td><td style="text-align: center;">'.$result->test_id.'</td><td width="20%">'.$result->student_name.'</td>';
    //         foreach($subjects as $subject) {
    //             $marks = DB::select("SELECT sum(mark=4)r,sum(mark=-1)w,sum(mark=0)l,sum(mark)tot,subject FROM `exam_answer` where test_id in($testids) and student_id=$result->student_id and subject='$subject'");
    //             $html .= '<td width="4%" style="text-align: center;">'.$marks[0]->r.'</td><td width="4%" style="text-align: center;">'.$marks[0]->w.'</td><td width="4%" style="text-align: center;">'.$marks[0]->l.'</td><td width="4%" style="text-align: center;">'.$marks[0]->tot.'</td>';
    //         }
    //         $html .= '<td style="text-align: center;">'.$result->mark.'</td>';
    //         $html .= '</tr>';
    //     }
    //     $html .= '</tbody></table></body></html>';
    //     $pdf->writeHTML($html, true, false, false, false, '');
    //     return $pdf->Output("Overall Report $test_name.pdf", 'D');
    // }


  
}
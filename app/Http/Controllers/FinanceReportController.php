<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\FeeCollection;
use App\Models\FeesPlanItem;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class FinanceReportController extends Controller
{
    public function dfc(Request $request){
        $branchselect = Branch::when(auth()->user()->branch, function ($query) { $query->where('id', auth()->user()->branch); })->pluck('name', 'id');
        $reports = collect([]);
        if($request->filled('branch_id') && $request->filled('payment_mode')){
            $reports = FeeCollection::where('financial_year', $this->financial_year)->where('collected_branch', $request->input('branch_id'))->where('payment_date', date('Y-m-d'))->where('is_cancelled', 0)->when($request->input('payment_mode') != 'all', function ($query) use ($request) { $query->where('payment_mode', $request->input('payment_mode')); })->get();
        }
        return view('finance.dfc',compact('branchselect','reports'));
    }

    public function collectionreport(Request $request){
        $branchselect = Branch::when(auth()->user()->branch, function ($query) { $query->where('id', auth()->user()->branch); })->pluck('name', 'id');

        // Base filters
        $branchId = $request->branch_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $paymentMode = $request->payment_mode ?? 'all';
        $whichWise = $request->which_wise;
        $reporttype = $request->report_type ?? 'summary';

        if ($whichWise === 'segmentwise' && $reporttype === 'summary') {

            // 1) Get distinct segments actually used in students
            $segmentIds = DB::table('student')
                ->whereNotNull('segments')
                ->where('segments', '!=', '')
                ->pluck('segments')
                ->flatMap(function ($s) {
                    return explode(',', $s);
                })
                ->unique()
                ->filter()
                ->values();

            $segmentsList = DB::table('segments')
                ->whereIn('id', $segmentIds)
                ->pluck('name', 'id');   // [id => name]

            // 2) Build dynamic SELECT
            $selectParts = [];
            foreach ($segmentsList as $segId => $segName) {
                $cleanName = str_replace('`', '', (string)$segName);
                $aliasAmt  = $cleanName.'_Amount';
                $aliasStd  = $cleanName.'_Students';

                $selectParts[] = "
                    SUM(
                        CASE WHEN FIND_IN_SET({$segId}, s.segments) > 0
                            THEN fci.payamount ELSE 0
                        END
                    ) AS `{$aliasAmt}`
                ";

                $selectParts[] = "
                    COUNT(DISTINCT
                        CASE WHEN FIND_IN_SET({$segId}, s.segments) > 0
                            THEN fci.studentid
                        END
                    ) AS `{$aliasStd}`
                ";
            }

            // 3) Filters (same pattern as fee type)
            $where = "WHERE fc.is_cancelled = 0";

            if ($branchId !== 'all' && !empty($branchId)) {
                $where .= " AND fc.collected_branch = {$branchId}";
            }

            $where .= " AND fc.payment_date BETWEEN '{$startDate}' AND '{$endDate}'";

            if ($paymentMode !== 'all' && !empty($paymentMode)) {
                $pm = str_replace("'", "''", $paymentMode);
                $where .= " AND fc.payment_mode = '{$pm}'";
            }

            // 4) Final SQL
            $sql = "
                SELECT
                    fc.payment_date,
                    ".implode(', ', $selectParts).",
                    SUM(fci.payamount) AS Total_Amount,
                    COUNT(DISTINCT fci.studentid) AS Total_Students
                FROM fee_collection fc
                JOIN feecollection_item fci ON fc.id = fci.fee_collection_id
                JOIN student s ON s.id = fc.student_id
                {$where}
                GROUP BY fc.payment_date
                ORDER BY fc.payment_date ASC
            ";

            $segmentreport = DB::select($sql);
            $reportCollection = collect($segmentreport);
            $grandTotal = $reportCollection->sum('Total_Amount');

            $segmentTotals = [];
            foreach ($segmentsList as $segName) {
                $segmentTotals[$segName.'_Amount'] = $reportCollection->sum($segName.'_Amount');
            }

            return view('finance.collectionreport', compact(
                'branchselect',
                'segmentreport',
                'segmentsList',
                'branchId',
                'startDate',
                'endDate',
                'paymentMode',
                'grandTotal',
                'segmentTotals',
                'whichWise',
                'reporttype'
            ));
        }

        if ($whichWise === 'segmentwise' && $reporttype === 'detail') {

            $segmentIds = DB::table('student')
                ->whereNotNull('segments')
                ->where('segments', '!=', '')
                ->pluck('segments')
                ->flatMap(fn($s) => explode(',', $s))
                ->unique()
                ->filter()
                ->values();

            $segmentsList = DB::table('segments')
                ->whereIn('id', $segmentIds)
                ->pluck('name', 'id');

            $selectParts = [];
            foreach ($segmentsList as $segId => $segName) {
                $cleanName = str_replace('`', '', (string)$segName);
                $aliasAmt  = $cleanName.'_Amount';

                $selectParts[] = "
                    SUM(
                        CASE WHEN FIND_IN_SET({$segId}, s.segments) > 0
                            THEN fci.payamount ELSE 0
                        END
                    ) AS `{$aliasAmt}`
                ";
                // if you need Students count column, add a CASE COUNT same as summary
            }

            $where = "WHERE fc.is_cancelled = 0";

            if ($branchId !== 'all' && !empty($branchId)) {
                $where .= " AND fc.collected_branch = {$branchId}";
            }

            if (!empty($startDate) && !empty($endDate)) {
                $where .= " AND fc.payment_date BETWEEN '{$startDate}' AND '{$endDate}'";
            }

            if ($paymentMode !== 'all' && !empty($paymentMode)) {
                $pm = str_replace("'", "''", $paymentMode);
                $where .= " AND fc.payment_mode = '{$pm}'";
            }

            $sql = "
                SELECT
                    fc.payment_date,
                    fc.receipt_no,
                    fc.payment_mode,
                    s.student_name,
                    s.student_id,
                    ".implode(', ', $selectParts).",
                    SUM(fci.payamount) AS Total_Amount,
                    COUNT(DISTINCT fci.studentid) AS Total_Students
                FROM fee_collection fc
                JOIN feecollection_item fci ON fc.id = fci.fee_collection_id
                JOIN student s ON s.id = fc.student_id
                {$where}
                GROUP BY
                    fc.payment_date,
                    fc.receipt_no,
                    s.student_name,
                    s.student_id,
                    fc.payment_mode
                ORDER BY
                    fc.payment_date ASC,
                    fc.receipt_no ASC
            ";

            $segmentdetailReport = DB::select($sql);
            $detailReportCollection = collect($segmentdetailReport);
            $grandTotal = $detailReportCollection->sum('Total_Amount');

            $segmentTotals = [];
            foreach ($segmentsList as $segName) {
                $segmentTotals[$segName.'_Amount'] = $detailReportCollection->sum($segName.'_Amount');
            }
            // dd($segmentTotals);
            return view('finance.collectionreport', compact(
                'branchselect',
                'segmentdetailReport',
                'segmentsList',
                'branchId',
                'startDate',
                'endDate',
                'paymentMode',
                'reporttype',
                'whichWise',
                'grandTotal',
                'segmentTotals'
            ));
        }



        
        // // inside collectionreport(Request $request) -- replace existing segmentwise block with this:
        // if ($whichWise === 'segmentwise') {
        //     $branchId   = $request->branch_id;
        //     $courseId   = $request->course_id;
        //     $batchId    = $request->batch_id;
        //     $sectionId  = $request->section_id;
        //     $studentId  = $request->student_id;
        //     $academicYr = $request->academic_year;

        //     /* ----------------------------------------------------
        //     1) FETCH ALL ACTIVE SEGMENTS (for table header)
        //     ---------------------------------------------------- */
        //     $segments = DB::table('segments')
        //         ->where('is_active', 1)
        //         ->pluck('name', 'id');   // [id => name]

        //     if ($segments->isEmpty()) {
        //         return view('finance.segment_due_report', [
        //             'segments' => [],
        //             'report'   => []
        //         ]);
        //     }

        //     $segmentIds = implode(',', array_keys($segments->toArray())); // "1,2,3..."

        //     /* ----------------------------------------------------
        //     2) BUILD STUDENT FILTERS  
        //     ---------------------------------------------------- */
        //     $where = " WHERE 1=1 ";

        //     if ($academicYr) $where .= " AND s.academic_year = '$academicYr' ";
        //     if ($branchId && $branchId != 'all') $where .= " AND s.campus = $branchId ";
        //     if ($courseId && $courseId != 'all') $where .= " AND s.course = $courseId ";
        //     if ($batchId && $batchId != 'all') $where .= " AND s.batch = $batchId ";
        //     if ($sectionId && $sectionId != 'all') $where .= " AND s.section = $sectionId ";
        //     if ($studentId && $studentId != 'all') $where .= " AND s.id = $studentId ";

        //     /* ----------------------------------------------------
        //     3) FETCH ALL STUDENTS WITH SEGMENTS
        //     ---------------------------------------------------- */
        //     $students = DB::select("
        //         SELECT s.id, s.student_name, s.student_id, s.segments
        //         FROM student s
        //         $where
        //     ");

        //     if (empty($students)) {
        //         return view('finance.segment_due_report', [
        //             'segments' => $segments,
        //             'report'   => []
        //         ]);
        //     }

        //     /* ----------------------------------------------------
        //     4) PREPARE DYNAMIC SELECT PARTS (for summary table)
        //     ---------------------------------------------------- */
        //     $selectParts = [];
        //     foreach ($segments as $segId => $segName) {
        //         $colAmount   = "`SEG_{$segId}_Amount`";
        //         $colStudents = "`SEG_{$segId}_Students`";

        //         $selectParts[] = "SUM(CASE WHEN fp.segment_id = $segId THEN fp.amount ELSE 0 END) AS $colAmount";
        //         $selectParts[] = "COUNT(DISTINCT CASE WHEN fp.segment_id = $segId THEN s.id END) AS $colStudents";
        //     }

        //     $selectSQL = implode(", ", $selectParts);

        //     /* ----------------------------------------------------
        //     5) FINAL QUERY  
        //     (Fetch feeplan_item and join with collection)
        //     ---------------------------------------------------- */
        //     $sql = "
        //         SELECT 
        //             s.id AS student_dbid,
        //             s.student_id AS admno,
        //             s.student_name,
        //             $selectSQL,
        //             SUM(fp.amount) AS Total_Amount,
        //             COUNT(DISTINCT s.id) AS Total_Students
        //         FROM student s
        //         JOIN feeplan_item fp ON FIND_IN_SET(fp.segment_id, s.segments)
        //         LEFT JOIN feecollection_item fci 
        //             ON fci.studentid = s.id 
        //             AND fci.feeplan_item_id = fp.id
        //         $where
        //         GROUP BY 
        //             s.id, s.student_id, s.student_name
        //         ORDER BY s.student_name ASC
        //     ";

        //     $report = DB::select($sql);

        //     return view('finance.collectionreport', [
        //         'segments' => $segments,
        //         'segmentreport'   => $report,
        //         'branchselect' => $branchselect
        //     ]);
        // }


        // ---- Only run FeeType wise report ----
        if ($whichWise === 'feetypewise' && $reporttype === 'summary') {

            // 1) Fetch Fee Types from feeplan_master (Not from feeplan_item anymore)
            $feeTypes = DB::table('feeplan_item')
                ->distinct()
                ->pluck('fee_type')
                ->filter()
                ->values();

            // 2) Build dynamic SELECT columns
            $selectParts = [];
            foreach ($feeTypes as $type) {

                $clean = str_replace('`', '', (string)$type);
                $cleanSql = str_replace("'", "''", $clean);

                $amountAlias = $clean . '_Amount';
                $studentsAlias = $clean . '_Students';

                $selectParts[] = "
                    SUM(CASE WHEN fpi.fee_type = '{$cleanSql}' 
                            THEN fci.payamount ELSE 0 END) AS `{$amountAlias}`
                ";

                $selectParts[] = "
                    COUNT(DISTINCT CASE WHEN fpi.fee_type = '{$cleanSql}'
                                        THEN fci.studentid END) AS `{$studentsAlias}`
                ";
            }


            // 3) Build WHERE clause
            $where = "WHERE fc.is_cancelled = 0";  // <-- IMPORTANT: exclude cancelled receipts

            if ($branchId !== 'all' && !empty($branchId)) {
                $where .= " AND fc.collected_branch = {$branchId}";
            }

            $where .= " AND fc.payment_date BETWEEN '{$startDate}' AND '{$endDate}'";

            if ($paymentMode !== 'all' && !empty($paymentMode)) {
                $pm = str_replace("'", "''", $paymentMode);
                $where .= " AND fc.payment_mode = '{$pm}'";
            }

            // 4) Final Query
            $sql = "
                SELECT 
                    fc.payment_date,

                    " . implode(", ", $selectParts) . ",

                    SUM(fci.payamount) AS Total_Amount,
                    COUNT(DISTINCT fci.studentid) AS Total_Students

                FROM fee_collection fc
                JOIN feecollection_item fci 
                    ON fc.id = fci.fee_collection_id
                JOIN feeplan_item fpi
                    ON fci.feeplan_item_id = fpi.id
                JOIN feeplan_master fm
                    ON fpi.feeplan_master_id = fm.id

                {$where}

                GROUP BY fc.payment_date
                ORDER BY fc.payment_date ASC
            ";

            $report = DB::select($sql);

            $reportCollection = collect($report);

            $grandTotal = $reportCollection->sum('Total_Amount');

            $feeTypeTotals = [];
            foreach ($feeTypes as $type) {
                $feeTypeTotals[$type."_Amount"] = $reportCollection->sum($type."_Amount");
            }

            return view('finance.collectionreport', compact(
                'branchselect', 'report', 'feeTypes',
                'branchId', 'startDate', 'endDate', 'paymentMode','grandTotal', 'feeTypeTotals'
            ));
        }


        if ($whichWise === 'feetypewise' && $reporttype === 'detail') {

            // 1) Get distinct fee types
            $feeTypes = DB::table('feeplan_item')
                ->distinct()
                ->pluck('fee_type')
                ->filter()
                ->values();

            // 2) Build dynamic select expressions
            $selectParts = [];
            foreach ($feeTypes as $type) {
                $clean = str_replace('`', '', (string)$type);
                $cleanForSql = str_replace("'", "''", $clean);

                $amtAlias = $clean . "_Amount";
                $studAlias = $clean . "_Students";

                $selectParts[] =
                    "SUM(CASE WHEN fp.fee_type = '{$cleanForSql}' THEN fci.payamount ELSE 0 END) AS `{$amtAlias}`";

                $selectParts[] =
                    "COUNT(DISTINCT CASE WHEN fp.fee_type = '{$cleanForSql}' THEN fci.studentid END) AS `{$studAlias}`";
            }

            // 3) Build filters
            $where = "WHERE fc.is_cancelled = 0";

            if ($branchId !== 'all' && !empty($branchId)) {
                $where .= " AND fc.collected_branch = {$branchId}";
            }

            if (!empty($startDate) && !empty($endDate)) {
                $where .= " AND fc.payment_date BETWEEN '{$startDate}' AND '{$endDate}'";
            }

            if ($paymentMode !== 'all' && !empty($paymentMode)) {
                $pm = str_replace("'", "''", $paymentMode);
                $where .= " AND fc.payment_mode = '{$pm}'";
            }

            // 4) GROUP BY student/date/receipt for DETAIL
            $sql = "
                SELECT 
                    fc.payment_date,
                    fc.receipt_no,
                    fc.payment_mode,
                    s.student_name AS student_name,
                    s.student_id,
                    " . implode(", ", $selectParts) . ",
                    SUM(fci.payamount) AS Total_Amount,
                    COUNT(DISTINCT fci.studentid) AS Total_Students
                FROM fee_collection fc
                JOIN feecollection_item fci ON fc.id = fci.fee_collection_id
                JOIN feeplan_item fp ON fp.id = fci.feeplan_item_id
                JOIN student s ON s.id = fc.student_id
                {$where}
                GROUP BY 
                    fc.payment_date,
                    fc.receipt_no,
                    s.student_name,
                    s.student_id,
                    fc.payment_mode
                ORDER BY 
                    fc.payment_date ASC,
                    fc.receipt_no ASC
            ";

            $detailReport = DB::select($sql);

            $detailReportCollection = collect($detailReport);

            $grandTotal = $detailReportCollection->sum('Total_Amount');

            // FEE TYPE TOTALS
            $feeTypeTotals = [];
            foreach ($feeTypes as $type) {
                $feeTypeTotals[$type."_Amount"] = $detailReportCollection->sum($type."_Amount");
            }
            return view(
                'finance.collectionreport',
                compact(
                    'branchselect',
                    'detailReport',
                    'feeTypes',
                    'branchId',
                    'startDate',
                    'endDate',
                    'paymentMode',
                    'reporttype',
                    'grandTotal',
                    'feeTypeTotals'
                )
            );
        }



        // ---- Run normal report ----

        return view('finance.collectionreport', compact('branchselect'));
    }

    public function dueReport(Request $req)
    {
        $academicYear = $req->academic_year;
        $branchId     = $req->branch_id;
        $course       = $req->course;
        $batch        = $req->batch;
        $section      = $req->section;
        $studentId    = $req->student;
        $feeType      = $req->fee_type;
        $instalment   = $req->instalment;

         $branchselect = Branch::when(auth()->user()->branch, function ($query) { $query->where('id', auth()->user()->branch); })->pluck('name', 'id');

         $academicYearselect = AcademicYear::pluck('academic_year')->toArray();

        /* -------------------------------------------------------
        STEP 1: FETCH ALL STUDENTS BASED ON FILTERS
        --------------------------------------------------------*/
        if($req->report_type == 'detail') {
            
        $students = Student::with('branch')
            ->when($academicYear, function($q) use ($academicYear) {
                $q->where('academic_year', $academicYear);
            })
            ->when($branchId && $branchId != 'all', function($q) use ($branchId) {
                $q->where('campus', $branchId);
            })
            ->when($course, function($q) use ($course) {
                $q->where('course', $course);
            })
            ->when($batch, function($q) use ($batch) {
                $q->where('batch', $batch);
            })
            ->when($section, function($q) use ($section) {
                $q->where('section', $section);
            })
            ->when($studentId, function($q) use ($studentId) {
                $q->where('id', $studentId);
            })
            ->select('id', 'student_id', 'student_name', 'campus', 'course', 'batch', 'section', 'segments')
            ->get();


        /* -------------------------------------------------------
        STEP 2: BUILD DUE REPORT PER STUDENT
        --------------------------------------------------------*/
        $report = [];

        foreach ($students as $stu) {

            if (!$stu->segments) continue;

            $segmentIds = explode(",", $stu->segments);

            /* ---------------------------------------------------
            2A: TOTAL FEE FROM feeplan_item (segment matched)
            ----------------------------------------------------*/
            $totalFeeQuery = DB::table('feeplan_item AS fpi')
                ->whereIn('fpi.segment_id', $segmentIds)
                ->when($academicYear, fn($q) => $q->where('fpi.academic_year', $academicYear))
                ->where('fpi.branch_id', $stu->campus)
                // ->when($branchId && $branchId != 'all', fn($q) => $q->where('fpi.branch_id', $branchId))
                // ->when($course, fn($q) => $q->where('fpi.course', 'LIKE', "%{$course}%"))
                // ->when($batch, fn($q) => $q->where('fpi.batch', 'LIKE', "%{$batch}%"))
                // ->when($feeType, fn($q) => $q->where('fpi.fee_type', $feeType))
                // ->when($instalment, fn($q) => $q->where('fpi.instalment', $instalment))
                ->sum('fpi.amount');

            /* ---------------------------------------------------
            2B: TOTAL PAID FROM feecollection_item
            ----------------------------------------------------*/
            $totalPaidQuery = DB::table('feecollection_item AS fci')
                ->join('fee_collection AS fc', 'fc.id', '=', 'fci.fee_collection_id')
                ->where('fci.studentid', $stu->id)
                ->where('fc.is_cancelled', 0)
                ->when($academicYear, fn($q) => $q->where('fci.academic_year', $academicYear))
                ->when($branchId && $branchId != 'all', fn($q) => $q->where('fc.collected_branch', $branchId))
                ->when($feeType, fn($q) =>
                    $q->join('feeplan_item AS fpi2', 'fpi2.id', '=', 'fci.feeplan_item_id')
                    ->where('fpi2.fee_type', $feeType)
                )
                ->when($instalment, fn($q) =>
                    $q->join('feeplan_item AS fpi3', 'fpi3.id', '=', 'fci.feeplan_item_id')
                    ->where('fpi3.instalment', $instalment)
                )
                ->sum('fci.payamount');


            $dueAmount = $totalFeeQuery - $totalPaidQuery;

                $report[] = [
                    'student_id'     => $stu->student_id,
                    'student_name'   => $stu->student_name,
                    'branch'         => optional($stu->branch)->name,
                    'course'         => $stu->course,
                    'batch'          => $stu->batch,
                    'section'        => $stu->section,
                    'total_fee'      => $totalFeeQuery,
                    'collected_fee'  => $totalPaidQuery,
                    'due'            => $dueAmount,
                ];
        }

        return view('finance.duereport', compact(
            'report',
            'academicYear',
            'branchId',
            'course',
            'batch',
            'section',
            'studentId',
            'feeType',
            'instalment',
            'branchselect',
            'academicYearselect'
        ));
        }

        if($req->report_type == 'summary'){

            $students = DB::table('student');

            if ($branchId && $branchId != 'all')     $students->where('campus', $branchId);
            if ($course && $course != 'all')         $students->where('course', $course);
            if ($batch && $batch != 'all')           $students->where('batch', $batch);
            if ($section && $section != 'all')       $students->where('section', $section);
            if ($studentId && $studentId != 'all')   $students->where('id', $studentId);

            $students = $students->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found for selected filters.');
            }

            $studentIds = $students->pluck('id')->toArray();

            //------------------------------------------------------------------
            // 2. FEEPLAN ITEMS (TOTAL FEES)
            //------------------------------------------------------------------
            $feeQuery = DB::table('feeplan_item as fpi')
                ->whereIn('fpi.segment_id', function($q) use ($studentIds) {
                    $q->select(DB::raw("CAST(segments AS SIGNED)"))
                    ->from('student')
                    ->whereIn('id', $studentIds);
                });

            if ($academicYear)   $feeQuery->where('fpi.academic_year', $academicYear);
            if ($branchId!='all')$feeQuery->where('fpi.branch_id', $branchId);
            if ($feeType!='all' && $feeType != null) $feeQuery->where('fpi.fee_type', $feeType);
            if ($instalment!='all' && $instalment != null) $feeQuery->where('fpi.instalment', $instalment);

            $feeItems = $feeQuery->get();

            //------------------------------------------------------------------
            // 3. COLLECTION ITEMS (COLLECTED FEES)
            //------------------------------------------------------------------
            $collectionItems = DB::table('feecollection_item as fci')
                ->join('fee_collection as fc','fc.id','=','fci.fee_collection_id')
                ->where('fc.is_cancelled', 0)
                ->whereIn('fci.studentid', $studentIds);

            if ($academicYear)   $collectionItems->where('fci.academic_year', $academicYear);
            if ($feeType!='all' && $feeType != null) $collectionItems->where('fci.feeplan_item_id', function($q) use ($feeType) {
                $q->select('id')->from('feeplan_item')->where('fee_type', $feeType);
            });
            if ($instalment!='all' && $instalment != null) {
                $collectionItems->where('fci.feeplan_item_id', function($q) use ($instalment) {
                    $q->select('id')->from('feeplan_item')->where('instalment', $instalment);
                });
            }

            $collectionItems = $collectionItems->get();

            //------------------------------------------------------------------
            // ===== SUMMARY REPORT (BY FEE TYPE) =====
            //------------------------------------------------------------------
            $summary = [];

            foreach ($feeItems as $item) {
                $ft = $item->fee_type;

                if (!isset($summary[$ft])) {
                    $summary[$ft] = [
                        'fee_type'   => $ft,
                        'total_fee'  => 0,
                        'collected'  => 0,
                        'due'        => 0
                    ];
                }

                // Total fee
                $summary[$ft]['total_fee'] += $item->amount;

                // Collected against fee_item
                $collected = $collectionItems
                    ->where('feeplan_item_id', $item->id)
                    ->sum('payamount');

                $summary[$ft]['collected'] += $collected;
            }

            // Calculate due
            foreach ($summary as $ft => $row) {
                $summary[$ft]['due'] = $row['total_fee'] - $row['collected'];
            }
            return view('finance.duereport', compact(
                'summary',
                'branchselect',
                'academicYearselect'
            ));
        }
        if($req->report_type == 'overall'){
            
            $students = DB::table('student');

            if ($branchId && $branchId != 'all')     $students->where('campus', $branchId);
            if ($course && $course != 'all')         $students->where('course', $course);
            if ($batch && $batch != 'all')           $students->where('batch', $batch);
            if ($section && $section != 'all')       $students->where('section', $section);
            if ($studentId && $studentId != 'all')   $students->where('id', $studentId);

            $students = $students->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found for selected filters.');
            }

            $studentIds = $students->pluck('id')->toArray();

            //------------------------------------------------------------------
            // 2. FEEPLAN ITEMS (TOTAL FEES)
            //------------------------------------------------------------------
            $feeQuery = DB::table('feeplan_item as fpi')
                ->whereIn('fpi.segment_id', function($q) use ($studentIds) {
                    $q->select(DB::raw("CAST(segments AS SIGNED)"))
                    ->from('student')
                    ->whereIn('id', $studentIds);
                });

            if ($academicYear)   $feeQuery->where('fpi.academic_year', $academicYear);
            if ($branchId!='all')$feeQuery->where('fpi.branch_id', $branchId);
            if ($feeType!='all' && $feeType != null) $feeQuery->where('fpi.fee_type', $feeType);
            if ($instalment!='all' && $instalment != null) $feeQuery->where('fpi.instalment', $instalment);

            $feeItems = $feeQuery->get();

            //------------------------------------------------------------------
            // 3. COLLECTION ITEMS (COLLECTED FEES)
            //------------------------------------------------------------------
            $collectionItems = DB::table('feecollection_item as fci')
                ->join('fee_collection as fc','fc.id','=','fci.fee_collection_id')
                ->where('fc.is_cancelled', 0)
                ->whereIn('fci.studentid', $studentIds);

            if ($academicYear)   $collectionItems->where('fci.academic_year', $academicYear);
            if ($feeType!='all' && $feeType != null) $collectionItems->where('fci.feeplan_item_id', function($q) use ($feeType) {
                $q->select('id')->from('feeplan_item')->where('fee_type', $feeType);
            });
            if ($instalment!='all' && $instalment != null) {
                $collectionItems->where('fci.feeplan_item_id', function($q) use ($instalment) {
                    $q->select('id')->from('feeplan_item')->where('instalment', $instalment);
                });
            }

            $collectionItems = $collectionItems->get();

            $detailed = [];

            foreach ($students as $stu) {

                $stuFeeItems = $feeItems->where('segment_id', explode(',', $stu->segments)[0]);

                $totalFee = 0;
                $collected = 0;

                foreach ($stuFeeItems as $item) {
                    $totalFee += $item->amount;

                    $col = $collectionItems
                        ->where('studentid', $stu->id)
                        ->where('feeplan_item_id', $item->id)
                        ->sum('payamount');

                    $collected += $col;

                    // Push detailed rows
                    $detailed[] = [
                        'student_id'    => $stu->id,
                        'student_name'  => $stu->student_name,
                        'fee_type'      => $item->fee_type,
                        'instalment'    => $item->instalment,
                        'amount'        => $item->amount,
                        'collected'     => $col,
                        'due'           => $item->amount - $col,
                        'due_date'      => $item->due_date,
                    ];
                }
            }

                return view('finance.duereport', compact(
                    'detailed',
                    'branchselect',
                    'academicYearselect'
                ));
        }
        return view('finance.duereport', compact('branchselect', 'academicYearselect'));
    }


    // public function collectionreport(Request $request){
    //     $branchselect = Branch::when(auth()->user()->branch, function ($query) { $query->where('id', auth()->user()->branch); })->pluck('name', 'id');

    //     // Base filters
    //     $branchId = $request->branch_id;
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;
    //     $paymentMode = $request->payment_mode ?? 'all';
    //     $whichWise = $request->which_wise;

    //     // ---- Only run FeeType wise report ----
    //     if ($whichWise === 'feetypewise') {

    //        // 1) Get distinct fee types
    //     $feeTypes = DB::table('feeplan_item')
    //         ->distinct()
    //         ->pluck('fee_type')
    //         ->filter() // remove null/empty
    //         ->values();

    //     // 2) Build dynamic select parts, sanitize fee type names
    //     $selectParts = [];
    //     foreach ($feeTypes as $type) {
    //         // sanitize to avoid breaking SQL identifiers and quotes
    //         $clean = str_replace('`', '', (string) $type);
    //         $cleanForSql = str_replace("'", "''", $clean); // escape single quotes for SQL literal
    //         $amountAlias = $clean . '_Amount';
    //         $studentsAlias = $clean . '_Students';

    //         $selectParts[] = "SUM(CASE WHEN fp.fee_type = '{$cleanForSql}' THEN fci.payamount ELSE 0 END) AS `{$amountAlias}`";
    //         $selectParts[] = "COUNT(DISTINCT CASE WHEN fp.fee_type = '{$cleanForSql}' THEN fci.studentid END) AS `{$studentsAlias}`";
    //     }

    //     // 3) Build WHERE clause safely (note: branchId and dates are validated)
    //     $wherebranch = '';
    //     if($branchId !== 'all' && !empty($branchId)){
    //        $wherebranch = "WHERE fc.collected_branch = {$branchId}";
    //     }
    //     $where = $wherebranch . " AND fc.payment_date BETWEEN '{$startDate}' AND '{$endDate}'";

    //     if ($paymentMode !== 'all' && !empty($paymentMode)) {
    //         // sanitize payment mode
    //         $pm = str_replace("'", "''", $paymentMode);
    //         $where .= " AND fc.payment_mode = '{$pm}'";
    //     }

    //     // 4) Build & run final SQL grouped by payment_date
    //     $sql = "
    //         SELECT 
    //             fc.payment_date AS payment_date,
    //             " . implode(", ", $selectParts) . ",
    //             SUM(fci.payamount) AS Total_Amount,
    //             COUNT(DISTINCT fci.studentid) AS Total_Students
    //         FROM fee_collection fc
    //         JOIN feecollection_item fci ON fc.id = fci.fee_collection_id
    //         JOIN feeplan_item fp ON fci.feeplan_item_id = fp.id
    //         {$where}
    //         GROUP BY fc.payment_date
    //         ORDER BY fc.payment_date ASC
    //     ";
    //     // dd($sql);
    //     $report = DB::select($sql);

    //     return view('finance.collectionreport', compact('branchselect',
    //         'report', 'feeTypes', 'branchId', 'startDate', 'endDate', 'paymentMode'
    //     ));
    //     }
    //     return view('finance.collectionreport', compact('branchselect'));
    // }
}

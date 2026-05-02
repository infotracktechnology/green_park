<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FeePlanMaster;
use App\Models\FeesPlanItem;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Options;
use Illuminate\Support\Facades\DB;
use App\Models\BankAccounts;
use App\Models\BillType;
use App\Models\FeeCollection;
use App\Models\FeeCollectionItem;
use App\Http\Controllers\ImportController;
use App\Models\Segment;
use App\Models\Concession;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function feetype(Request $request)
    {
        $feetype = Options::where('type', 'fees')->first();
        $feetype = $feetype->value ?? [];
        if ($request->has('create')) {
            $feetype[] = ['feetype' => $request->feetype];
            Options::updateOrCreate(['type' => 'fees'], ['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type added successfully!');
        }

        if ($request->has('update')) {
            $feetype[$request->index] = ['feetype' => $request->feetype];
            Options::updateOrCreate(['type' => 'fees'], ['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type updated successfully!');
        }

        if ($request->has('delete')) {
            unset($feetype[$request->index]);
            Options::updateOrCreate(['type' => 'fees'], ['value' => $feetype]);
            return redirect()->back()->with('success', 'Fee Type deleted successfully!');
        }

        return view('finance.feetype', compact('feetype'));
    }
    public function index(Request $request)
    {
        // $fees_plan = FeesPlanItem::when(auth()->user()->branch, function ($query) {
        //     $query->where('branch_id', auth()->user()->branch);
        // })->where('academic_year', $this->academic_year)->get();

        $fees_plan = FeePlanMaster::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        })->where('academic_year', $this->academic_year)->get();
        return view('finance.index', compact('fees_plan'));
    }

    public function create(Request $request)
    {
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $courseselect = Student::select('campus', 'course')->distinct()->get();
        $batchselect = Student::select('campus', 'course', 'batch')->distinct()->get();
        $bill_types = DB::table('bill_type')->pluck('name', 'id');
        $coaching_type = Student::select('coaching_type')->distinct()->get();
        $feetype = Options::where('type', 'fees')->first();
        $feetype = $feetype->value ?? [];
        $bank_accounts = BankAccounts::all();
        $segments = Segment::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        })->where('is_active', 1)->get();
        return view('finance.create', compact('coaching_type', 'feetype', 'branchselect', 'bill_types', 'courseselect', 'batchselect', 'bank_accounts', 'segments'));
    }

    public function store(Request $request)
    {
        // foreach ($request->batch as $batch) {
        //     foreach ($request->item as $item) {
        //         $exists = FeesPlanItem::where('name', $request->name)
        //             ->where('academic_year', $this->academic_year)
        //             ->where('coaching_type', $request->coaching_type)
        //             ->where('branch_id', $request->branch_id)
        //             ->where('course', $request->course)
        //             ->where('batch', $batch)
        //             ->where('fee_type', $request->fee_type)
        //             ->where('bill_type_id', $request->bill_type_id)
        //             ->where('segment_id', $request->segment_id)
        //             ->where('instalment', $item['instalment'])
        //             ->exists();

        //         if ($exists) {
        //             // return back()
        //             //     ->withErrors([
        //             //         'name' => "The fee item '{$request->name}' already exists for batch '{$batch}'."
        //             //     ])
        //             //     ->withInput();

        //             return redirect()->route('feesplan.index')->with('error', 'Fee plan already exists');
        //         }
        //     }
        // }
        // foreach ($request->batch as $batch) {
        //     foreach ($request->item as $item) {
        //         FeesPlanItem::create([
        //             'academic_year' => $this->academic_year,
        //             'branch_id' => $request->branch_id,
        //             'course' => $request->course,
        //             'batch' => $batch,
        //             'bill_type_id' => $request->bill_type_id,
        //             'segment_id' => $request->segment_id,
        //             'is_hostel' => $request->is_hostel,
        //             'name' => $request->name,
        //             'coaching_type' => $request->coaching_type,
        //             'fee_type' => $request->fee_type,
        //             'instalment' => $item['instalment'],
        //             'amount' => $item['amount'],
        //             'invoice_date' => $item['invoice_date'],
        //             'due_date' => $item['due_date'],
                    
        //         ]);
        //     }
        // }
        // dd($request->all());
        try{

        DB::transaction(function () use ($request) {
            $batch = $request->batch;
            sort($batch);
            $feeplan_master = FeePlanMaster::create([
                'branch_id' => $request->branch_id,
                'coaching_type' => $request->coaching_type,
                'course' => $request->course,
                'batch' => implode(',', $batch),
                'bill_type_id' => $request->bill_type_id,
                'segment_id' => $request->segment_id,
                'is_hostel' => $request->is_hostel,
                'name' => $request->name,
                // 'fee_type' => $request->fee_type,
                'academic_year' => $this->academic_year,
                'is_active' => $request->is_active,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ]);

            foreach ($request->item as $item) {
                FeesPlanItem::create([
                    'feeplan_master_id' => $feeplan_master->id,
                    'academic_year' => $this->academic_year,
                    'branch_id' => $request->branch_id,
                    'course' => $request->course,
                    'batch' => implode(',', $batch),
                    'bill_type_id' => $request->bill_type_id,
                    'segment_id' => $request->segment_id,
                    'is_hostel' => $request->is_hostel,
                    'name' => $request->name,
                    'coaching_type' => $request->coaching_type,
                    'fee_type' => $item['fee_type'],
                    'instalment' => $item['instalment'],
                    'amount' => $item['amount'],
                    'invoice_date' => $item['invoice_date'],
                    'due_date' => $item['due_date'],
                ]);
            }

        });

        return redirect()->route('feesplan.index')->with('success', 'Fees Plan created successfully!');
    }
    catch(\Exception $e){

        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
    }

    public function edit(Request $request, $id)
    {

        $feesplan = FeePlanMaster::findOrFail($id);
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $courseselect = Student::select('campus', 'course')->distinct()->get();
        $batchselect = Student::select('campus', 'course', 'batch')->distinct()->get();
        $bill_types = DB::table('bill_type')->pluck('name', 'id');
        $coaching_type = Student::select('coaching_type')->distinct()->get();
        $feetype = Options::where('type', 'fees')->first();
        $feetype = $feetype->value ?? [];
        $bank_accounts = BankAccounts::all();
        // $segments = Segment::when(auth()->user()->branch, function ($query) {
        //     $query->where('branch_id', auth()->user()->branch);
        // })->get();
        $segments = Segment::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        })->where('is_active', 1)->get();
        return view('finance.edit', compact('feesplan', 'coaching_type', 'feetype', 'branchselect', 'bill_types', 'courseselect', 'batchselect', 'bank_accounts', 'segments'));
    }

    public function update(Request $request, $id)
    {
        try{
        $fees_plan = FeePlanMaster::find($id);

        DB::transaction(function () use ($request, $fees_plan) {
            $fees_plan->update([
            'branch_id' => $request->branch_id,
            'coaching_type' => $request->coaching_type,
            'course' => $request->course,
            'batch' => implode(',', $request->batch),
            'bill_type_id' => $request->bill_type_id,
            'segment_id' => $request->segment_id,
            'is_hostel' => $request->is_hostel,
            'name' => $request->name,
            // 'fee_type' => $request->fee_type,
            'academic_year' => $this->academic_year,
            'is_active' => $request->is_active,
            // 'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id
        ]);

        foreach ($request->item as $item) {
            
            $search = [];

            // If existing item → match by ID
            if (!empty($item['id'])) {
                $search['id'] = $item['id'];
            } else {
                // For new item → match by unique combination
                $batch = $request->batch;
                sort($batch);
                $search = [
                    'feeplan_master_id' => $fees_plan->id,
                    'instalment'        => $item['instalment'],
                    'branch_id' => $request->branch_id,
                    'coaching_type' => $request->coaching_type,
                    'course' => $request->course,
                    'batch' => implode(',', $batch),
                    'is_hostel' => $request->is_hostel,
                    'academic_year' => $this->academic_year,
                ];
            }
            FeesPlanItem::updateOrCreate(
                $search,
                [
                'feeplan_master_id' => $fees_plan->id,
                'academic_year' => $this->academic_year,
                'branch_id' => $request->branch_id,
                'course' => $request->course,
                'batch' => implode(',', $request->batch),
                'bill_type_id' => $request->bill_type_id,
                'segment_id' => $request->segment_id,
                'is_hostel' => $request->is_hostel,
                'name' => $request->name,
                'coaching_type' => $request->coaching_type,
                'fee_type' => $item['fee_type'],
                'instalment' => $item['instalment'],
                'amount' => $item['amount'],
                'invoice_date' => $item['invoice_date'],
                'due_date' => $item['due_date'],
            ]);
        }
        });
        return redirect()->route('feesplan.index')->with('success', 'Fees Plan updated successfully!');
    } catch(\Exception $e){

        return redirect()->route('feesplan.index')->with('error', 'Something went wrong. Please try again.');
    }
    }

    public function destroy(Request $request, $id)
    {
        $fees_plan = FeesPlanItem::findorFail($id);
        $exists = FeeCollectionItem::where('feeplan_item_id', $fees_plan->id)->exists();

        if ($exists) {
            return redirect()->route('feesplan.index')->with('error', 'Fee plan is in use and cannot be deleted.');
        }
        $fees_plan->delete();
        return redirect()->route('feesplan.index')->with('success', 'Fees Plan deleted successfully!');
    }

    public function collection(Request $request)
    {
        $branches = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $coachingTypes = Student::select('coaching_type')->distinct()->get();
        $students = Student::when(auth()->user()->branch, function ($query) {
            $query->where('campus', auth()->user()->branch);
        })->select('id', 'coaching_type', 'campus', 'course', 'batch', 'student_id', 'student_name', 'user_name', 'father_name', 'mother_name', 'ph_no1')->get();
        $concessions = Concession::where('is_active', 1)->get();

        $student = null;
        $receipthistory = null;

        if ($request->isMethod('get')) {
            if ($request->filled('student_id')) {
                $student = Student::when(auth()->user()->branch, function ($query) {
                    $query->where('campus', auth()->user()->branch);
                })->find($request->input('student_id'));
                $receipthistory = FeeCollection::where('student_id', $student->student_id)->get();
            }
            return view('finance.collection', compact('branches', 'coachingTypes', 'students', 'student', 'receipthistory', 'concessions'));
        }

        if ($request->isMethod('post')) {
            $financial_year = $this->financial_year();
            $academic_year = $this->academic_year;
            $student = Student::when(auth()->user()->branch, function ($query) {
                $query->where('campus', auth()->user()->branch);
            })->find($request->input('studentID'));
            if (!$student) {
                return redirect()->back()->with('error', 'Student not found.');
            }
            $fee_collection = DB::transaction(function () use ($request, $student, $financial_year, $academic_year) {
                $fees_collection  = FeeCollection::create([
                    'academic_year' => $academic_year,
                    'financial_year' => $financial_year,
                    'receipt_no' => 'receipt' . str_pad(FeeCollection::count() + 1, 7, '0', STR_PAD_LEFT),
                    'student_id' => $student->id,
                    'collected_branch' => auth()->user()->branch ?? $student->campus,
                    'payment_date' => $request->payment_date ?? date('Y-m-d'),
                    'payment_mode' => $request->payment_mode,
                    'bank_transfer_mode' => $request->bank_transfer_mode,
                    'bank_name' => $request->bank_name,
                    'bank_transfer_date' => $request->bank_transfer_date,
                    'transaction_id' => $request->transaction_id,
                    'total' => $request->total,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id
                ]);

                foreach ($request->fees as $fee) {

                    // Skip if payamount is null, empty, or zero
                    if (empty($fee['payamount'])) {
                        continue;
                    }

                    FeeCollectionItem::create([
                        'academic_year' => $academic_year,
                        'financial_year' => $financial_year,
                        'fee_collection_id' => $fees_collection->id,   // your FK to fee collection
                        'feeplan_item_id'  => $fee['feeplan_item_id'],
                        'payamount'        => $fee['payamount'],
                        'studentid'        => $student->id,    // the internal student PK
                        'concession_id'    => $fee['concession_id'],
                        'concession_amount' => $fee['concession_amount'] ?? 0,
                    ]);
                }

                return $fees_collection;
               
            });

            // return redirect()->route('fees.receipt', $fee_collection->id)->with('success', 'Fee Collection created successfully and receipt is ready to print!');

            return redirect()->route('fees.collection', ['student_id' => $student->id])->with([
        'success' => 'Fee Collection created successfully!',
        'last_receipt_id' => $fee_collection->id,
    ]);
        }
    }

    public function receipt($id)
    {
        $fee_collection = FeeCollection::with(['student', 'item.feeplanitem'])->findOrFail($id);
        
        $copyType = request()->has('copy') ? 'DUPLICATE' : 'ORIGINAL';

        return view('finance.receipt', compact('fee_collection', 'copyType'));
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        $receipt = FeeCollection::findOrFail($id);

        // If already cancelled
        if ($receipt->is_cancelled) {
            return back()->with('error', 'This receipt is already cancelled.');
        }

        // Mark as cancelled
        $receipt->update([
            'is_cancelled' => true,
            'cancel_reason' => $request->cancel_reason,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return redirect()->route('feesplan.index')
            ->with('success', 'Receipt cancelled successfully!');
    }




    public function bankcreate(Request $request)
    {
        $bank_accounts = BankAccounts::all();
        $bill_types = BillType::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        })->get();
        $segments = Segment::when(auth()->user()->branch, function ($query) {
            $query->where('branch_id', auth()->user()->branch);
        })->get();
        $concessions = Concession::all();
        $typeselect = ['percentage', 'fixed'];
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $courseselect = Student::select('campus', 'course')->distinct()->get();
        $batchselect = Student::select('campus', 'course', 'batch')->distinct()->get();
        return view('finance.bankcreate', compact('bank_accounts', 'bill_types', 'branchselect', 'courseselect', 'batchselect', 'segments', 'concessions', 'typeselect'));
    }

    public function bankstore(Request $request)
    {
        try {
            $data = $request->all();
            $data['academic_year'] = $this->academic_year;
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;

            $bankaccount = BankAccounts::create($data);
            return redirect()->route('bank.create')->with('success', 'Bank Account created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function bankedit(Request $request, $id)
    {
        $bank_accounts = BankAccounts::find($id);
        $bill_types = BillType::all();
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $courseselect = Student::select('campus', 'course')->distinct()->get();
        $batchselect = Student::select('campus', 'course', 'batch')->distinct()->get();
        return view('finance.bankedit', compact('bank_accounts', 'bill_types', 'branchselect', 'courseselect', 'batchselect'));
    }

    public function bankupdate(Request $request, $id)
    {
        try {
            $bank_accounts = BankAccounts::find($id);
            $data = $request->all();
            $data['updated_by'] = auth()->user()->id;
            $bank_accounts->update($data);
            return redirect()->route('bank.create')->with('success', 'Bank Account updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function billTypestore(Request $request)
    {
        try {
            $request->validate(['name' => 'required|unique:bill_type,name']);
            $data = $request->all();
            $data['academic_year'] = $this->academic_year;
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;

            $billtype = BillType::create($data);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Bill Type created successfully!', 'data' => $billtype]);
            }
            return redirect()->route('bank.create')->with('success', 'Bill Type created successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function billTypeedit(Request $request, $id){
        $bill_type = BillType::find($id);
        $branchselect = Branch::when(auth()->user()->branch, function ($query) {
            $query->where('id', auth()->user()->branch);
        })->pluck('name', 'id');
        $courseselect = Student::select('campus', 'course')->distinct()->get();
        $batchselect = Student::select('campus', 'course', 'batch')->distinct()->get();
        $bank_accounts = BankAccounts::all();
        return view('finance.billtypeedit', compact('bill_type', 'branchselect', 'courseselect', 'batchselect', 'bank_accounts'));
    }

    public function billTypeupdate(Request $request, $id)
    {
        try {
            $billtype = BillType::find($id);
            $data = $request->all();
            $data['updated_by'] = auth()->user()->id;
            $billtype->update($data);
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Bill Type updated successfully!', 'data' => $billtype]);
            }
            return redirect()->route('bank.create')->with('success', 'Bill Type Updated successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }




    public function FeesMigration(Request $request,ImportController $import)
    {
       if($request->isMethod('post')){
        $rows = $import->parseCSV($request->file('feemigration')->getRealPath());

        if(!isset($rows[0]['instalment']) || !isset($rows[0]['student_id']))  
        return redirect()->back()->with('error', 'Please upload a valid file.');

        $feeItems = FeesPlanItem::where(['academic_year' => $this->academic_year,'branch_id' => $request->branch,'course' => $request->course,'batch' => $request->batch,'coaching_type' => $request->coaching_type])->pluck('id', 'instalment')->toArray();

        $rows = array_map(function ($row) use ($request) {
            $row['academic_year'] = $this->academic_year;
            $row['financial_year'] = $this->financial_year();
            $row['collected_branch'] = $request->branch;
            $row['feeitem_id'] = $feeItems[$row['instalment']] ?? 0;
            unset($row['instalment']);
            return $row;
        }, $rows);
        FeeCollection::insert($rows);
        return redirect()->back()->with('success', 'Fees Migration completed successfully!');
       }
       
        return view('finance.migration');
    }
}

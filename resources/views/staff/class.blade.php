@extends('layouts.app')
@section('title', 'Class Assign')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}"> 
@endsection

@section('main')
@php use Illuminate\Support\Str; @endphp
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row justify-content-center">
                <div class="col-12">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible show fade"> {{ session('success') }} </div>
                @endif
                    <!-- Staff Selection -->
                    <div class="card mt-4">
                        <div class="card-header"><h4>Staff Assign</h4></div>
                        <div class="card-body">
                            <form method="POST" action="{{  route('staff.subjectAssign') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-lg-3">
                                        <label for="name">Staff Name</label>
                                        <select name="name" id="staff_name" class="select2 form-control form-control-sm" required>
                                            <option value="">Choose Staff Name</option>
                                            @foreach ($staffDetails as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @foreach ($staffDetails as $staff)
                                    <div class="staff-data mt-4" id="staff-{{ $staff->id }}" style="display: none;">
                                        <h5>{{ strtoupper($staff->name) }}</h5>
                                        <div class="row">
                                            @foreach (['Mobile' => $staff->mob_no, 'Date of Birth' => \Carbon\Carbon::parse($staff->dob)->format('d M Y'), 'Joining Date' => \Carbon\Carbon::parse($staff->date_of_joining)->format('d M Y'), 'Email' => $staff->email, 'Qualification' => $staff->qualifications, 'Designation' => $staff->designation] as $label => $value)
                                                <div class="col-md-4"><p><strong>{{ $label }}:</strong> {{ $value }}</p></div>
                                            @endforeach
                                        </div>
                                        <hr>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>

                    <!-- Assignment Tabs -->
                   <!-- Assignment Tabs -->
<div class="card" id="assignCard" style="display: none;">
    <div class="card-header"><h4>Assignment Tabs</h4></div>
    <div class="card-body">
        <ul class="nav nav-pills mb-3" id="assignTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="class-tab" data-toggle="tab" href="#class" role="tab">Class Assign</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="subject-tab" data-toggle="tab" href="#subject" role="tab">Subject Assign</a>
            </li>
        </ul>

        <div class="tab-content" id="assignTabContent">

            <!-- CLASS ASSIGN TAB -->
            <div class="tab-pane fade show active" id="class" role="tabpanel">
                <form method="POST" action="{{ route('staff.class') }}">
                    @csrf
                    <input type="hidden" name="name" class="id">

                    <div class="form-row">
                        <!-- Branch -->
                        <div class="form-group col-lg-3">
                            <label for="branch_class">Branch</label>
                            <select name="branch" id="branch_class" class="select2 form-control form-control-sm" required>
                                <option value="">Choose Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Coaching Type -->
                        <div class="form-group col-lg-4">
                            <label>Coaching Type</label>
                            <select name="coaching_type" id="coaching_type_class" class="select2 form-control form-control-sm" required>
                                <option value="">Choose Coaching Type</option>
                                @foreach (['Offline', 'Online Recorded', 'Online Live', 'Test Series', '11', '12'] as $ctype)
                                    <option value="{{ $ctype }}">{{ $ctype }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sections -->
                        <div class="form-group col-12">
                            <label>Sections</label>
                            <div class="row" id="class-sections-wrapper">
                                @foreach ($sections as $section)
                                    @php 
                                        $slugType = Str::slug($section->coaching_type);
                                        $checkboxId = "class-section-" . Str::slug($section->campus . '-' . $slugType . '-' . $section->section);
                                    @endphp
                                    <div class="col-md-3 form-check class-section-{{ $section->campus }}-{{ $slugType }}" style="display: none;">
                                        <input class="form-check-input" type="checkbox" name="section[]" id="{{ $checkboxId }}" value="{{ $section->section }}">
                                        <label class="form-check-label" for="{{ $checkboxId }}">{{ $section->section }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Assign</button>
                </form>
            </div>

            <!-- SUBJECT ASSIGN TAB -->
            <div class="tab-pane fade" id="subject" role="tabpanel">
                <form method="POST" action="{{ route('staff.subjectAssign') }}">
                    @csrf
                    <input type="hidden" name="name" class="id">

                    <div class="form-row">
                        <!-- Branch -->
                        <div class="form-group col-lg-3">
                            <label for="branch_subject">Branch</label>
                            <select name="branch" id="branch_subject" class="select2 form-control form-control-sm" required>
                                <option value="">Choose Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Coaching Type -->
                        <div class="form-group col-lg-4">
                            <label>Coaching Type</label>
                            <select name="coaching_type" id="coaching_type_subject" class="select2 form-control form-control-sm" required>
                                <option value="">Choose Coaching Type</option>
                                @foreach (['Offline', 'Online Recorded', 'Online Live', 'Test Series', '11', '12'] as $ctype)
                                    <option value="{{ $ctype }}">{{ $ctype }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject -->
                        <div class="form-group col-lg-3">
                            <label for="subject">Subject</label>
                            <select name="subject" class="select2 form-control form-control-sm" required>
                                <option value="">Choose Subject</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sections -->
                        <div class="form-group col-12">
                            <label>Sections</label>
                            <div class="row" id="subject-sections-wrapper">
                                @foreach ($sections as $section)
                                    @php 
                                        $slugType = Str::slug($section->coaching_type);
                                        $checkboxId = "subject-section-" . Str::slug($section->campus . '-' . $slugType . '-' . $section->section);
                                    @endphp
                                    <div class="col-md-3 form-check subject-section-{{ $section->campus }}-{{ $slugType }}" style="display: none;">
                                        <input class="form-check-input" type="checkbox" name="section[]" id="{{ $checkboxId }}" value="{{ $section->section }}">
                                        <label class="form-check-label" for="{{ $checkboxId }}">{{ $section->section }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Assign</button>
                </form>
            </div>

        </div>
    </div>
</div>

        </div>
    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
        const staffAssignData = @json($staffDetails->keyBy('id')->map->only(['class_assign', 'sub_assign']));

        const slugify = text => text.toLowerCase().replace(/ /g, '-');

        function updateSections(type) {
            const wrapper = `#${type}-sections-wrapper`;
            const branch = $(`#branch_${type}`).val();
            const coachingType = $(`#coaching_type_${type}`).val();
            const staffId = $('#staff_name').val();
            const slug = slugify(coachingType);

            $(`${wrapper} .form-check`).hide();
            $(`${wrapper} input[type="checkbox"]`).prop('checked', false);

            if (!branch || !coachingType) return;
            $(`${wrapper} .${type}-section-${branch}-${slug}`).show();

            if (type === 'class' && staffAssignData[staffId]?.class_assign?.branch_id == branch) {
                const assigned = staffAssignData[staffId].class_assign;
                if (assigned.coaching_types === coachingType) {
                    (assigned.sections || []).forEach(section => {
                        $(`${wrapper} input[value="${section}"]`).prop('checked', true);
                    });
                }
            }
        }

        $('#branch_class, #coaching_type_class').on('change', () => updateSections('class'));
        $('#branch_subject, #coaching_type_subject').on('change', () => updateSections('subject'));

        $('#staff_name').on('change', function () {
            const staffId = $(this).val();
            $('#assignCard').show();
            $('.staff-data').hide();
            $('#staff-' + staffId).show();
            $(".id").val(staffId);
        });

</script>
@endsection

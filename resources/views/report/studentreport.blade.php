@extends('layouts.app')
@section('title', "PTA Student Report")

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/select2/dist/css/select2.min.css') }}">
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        
                        <div class="card-header">
                            <h4>Student Report</h4>
                        </div>

                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('report.studentreport') }}" id="myForm">
                                @csrf

                                <div class="row">
                                    <div class="form-group col-lg-2 col-md-6">
                                        <label for="report_branch">Branch <span class="text-danger">*</span></label>
                                        <select name="branch" id="report_branch" class="form-control select2" required>
                                            <option value="">Select Branch</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-6">
                                        <label for="report_course">Course <span class="text-danger">*</span></label>
                                        <select name="course" id="report_course" class="form-control select2" required>
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-6">
                                        <label for="report_section">Section <span class="text-danger">*</span></label>
                                        <select name="section" id="report_section" class="form-control select2" required>
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-6">
                                        <label for="report_coaching_type">Coaching Type <span class="text-danger">*</span></label>
                                        <select name="coaching_type" id="report_coaching_type" class="form-control select2" required>
                                            <option value="">Select Coaching Type</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-6">
                                        <label for="report_students">Student <span class="text-danger">*</span></label>
                                        <select name="student_id" id="report_students" class="form-control select2" required>
                                            <option value="">Select Student</option>

                                        </select>
                                    </div>

                                    <div class="form-group col-lg-2 col-md-6 mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-file-pdf mr-1"></i> Download PDF
                                        </button>
                                    </div>
                                </div>
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
<script>
$(document).ready(function () {

    $('.select2').select2({ width: '100%' });

    const reportUrl = "{{ route('report.studentreport') }}";

    function setSelectOptions(selector, optionsHtml) {
        const el = document.querySelector(selector);
        if (el) {
            el.innerHTML = optionsHtml;
            $(el).val('');
        }
    }

    function loadDropdownData(requestData, targetSelector, placeholder, isStudent = false, errorMsg = 'Unable to load data.') {
    $.ajax({ url: reportUrl, type: 'GET', data: requestData,

        success: function (response) {
            let options = `<option value="">${placeholder}</option>`;
            if (isStudent) {
                options += `<option value="all">All Students</option>`;
            }
            $.each(response, function (index, item) {
                if (isStudent) {
                    options += `<option value="${item.student_id}"> ${item.student_id} - ${item.student_name} </option>`;
                } else {
                    options += `<option value="${item}"> ${item} </option>`;
                }
            });
            setSelectOptions(targetSelector, options);
        }
    });
}

    $('#report_branch').on('change', function () {
        const branch = $(this).val();

        setSelectOptions('#report_course', '<option value="">Select Course</option>');
        setSelectOptions('#report_section', '<option value="">Select Section</option>');
        setSelectOptions('#report_coaching_type', '<option value="">Select Coaching Type</option>');
        setSelectOptions('#report_students', '<option value="">Select Student</option>');

        if (branch) {
            loadDropdownData({ branch }, '#report_course', 'Select Course', false, 'Unable to load courses.');
        }
    });

    $('#report_course').on('change', function () {
        const branch = $('#report_branch').val();
        const course = $(this).val();

        setSelectOptions('#report_section', '<option value="">Select Section</option>');
        setSelectOptions('#report_coaching_type', '<option value="">Select Coaching Type</option>');
        setSelectOptions('#report_students', '<option value="">Select Student</option>');

        if (branch && course) {
            loadDropdownData({ branch, course }, '#report_section', 'Select Section', false, 'Unable to load sections.');
        }
    });

    $('#report_section').on('change', function () {
        const branch = $('#report_branch').val();
        const course = $('#report_course').val();
        const section = $(this).val();

        setSelectOptions('#report_coaching_type', '<option value="">Select Coaching Type</option>');
        setSelectOptions('#report_students', '<option value="">Select Student</option>');

        if (branch && course && section) {
            loadDropdownData({ branch, course, section }, '#report_coaching_type', 'Select Coaching Type', false, 'Unable to load coaching types.');
        }
    });

    $('#report_coaching_type').on('change', function () {
        const branch = $('#report_branch').val();
        const course = $('#report_course').val();
        const section = $('#report_section').val();
        const coachingType = $(this).val();

        setSelectOptions('#report_students', '<option value="">Select Student</option>');

        if (branch && course && section && coachingType) {
            loadDropdownData(
                { branch, course, section, coaching_type: coachingType }, 
                '#report_students', 
                'Select Student', 
                true, 
                'Unable to load students.'
            );
        }
    });

});
document.getElementById('myForm').addEventListener('submit', function () {
        setTimeout(function () {
            window.location.reload();
        }, 4000);
    });

</script>
@endsection
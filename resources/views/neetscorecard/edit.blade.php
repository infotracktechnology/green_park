@extends('layouts.app')

@section('title', 'Edit NEET Scorecard')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Edit NEET Details</h4>
                </div>

                <form action="{{ route('neetscorecard.update', $student->student_id) }}" method="POST">
                    @csrf

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>NEET Application Number <span class="text-danger">*</span></label>
                                    <input type="text" name="neetappno" class="form-control" value="{{ old('neetappno', $student->neetappno) }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>NEET Roll Number <span class="text-danger">*</span></label>
                                    <input type="text" name="neetrollno" class="form-control" value="{{ old('neetrollno', $student->neetrollno) }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>AIQ NEET Category <span class="text-danger">*</span></label>
                                    <select name="neetcomm" class="form-control">
                                        <option value="">Select Community</option>
                                        <option value="General" {{ old('neetcomm', $student->neetcomm) == 'General' ? 'selected' : '' }}>General</option>
                                        <option value="GEN-EWS" {{ old('neetcomm', $student->neetcomm) == 'GEN-EWS' ? 'selected' : '' }}>GEN-EWS</option>
                                        <option value="OBC-NCL" {{ old('neetcomm', $student->neetcomm) == 'OBC-NCL' ? 'selected' : '' }}>OBC-NCL</option>
                                        <option value="SC" {{ old('neetcomm', $student->neetcomm) == 'SC' ? 'selected' : '' }}>SC</option>
                                        <option value="ST" {{ old('neetcomm', $student->neetcomm) == 'ST' ? 'selected' : '' }}>ST</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Special Category <span class="text-danger">*</span></label>
                                    <select type="text" name="neetspecialcategory" class="form-control">
                                        <option value="">Select Category</option>
                                        <option value="NIL" {{ old('neetspecialcategory', $student->neetspecialcategory) == 'NIL' ? 'selected' : '' }}>NIL</option>
                                        <option value="7.5% " {{ old('neetspecialcategory', $student->neetspecialcategory) == '7.5%' ? 'selected' : '' }}>7.5% </option>
                                        <option value="PWD" {{ old('neetspecialcategory', $student->neetspecialcategory) == 'PWD' ? 'selected' : '' }}>PWD</option>
                                        <option value="Ex-Serviceman" {{ old('neetspecialcategory', $student->neetspecialcategory) == 'Ex-Serviceman' ? 'selected' : '' }}>Ex-Serviceman</option>
                                        <option value="Sports" {{ old('neetspecialcategory', $student->neetspecialcategory) == 'Sports' ? 'selected' : '' }}>Sports</option>
                                        <option value="IRT" {{ old('neetspecialcategory', $student->neetspecialcategory) == 'IRT' ? 'selected' : '' }}>IRT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>NEET Mark <span class="text-danger">*</span></label>
                                    <input type="number" name="neetmark" class="form-control"  max="720" value="{{ old('neetmark', $student->neetmark) }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>All-India Rank <span class="text-danger">*</span></label>
                                    <input type="number" name="neetrank" class="form-control" value="{{ old('neetrank', $student->neetrank) }}">
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('neetscorecard.index') }}" class="btn btn-secondary"> Cancel </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </section>
</div>
@endsection
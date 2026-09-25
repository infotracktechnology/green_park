@extends('layouts.dashboard')
@section('title', 'Consolidated Report')

@section('css')
<style>
    .pdf-container {
        width: 100%;
        height: calc(100vh - 180px);
        min-height: 700px;
    }

    .pdf-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Consolidated Report</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="pdf-container">
                                <iframe src="{{ route('student.reportstream') }}"> </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

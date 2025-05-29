@extends('layouts.dashboard')

@section('title', 'Question Key')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
    .answer-key-card {
        border-radius: 12px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }
    .answer-key-card:hover {
        transform: translateY(-5px);
    }
    .card-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        font-size: 20px;
        font-weight: bold;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
  
    .download-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  
    color: white;
    font-weight: 600;
    font-size: 15px;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    transition: all 0.3s ease-in-out;
    text-decoration: none;
    box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.15);
    cursor: pointer;
}


@keyframes bounce {
    from { transform: translateY(0); }
    to { transform: translateY(-3px); }
}
    .icon-large {
        font-size: 32px;
        animation: float 1.5s infinite alternate ease-in-out;
    }
    @keyframes float {
        from { transform: translateY(0px); }
        to { transform: translateY(-5px); }
    }
    .table thead {
        background-color: #f1f3f5;
        font-weight: bold;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
        transition: 0.2s ease-in-out;
    }
    
    
    .file-icon {
        font-size: 22px;
    }
</style>
@endsection

@section('main')
<div class="main-content">
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-lg-12">
              
                <div class="card answer-key-card">
                    <div class="card-header">
                        <i class="fas fa-file-alt icon-large"></i> Question Key
                    </div>
                   
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                         
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questionKeys as $questionKey)
                                    <tr>
                                        <td>{{ $questionKey->title }}</td>
                                        <td>
                                            <a href="{{ env('APP_URL').$questionKey->file_path }}" 
                                               class="download-btn btn success" download>
                                                <i class="fas fa-file-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                  
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

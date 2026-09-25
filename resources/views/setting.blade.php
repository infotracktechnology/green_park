@extends('layouts.app')
@section('title', 'Admin Settings')

@section('css')
<link rel="stylesheet" href="{{asset('bundles/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="row">
        <div class="col-md-12 col-sm-12">

          @if(session()->has('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
          </div>
          @endif

          <div class="card card-primary">
            <div class="card-header">
              <h4>Admin Settings</h4>
            </div>
            <div class="card-body">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="admission-tab" data-toggle="tab" href="#admission" role="tab" aria-controls="admission" aria-selected="true">Admission</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="document-tab" data-toggle="tab" href="#document" role="tab" aria-controls="document" aria-selected="false">Document List</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contact Us</a>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="admission" role="tabpanel" aria-labelledby="admission-tab">
                  <div class="row mt-3">
                    <div class="col-lg-12">
                      <div class="table-responsive">
                        <table class="table table-striped" style="width:100%;">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Attribute</th>
                              <th>Value</th>
                              <th>Created Time</th>
                              <th>Set Value</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($setting as $key => $row)
                            <tr>
                              <td>{{ $key+1 }}</td>
                              <td>{{ $row->key }}</td>
                              <td>{{ $row->value }}</td>
                              <td>{{ $row->created_at->diffForHumans() }}</td>
                              <td>
                                <button class="btn btn-primary set-value" data-toggle="modal" data-target="#SetValue" data-row="{{ json_encode($row) }}">Set Value</button>
                              </td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                  <div class="row mt-3">
                    <div class="col-lg-6">
                      <form method="post" action="{{ route('option.document') }}">
                        @csrf
                        <div class="form-group">
                          <label>Document List</label>
                          <div class="input-group">
                            <input type="text" name="document_name" class="form-control" placeholder="Enter document name" required>
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="submit">Add Document</button>
                            </div>
                          </div>
                        </div>
                      </form>
                      <div class="table-responsive mt-4">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Document Name</th>
                              <th class="text-right">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($documents as $key => $doc)
                            <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>{{ $doc }}</td>
                              <td class="text-right">
                                <form method="post" action="{{ route('option.document') }}" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <input type="hidden" name="document_name" value="{{ $doc }}">
                                  <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </form>
                              </td>
                            </tr>
                            @empty
                            <tr>
                              <td colspan="3" class="text-center">No document options found.</td>
                            </tr>
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- <-- contact us --> --}}
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                  <div class="row mt-3">
                      <div class="col-lg-12">

                          @forelse($contacts as $sectionIndex => $contact)
                              <div class="card mb-4">
                                  <div class="card-header d-flex justify-content-between align-items-center">
                                      <div>
                                          <h5 class="mb-1">{{ $contact['title'] ?? '' }}</h5>
                                          <small class="text-muted">{{ $contact['subtitle'] ?? '' }}</small>
                                      </div>
                                      <form method="POST" action="{{ route('admin.setting') }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="contact_action" value="delete_section">
                                        <input type="hidden"name="section_index"value="{{ $sectionIndex }}">
                                        <button type="submit"class="btn btn-sm btn-danger"onclick="return confirm('Are you sure you want to delete this contact section?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                  </div>

                                  <div class="card-body">
                                      <div class="table-responsive">
                                          <table class="table table-striped">
                                              <thead>
                                                  <tr>
                                                      <th style="width: 80px;">#</th>
                                                      <th>Phone Number</th>
                                                      <th class="text-right">Action</th>
                                                  </tr>
                                              </thead>
                                              <tbody>
                                                  @forelse($contact['contacts'] ?? [] as $numberIndex => $phone)
                                                      <tr>
                                                          <td>{{ $numberIndex + 1 }}</td>
                                                          <td>{{ $phone['number'] ?? '' }}</td>
                                                          <td class="text-right">
                                                              <button type="button" class="btn btn-sm btn-primary edit-number" data-toggle="modal" data-target="#editNumberModal" data-section="{{ $sectionIndex }}" data-number="{{ $numberIndex }}" data-value="{{ $phone['number'] ?? '' }}"><i class="fas fa-edit"></i>
                                                              </button>

                                                              <form method="POST" action="{{ route('admin.setting') }}" class="d-inline">
                                                                  @csrf
                                                                  <input type="hidden" name="contact_action" value="delete_number">
                                                                  <input type="hidden" name="section_index" value="{{ $sectionIndex }}">
                                                                  <input type="hidden" name="number_index" value="{{ $numberIndex }}">
                                                                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this number?')"> <i class="fas fa-trash"></i> </button>
                                                              </form>
                                                          </td>
                                                      </tr>
                                                  @empty
                                                      <tr>
                                                          <td colspan="3" class="text-center">No contact numbers found.</td>
                                                      </tr>
                                                  @endforelse
                                              </tbody>
                                          </table>
                                      </div>

                                      <button type="button" class="btn btn-success btn-sm mt-2" data-toggle="modal" data-target="#addNumberModal "data-section="{{ $sectionIndex }}"><i class="fas fa-plus"></i> Add Number </button>
                                  </div>
                              </div>
                          @empty
                              <div class="alert alert-info">
                                  No contact details found.
                              </div>
                          @endforelse
                          <div class="mt-3">
                              <button type="button"class="btn btn-primary"data-toggle="modal"data-target="#addContactSectionModal"><i class="fas fa-plus"></i> Add Contact Section
                              </button>
                          </div>

                      </div>
                  </div>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>




<div id="SetValue" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Set Value Form</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="post" action="{{ route('admin.setting') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label>New Value</label>
            <input type="hidden" name="id" id="id">
            <input type="text" id="value" name="value" class="form-control form-control-sm" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Number Modal -->
<div class="modal fade" id="addNumberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.setting') }}">
                @csrf
                <input type="hidden" name="contact_action" value="add_number">
                <input type="hidden" name="section_index" id="add_section_index">

                <div class="modal-header">
                    <h5 class="modal-title">Add Contact Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="number" class="form-control" placeholder="Enter phone number" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Number</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Number Modal -->
<div class="modal fade" id="editNumberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.setting') }}">
                @csrf
                <input type="hidden" name="contact_action" value="edit_number">
                <input type="hidden" name="section_index" id="edit_section_index">
                <input type="hidden" name="number_index" id="edit_number_index">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Contact Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="number" id="edit_number" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Contact Section Modal -->
<div class="modal fade" id="addContactSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.setting') }}">
                @csrf
                <input type="hidden" name="contact_action" value="add_section">

                <div class="modal-header">
                    <h5 class="modal-title">Add Contact Section</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                    </div>

                    <div class="form-group">
                        <label>Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Enter subtitle">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{asset('bundles/datatables/datatables.min.js')}}"></script>
<script src="{{asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script>
  // $('.table').DataTable();
   $('.set-value').on('click', function() {
     let data = $(this).data('row');
     $('#SetValue').modal('show');
     $('#id').val(data.id);
     $('#value').val(data.value);
   });
  
   // Preserve active tab on page reload
   $(document).ready(function() {
     $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
         localStorage.setItem('activeTab', $(e.target).attr('href'));
     });
     var activeTab = localStorage.getItem('activeTab');
     if(activeTab){
         $('#myTab a[href="' + activeTab + '"]').tab('show');
     }
   });
   //contact us
   $('#addNumberModal').on('show.bs.modal', function (event) {
      let button = $(event.relatedTarget);
      let sectionIndex = button.data('section');
      $('#add_section_index').val(sectionIndex);
   });

   $(document).on('click', '.edit-number', function () {
      $('#edit_section_index').val($(this).data('section'));
      $('#edit_number_index').val($(this).data('number'));
      $('#edit_number').val($(this).data('value'));

   });
</script>
@endsection
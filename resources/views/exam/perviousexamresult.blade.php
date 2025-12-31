@extends('layouts.app')
@section('title', 'Previous Exam Result')

@section('css')
<link rel="stylesheet" href="{{ asset('bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('main')
<div class="main-content">
  <section class="section">
    <div class="section-body">
      <div class="card card-primary">

        <form method="get" action="{{ route('exam.perviousexamresult') }}">
          <div class="card-header">
            <h4>Previous Exam Result</h4>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="form-group col-lg-4">
                <label>Exam Category</label>
                <select name="testcategory" id="testcategory" class="select2" required>
                  <option value="">Select Category</option>
                  @foreach ($category as $row)
                  <option value="{{ $row }}" @selected($row==request('testcategory'))>
                    {{ $row }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-lg-4">
                <label>Exam Name</label>
                <select name="examname" id="examname" class="select2" required>
                  <option value="">Select Test</option>
                  @foreach ($exam as $row)
                  <option value="{{ $row->subject }}" @selected($row->subject == request('examname'))>
                    {{ $row->subject }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-lg-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  Show Marks
                </button>
              </div>
            </div>

            @if($exams->isNotEmpty())
            <?php
            $first = $exams->first();
            $subjects = [
            'PHYSICS' => ['tot' => 'ptot', 'r' => 'pr', 'w' => 'pw', 'l' => 'pl'],
            'CHEMISTRY' => ['tot' => 'ctot', 'r' => 'cr', 'w' => 'cw', 'l' => 'cl'],
            'BOTANY' => ['tot' => 'btot', 'r' => 'br', 'w' => 'bw', 'l' => 'bl'],
            'ZOOLOGY' => ['tot' => 'ztot', 'r' => 'zr', 'w' => 'zw', 'l' => 'zl'],
            ];
            ?>
            <div class="table-responsive mt-3">
              <table class="table table-bordered" id="examTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>STUDENT ID</th>
                    <th>STUDENT NAME</th>
                    <th>SECTION</th>
                    @if($first->tot)
                    <th>{{ \Str::between($first->category, '(', ')') }}</th>
                    @else
                    @foreach($subjects as $name => $cols)
                    <th>{{ $name }}</th>
                    @endforeach
                    @endif
                    <th>TOTAL</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach($exams as $key => $row)
                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $row->stuid }}</td>
                    <td>{{ $row->sname }}</td>
                    <td>{{ $row->sec }}</td>
                    
                    @if($row->tot)
                    <td>
                      {{ $row->tot}} / {{ ($row->r + $row->w + $row->l) * 4 }}
                    </td>
                    @else
                    @foreach($subjects as $cols)
                    <td>
                      @if($row->{$cols['tot']})
                      {{ $row->{$cols['tot']} }} / {{ ($row->{$cols['r']} + $row->{$cols['w']} + $row->{$cols['l']}) * 4 }}
                      @endif
                    </td>
                    @endforeach
                    @endif
                    <td>{{ $row->tot ? $row->tot : $row->nettot }} / {{ $row->totmark }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection

@section('js')
<script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>

<script>
  $('#testcategory').on('change', function () {
    const category = $(this).val();
    window.location = `{{ route('exam.perviousexamresult') }}?testcategory=${category}`;
  });
  
  $(document).ready(function () {
      $('#examTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['csv', 'excel'],
        searching: false,
        destroy: true,
      });
  });
</script>
@endsection
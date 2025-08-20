@extends('layouts.app')
 @section('title', 'Dashboard')
 @section('main')
<div class="main-content">
  <section class="section">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="overflow-y: scroll;">
                    <table class="table table-hover table-bordered align-middle text-center shadow-sm rounded">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:50px;"><i class="fas fa-layer-group"></i></th>
                                <th class="align-middle">📚 Coaching Type</th>
                                <th class="align-middle">
                                    {{-- 👥  --}}
                                    Total <br>
                                    <span class="badge badge-info p-2">{{$total}}</span>
                                </th>
                                <th class="align-middle">👦 <br>
                                    <span class="badge badge-primary p-2">{{$boys}}</span>
                                </th>
                                <th class="align-middle">👧 <br>
                                    <span class="badge bg-pink p-2">{{$girls}}</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody id="collapse-tbody" class="collapse show">
                            @foreach($data as $coachingType => $sections)
                                @php
                                    $totalBoys = $sections->sum('boys_count');
                                    $totalGirls = $sections->sum('girls_count');
                                    $totalAll = $sections->sum('total_count');
                                @endphp

                                {{-- Parent (Coaching Type) Row --}}
                                <tr class="bg-light fw-bold cursor-pointer"
                                    data-toggle="collapse"
                                    data-target="#collapse-{{ Str::slug($coachingType) }}"
                                    aria-expanded="false">
                                    <td>
                                        <i class="fas fa-plus-circle text-success toggle-icon"
                                        id="icon-{{ Str::slug($coachingType) }}"></i>
                                    </td>
                                    <td class="text-start">{{ $coachingType }}</td>
                                    <td class="text-warning">{{ $totalAll }}</td>
                                    <td class="text-primary">{{ $totalBoys }}</td>
                                    <td class="text-pink">{{ $totalGirls }}</td>
                                </tr>

                                {{-- Child (Sections Table) --}}
                                <tr class="collapse bg-white" id="collapse-{{ Str::slug($coachingType) }}">
                                    <td colspan="5">
                                        <table class="table table-sm table-striped mb-0 border">
                                            {{-- <thead class="table-secondary" style="display: none;">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Section</th>
                                                    <th>Total</th>
                                                    <th>Boys</th>
                                                    <th>Girls</th>
                                                </tr>
                                            </thead> --}}
                                            <tbody>
                                                @foreach($sections as $section)
                                                    <tr>
                                                        
                                                        <td width="55%">{{ $section->section == '' ? '-' : $section->section }}</td>
                                                        
                                                        <td class="fw-bold" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="all">{{ $section->total_count }}</td>
                                                        <td class="text-primary" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="Male">{{ $section->boys_count }}</td>
                                                        <td class="text-pink" data-section="{{ $section->section }}" data-type="{{ $coachingType }}" data-gender="Female">{{ $section->girls_count }}</td>
                                                    </tr>
                                                @endforeach
                                                {{-- <tr class="table-warning fw-bold">
                                                    <td>Subtotal</td>
                                                    <td>{{ $totalAll }}</td>
                                                    <td>{{ $totalBoys }}</td>
                                                    <td>{{ $totalGirls }}</td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
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
  </section>
  <div class="modal fade" id="studentInfoModal" tabindex="-1" aria-labelledby="studentInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content bg-white rounded shadow">
        <div class="modal-header bg-success text-white py-2">
          <h5 class="modal-title" id="studentInfoModalLabel">Student Information</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="studentInfoModalBody">
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')

<script>
$(document).on("click", "td", function() {
    // Get data attributes
    
    const section = $(this).data("section") == '' ? '-' : $(this).data("section");
    const type = $(this).data("type");
    const gender = $(this).data("gender");
    
if (!section || !type || !gender) {
        return;
    }

    // Send AJAX request
    $.ajax({
        url: "{{ route('dashboard.gender') }}", // change this
        type: "GET",
        data: { section: section, type: type, gender: gender },
        success: function(response) {

            // Empty modal body
            $('#studentInfoModalBody').empty();
            // Open modal with data
            $('#studentInfoModalLabel').text(`${type} - ${section} - ${gender == 'all' ? 'All' : gender}`);
                const table = $('<table class="table table-striped table-bordered table-hover"></table>');
                const thead = $('<thead class="thead-dark"></thead>');
                const tbody = $('<tbody></tbody>');
                const trHead = $('<tr></tr>');
                trHead.append('<th scope="col">#</th>');
                trHead.append('<th scope="col">ID</th>');
                trHead.append('<th scope="col">Name</th>');
                trHead.append('<th scope="col">Section</th>');
                trHead.append('<th scope="col">Type</th>');
                trHead.append('<th scope="col">Gender</th>');
                trHead.append('<th scope="col">Campus</th>');
                thead.append(trHead);
                response.students.forEach((student, index) => {
                    const tr = $('<tr></tr>');
                    tr.append(`<td>${index + 1 || '-'}</td>`);
                    tr.append(`<td>${student.student_id || '-'}</td>`);
                    tr.append(`<td>${student.student_name || '-'}</td>`);
                    tr.append(`<td>${student.section || '-'}</td>`);
                    tr.append(`<td>${student.coaching_type || '-'}</td>`);
                    tr.append(`<td>${student.gender || '-'}</td>`);
                    tr.append(`<td>${student.branch.name || '-'}</td>`);
                    tbody.append(tr);
                });
                table.append(thead);
                table.append(tbody);
                $('#studentInfoModalBody').append(table);
            $('#studentInfoModal').modal('show');
        },
        error: function(xhr) {
            alert('An error occurred while fetching student information.');
        }
    });
});
</script>
    
@endsection












{{-- @extends('layouts.app')
 @section('title', 'Dashboard')
 @section('main')
 <style>
  .ibox-content {
    background-color: #ffffff;
    padding: 15px 20px 20px 20px;
    border-color: #e7eaec;
    border-style: solid solid none;
    border-width: 1px 0;
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
}
.list-item{
  display: flex;
  justify-content: space-between;
}
 </style>
<div class="main-content">
  <section class="section">
    <div class="row">

      <div class="col-md-3">
        <div class="card l-bg-red">
          <div class="card-body">
            <div class="row">
              <div class="col">
               <h4>Boys</h4>
                <span class="font-18">{{ $students->where('gender', 'Male')->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="text-white">
                  <i class="fas fa-user-friends font-24"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card l-bg-cyan-dark">
          <div class="card-body">
            <div class="row">
              <div class="col">
               <h4>Girls</h4>
                <span class="font-18">{{ $students->where('gender', 'Female')->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="text-white">
                  <i class="fas fa-user-friends font-24"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-md-3">
        <div class="card l-bg-orange">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <h4>Active Users</h4>
                <span class="font-18">{{ $students->where('active', 1)->count() }}</span>
              </div>
              <div class="col-auto">
                <div class="text-white">
                  <i class="fas fa-user-friends font-24"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3"></div>

      <div class="col-md-6 mb-3">
        <div class="ibox-content">
          <h5 class="col-black">Branch Students</h5>

            <div id="chart1"></div>
        </div> 
      </div>

      <div class="col-md-6 mb-3">
        <div class="ibox-content">
          <h5 class="col-black">Coaching Type Students</h5>

            <div id="chart2"></div>

        </div> 
      </div>
      <div class="col-md-6 mb-3">
        <div class="ibox-content">
          <h5 class="col-black">Branch Satffs</h5>
            <div id="chart3"></div>
        </div> 
      </div>


    </div>
  </section>
</div>
<div style="background-color: #5c15e0"></div>

@endsection
@section('js')
<script src="{{ asset('bundles/apexcharts/apexcharts.min.js') }}"></script>
<script>
  
    const branches = @json($branches->map(function($branch) {
        return [
            'name' => $branch->name,
            'users' => $branch->student->count()
        ];
    }));

    const filtereddata = branches.map(function(branch) {
        return {
            name: branch.name.split(',')[0],
            users: branch.users
        };
    });
  
    var options = {
        chart: {
            height: 350,
            type: 'bar',
        },
        colors: ["#ffa500"],
        plotOptions: {
            bar: {
                columnWidth: '60%',
                dataLabels: {
                    position: 'top', 
                },
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val;
            },
            offsetY: 0,
            style: {
                fontSize: '12px',
                colors: ["#ffffff"]
            }
        },
        series: [{
            name: 'Students',
            data: filtereddata.map(function(branch) {
                return branch.users;
            })
        }],
        xaxis: {
            categories: filtereddata.map(function(branch) {
                return branch.name;
            }),
            position: 'top',
            labels: {
                offsetY: -18,
                style: {
                    colors: '#9aa0ac',
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: {
                enabled: true,
                offsetY: -35,

            }
        },
        fill: {
            gradient: {
                shade: 'light',
                type: "horizontal",
                shadeIntensity: 0.25,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [50, 0, 100, 100]
            },
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
                formatter: function (val) {
                    return val;
                }
            }

        },
        title: {
            text: 'Branches Vs Students',
            floating: true,
            offsetY: 320,
            align: 'center',
            style: {
                color: '#9aa0ac'
            }
        },
    }

    var chart = new ApexCharts(
        document.querySelector("#chart1"),
        options
    );

    chart.render();
</script>
<script>

  const coachingTypes = @json($students->groupBy('coaching_type'));
  
  var options = {
    chart: {
        height: 350,
        type: 'bar',
    },
    colors: ["#f81a1a"],
    plotOptions: {
        bar: {
            columnWidth: '20%',
            dataLabels: {
                position: 'top',
            },
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val;
        },
        offsetY: 0,
        style: {
            fontSize: '12px',
            colors: ["#ffffff"]
        }
    },
    series: [{
        name: 'Students',
        data: Object.values(coachingTypes).map(function(coachingType) {
            return coachingType.length;
        })
    }],
    xaxis: {
        categories: Object.keys(coachingTypes),
        position: 'top',
        labels: {
            offsetY: -18,
            style: {
                colors: '#9aa0ac',
            }
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        },
        crosshairs: {
            fill: {
                type: 'gradient',
                gradient: {
                    colorFrom: '#D8E3F0',
                    colorTo: '#BED1E6',
                    stops: [0, 100],
                    opacityFrom: 0.4,
                    opacityTo: 0.5,
                }
            }
        },
        tooltip: {
            enabled: true,
            offsetY: -35,
        }
    },
    fill: {
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [50, 0, 100, 100]
        },
    },
    yaxis: {
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: false,
            formatter: function (val) {
                return val;
            }
        }
    },
    title: {
        text: 'Coaching Type Vs Students',
        floating: true,
        offsetY: 320,
        align: 'center',
        style: {
            color: '#9aa0ac'
        }
    },
};

var chart = new ApexCharts(document.querySelector("#chart2"), options);
chart.render();

</script>
<script>
const branchvsStaff = @json($branchvsStaff);
var options = {
    chart: {
        height: 350,
        type: 'bar',
    },
    colors: ["#5c15e0"],
    grid: {
        padding: {
            top: 35,    
        }
    },
    plotOptions: {
        bar: {
            columnWidth: '50%',
            dataLabels: {
                position: 'top',
            },
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val;
        },
        offsetY: 0,       
        style: {
            fontSize: '12px',
            colors: ["#ffffff"]
        }
    },
    series: [{
        name: 'Staff',
        data: Object.values(branchvsStaff).map(function(count) {
            return count;
        })
    }],
    xaxis: {
        categories: Object.keys(branchvsStaff).map(function(branch) {
            return branch.split(',')[0];
        }),
        position: 'top',
        labels: {
            offsetY: -18,
            style: {
                colors: '#9aa0ac',
            }
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        },
        crosshairs: {
            fill: {
                type: 'gradient',
                gradient: {
                    colorFrom: '#D8E3F0',
                    colorTo: '#BED1E6',
                    stops: [0, 100],
                    opacityFrom: 0.4,
                    opacityTo: 0.5,
                }
            }
        },
        tooltip: {
            enabled: true,
            offsetY: -35,
        }
    },
    fill: {
        gradient: {
            shade: 'light',
            type: "horizontal",
            shadeIntensity: 0.25,
            gradientToColors: undefined,
            inverseColors: true,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [50, 0, 100, 100]
        },
    },
    yaxis: {
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: false,
            formatter: function (val) {
                return val;
            }
        },
        min: 0,                   
        forceNiceScale: true,      
        tickAmount: 6,             
        decimalsInFloat: 0,        
    },
    title: {
        text: 'Branches Vs Staff',
        floating: true,
        offsetY: 320,
        align: 'center',
        style: {
            color: '#9aa0ac'
        }
    },
};

var chart = new ApexCharts(document.querySelector("#chart3"), options);
chart.render();


</script>

@endsection --}}
 @extends('layouts.app')
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
            {{-- <ul class="m-0 p-0">
              @foreach($branches as $branch)
                  <li class="list-item list-group-item">
                  <h6 class="col-black">{{ $branch->name }}</h6>
                  <span class="col-black font-16">{{ $branch->student->count() }}</span>
              </li>       
              @endforeach  
            </ul> --}}
            <div id="chart1"></div>
        </div> 
      </div>

      <div class="col-md-6 mb-3">
        <div class="ibox-content">
          <h5 class="col-black">Coaching Type Students</h5>
            {{-- <ul class="m-0 p-0">
              @foreach($students->groupBy('coaching_type') as $key => $coaching_type)
                  <li class="list-item list-group-item">
                  <h6 class="col-black">{{ $key }}</h6>
                  <span class="col-black font-16">{{ $coaching_type->count() }}</span>
              </li>       
              @endforeach  
            </ul> --}}
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
                    position: 'top', // top, center, bottom
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
            top: 35,        // Space for data labels
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
        offsetY: 0,       // Slightly closer to bars
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
        min: 0,                    // Always start from 0
        forceNiceScale: true,      // Auto-calculate nice intervals
        tickAmount: 6,             // Maximum 6 axis fractions
        decimalsInFloat: 0,        // Whole numbers only
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

@endsection
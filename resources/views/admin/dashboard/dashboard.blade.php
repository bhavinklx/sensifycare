@extends("admin.layouts.app")
@section("content")
    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-center">
        <!-- Breadcrumb starts -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
                <a href="{{ route("dashboard") }}">Home</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                Dashboard
            </li>
        </ol>
        <!-- Breadcrumb ends -->
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <div class="app-body">
        <!-- Row starts -->
        @php
            $hour = \Carbon\Carbon::now()->format('H');
            if ($hour < 12) {
                $greeting = "Good Morning";
            } elseif ($hour < 17) {
                $greeting = "Good Afternoon";
            } elseif ($hour < 21) {
                $greeting = "Good Evening";
            } else {
                $greeting = "Good Night";
            }
        @endphp
        <div class="row gx-3">
            <div class="col-xxl-12 col-sm-12">
                <div class="card mb-3 bg-2">
                    <div class="card-body">
                        <div class="py-4 px-3 text-white">
                            <h6>{{ $greeting }},</h6>
                            <h2>{{ Auth::user()->name }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row ends -->

        <!-- Row starts -->
        <div class="row gx-3">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-success rounded-circle me-3">
                                <div class="icon-box md bg-success-subtle rounded-5">
                                    <i class="ri-surgical-mask-line fs-4 text-success"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h2 class="lh-1">{{ $totalPatient }}</h2>
                                <p class="m-0">New Patients</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-1">
                            @if (auth()->user()->can('patient-list'))
                                <a class="text-success" href="{{ route("patient-list") }}">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line text-success ms-1"></i>
                                </a>
                            @endif
                            <div class="text-end">
                                <p class="mb-0 {{ $patientPercentage > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $patientPercentage >= 0 ? '+' : '' }}{{ round($patientPercentage, 2) }}%
                                </p>
                                <span class="badge {{ $patientPercentage > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small">this month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-primary rounded-circle me-3">
                                <div class="icon-box md bg-primary-subtle rounded-5">
                                    <i class="ri-stethoscope-line fs-4 text-primary"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h2 class="lh-1">{{ $totalDoctor }}</h2>
                                <p class="m-0">New Doctors</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-1">
                            @if (auth()->user()->can('doctor-list'))
                                <a class="text-primary" href="{{ route("doctor-list") }}">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            @endif
                            <div class="text-end">
                                <p class="mb-0 {{ $doctorPercentage > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $doctorPercentage >= 0 ? '+' : '' }}{{ round($doctorPercentage, 2) }}%
                                </p>
                                <span class="badge {{ $doctorPercentage > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small">this month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-danger rounded-circle me-3">
                                <div class="icon-box md bg-danger-subtle rounded-5">
                                    <i class="ri-microscope-line fs-4 text-danger"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h2 class="lh-1">980</h2>
                                <p class="m-0">Lab tests</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-1">
                            <a class="text-danger" href="javascript:void(0);">
                                <span>View All</span>
                                <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                            <div class="text-end">
                                <p class="mb-0 text-danger">+60%</p>
                                <span class="badge bg-danger-subtle text-danger small">this month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-warning rounded-circle me-3">
                                <div class="icon-box md bg-warning-subtle rounded-5">
                                    <i class="ri-money-rupee-circle-line fs-4 text-warning"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h2 class="lh-1">₹98000</h2>
                                <p class="m-0">Total Earnings</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-1">
                            <a class="text-warning" href="javascript:void(0);">
                                <span>View All</span>
                                <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                            <div class="text-end">
                                <p class="mb-0 text-warning">+20%</p>
                                <span class="badge bg-warning-subtle text-warning small">this month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row ends -->

        <!-- Row starts -->
        <div class="row gx-3">
            <div class="col-xxl-6 col-sm-12" style="height: 420px;">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Patients Registration</h5>
                    </div>
                    <div class="card-body">
                        <div id="patients"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Patients by Gender</h5>
                    </div>
                    <div class="card-body">
                        <div class="auto-align-graph">
                            <div id="genderAge"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Hospital Earnings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Row start -->
                        <div class="row g-3">
                            <div class="col-sm-12 col-12">
                                <div class="border rounded-2 d-flex align-items-center flex-row p-2">
                                    <div class="me-2">
                                        <div id="sparkline1"></div>
                                    </div>
                                    <div class="m-0">
                                        <div class="d-flex align-items-center">
                                            <h4 class="m-0 fw-bold">₹4900</h4>
                                            <div class="ms-2 text-primary d-flex">
                                                <small>20%</small> <i class="ri-arrow-right-up-line ms-1 fw-bold"></i>
                                            </div>
                                        </div>
                                        <small>Online Consultation</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-12">
                                <div class="border rounded-2 d-flex align-items-center flex-row p-2">
                                    <div class="me-2">
                                        <div id="sparkline2"></div>
                                    </div>
                                    <div class="m-0">
                                        <div class="d-flex align-items-center">
                                            <div class="fs-4 fw-bold">₹750</div>
                                            <div class="ms-2 text-danger d-flex">
                                                <small>26%</small> <i class="ri-arrow-right-down-line ms-1 fw-bold"></i>
                                            </div>
                                        </div>
                                        <small class="text-dark">Overall Purchases</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row ends -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Row ends -->
    </div>
    <!-- App body ends -->
@endsection
@section('page-js')
    <script type="text/javascript">
        var options1 = {
            chart: {
                height: 300,
                type: "area",
                toolbar: { show: false }
            },

            dataLabels: { enabled: false },
            fill: {
                type: 'solid',
                opacity: 0.2
            },
            stroke: {
                curve: "smooth",
                width: 3
            },
            series: [{
                name: 'New Patients',
                type: 'area',
                data: @json($registerData)
            }],
            grid: {
                borderColor: "#d8dee6",
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
                yaxis: {
                    lines: {
                        show: false,
                    },
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                },
            },
            xaxis: {
                categories: [
                    "Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"
                ]
            },
            yaxis: {
                labels: { show: false }
            },
            legend: {
                show: false
            },
            colors: ["#116AEF"],
            markers: {
                size: 0,
                hover: { size: 6 }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Patients";
                    }
                }
            }
        };
        var chart1 = new ApexCharts(document.querySelector("#patients"), options1);
        chart1.render();

        var options2 = {
            chart: {
                height: 348, // Same as patient chart
                type: "donut",
                toolbar: {
                    show: false
                }
            },
            labels: ["Male", "Female", "Other"],
            series: [
                {{ $male }},
                {{ $female }},
                {{ $other }}
            ],
            plotOptions: {
                pie: {
                    donut: {
                        size: "75%", // nice modern look
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: "Total",
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2
            },
            legend: {
                position: "bottom",
                horizontalAlign: "center"
            },
            colors: ["#116AEF", "#0ebb13", "#ff5a39"],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " Patients";
                    }
                }
            }
        };
        var chart2 = new ApexCharts(document.querySelector("#genderAge"), options2);
        chart2.render();
    </script>
@endsection
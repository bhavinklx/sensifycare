@extends("admin.layouts.app")

@section('content')
    <!-- App hero header starts -->
    <div class="app-hero-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <!-- Breadcrumb starts -->
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <i class="ri-home-8-line lh-1 pe-3 me-3 border-end"></i>
                <a href="{{ route("dashboard") }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route("patient-list") }}">Patients</a>
            </li>
            <li class="breadcrumb-item text-primary" aria-current="page">
                {{ $patient->full_name ?: 'Patient Profile' }}
            </li>
        </ol>
        <!-- Breadcrumb ends -->

        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="ri-printer-line"></i> Print Profile
            </button>
            <a href="{{ route('patient-list') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                <i class="ri-arrow-left-line"></i> Back to List
            </a>
        </div>
    </div>
    <!-- App Hero header ends -->

    <!-- App body starts -->
    <div class="app-body">

        <!-- Toast notification container -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
            <div id="actionToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">Action completed</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- 1. Hero Header Card -->
        <div class="row gx-3">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                            <!-- Left: Avatar & Identity -->
                            <div class="d-flex align-items-center gap-3">
                                @if($patient->image_url)
                                    <img src="{{ $patient->image_url }}" alt="{{ $patient->full_name }}" class="rounded-circle border border-2 border-primary shadow-sm" style="width: 76px; height: 76px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center border border-2 border-primary-subtle shadow-sm" style="width: 76px; height: 76px; font-size: 26px;">
                                        {{ strtoupper(substr($patient->patient_fname ?: 'P', 0, 1) . substr($patient->patient_lname ?: '', 0, 1)) }}
                                    </div>
                                @endif

                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h4 class="mb-0 fw-bold">{{ $patient->full_name ?: 'Unnamed Patient' }}</h4>
                                        @if($patient->patient_gender)
                                            <span class="badge {{ strtolower($patient->patient_gender) == 'male' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning' }}">
                                                {{ ucwords($patient->patient_gender) }}
                                            </span>
                                        @endif
                                        @if($patient->patient_age)
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $patient->patient_age }} Yrs</span>
                                        @endif
                                        @if($patient->is_profile_complete)
                                            <span class="badge bg-success-subtle text-success"><i class="ri-shield-check-line me-1"></i>Profile Complete</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning"><i class="ri-error-warning-line me-1"></i>Step: {{ ucwords($patient->profile_step ?: 'Incomplete') }}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center flex-wrap gap-2 text-muted small">
                                        <span class="badge bg-light text-dark border">
                                            <i class="ri-hashtag"></i> UID: <span id="uidText">{{ $patient->patient_uid ?: 'N/A' }}</span>
                                            <button type="button" class="btn btn-sm btn-link p-0 ms-1 text-secondary" onclick="copyToClipboard('{{ $patient->patient_uid }}', 'UID copied to clipboard!')" title="Copy UID">
                                                <i class="ri-file-copy-line"></i>
                                            </button>
                                        </span>

                                        @if($patient->patient_phone)
                                            <span>
                                                <i class="ri-phone-line text-primary me-1"></i>
                                                <a href="tel:{{ $patient->patient_phone }}" class="text-decoration-none text-body">{{ $patient->patient_phone }}</a>
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 text-secondary" onclick="copyToClipboard('{{ $patient->patient_phone }}', 'Phone copied!')" title="Copy Phone">
                                                    <i class="ri-file-copy-line"></i>
                                                </button>
                                            </span>
                                            <span class="text-secondary">•</span>
                                        @endif

                                        @if($patient->patient_email)
                                            <span>
                                                <i class="ri-mail-line text-primary me-1"></i>
                                                <a href="mailto:{{ $patient->patient_email }}" class="text-decoration-none text-body">{{ $patient->patient_email }}</a>
                                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 text-secondary" onclick="copyToClipboard('{{ $patient->patient_email }}', 'Email copied!')" title="Copy Email">
                                                    <i class="ri-file-copy-line"></i>
                                                </button>
                                            </span>
                                            <span class="text-secondary">•</span>
                                        @endif

                                        @if($patient->patient_city)
                                            <span>
                                                <i class="ri-map-pin-line text-danger me-1"></i>{{ $patient->patient_city }}{{ $patient->patient_state ? ', ' . $patient->patient_state : '' }}
                                            </span>
                                            <span class="text-secondary">•</span>
                                        @endif

                                        <span>
                                            <i class="ri-calendar-line text-secondary me-1"></i>Registered: {{ $patient->created_at ? $patient->created_at->format('d M, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Status Toggle -->
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-end">
                                    <div class="small text-muted mb-1">Account Status</div>
                                    <div id="td_status_{{ $patient->patient_id }}">
                                        @if ($patient->patient_status == '1')
                                            <a href="javascript:void(0)" onclick="change_status({{ $patient->patient_id }}, 0)">
                                                <span class="badge bg-success py-2 px-3 fs-6">
                                                    <i class="ri-checkbox-circle-line me-1"></i> Active
                                                </span>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" onclick="change_status({{ $patient->patient_id }}, 1)">
                                                <span class="badge bg-danger py-2 px-3 fs-6">
                                                    <i class="ri-close-circle-line me-1"></i> Inactive
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. High-Impact KPI Metrics Row -->
        <div class="row gx-3">
            <!-- KPI 1: Overall Health Score -->
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-{{ $scoreColor }} rounded-circle me-3">
                                <div class="icon-box md bg-{{ $scoreColor }}-subtle rounded-5">
                                    <i class="ri-heart-pulse-line fs-4 text-{{ $scoreColor }}"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1 mb-1 fw-bold">{{ $totalHealthScore }}<span class="fs-6 text-muted">/100</span></h3>
                                <p class="m-0 text-muted small">Overall Health Score</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                            <span class="badge {{ $scoreBadge }} small">{{ $scoreLabel }}</span>
                            <span class="text-muted small">4 Pillars Combined</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 2: BMI Index -->
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-{{ $patient->bmi_status['class'] }} rounded-circle me-3">
                                <div class="icon-box md bg-{{ $patient->bmi_status['class'] }}-subtle rounded-5">
                                    <i class="ri-scales-3-line fs-4 text-{{ $patient->bmi_status['class'] }}"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1 mb-1 fw-bold">
                                    {{ $patient->bmi ? $patient->bmi : 'N/A' }}
                                    @if($patient->bmi)<span class="fs-6 text-muted">kg/m²</span>@endif
                                </h3>
                                <p class="m-0 text-muted small">Body Mass Index</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                            <span class="badge {{ $patient->bmi_status['badge'] }} small">{{ $patient->bmi_status['label'] }}</span>
                            <span class="text-muted small">{{ $patient->height_cm ?: '-' }}cm / {{ $patient->weight_kg ?: '-' }}kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 3: Diagnostic Reports -->
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-primary rounded-circle me-3">
                                <div class="icon-box md bg-primary-subtle rounded-5">
                                    <i class="ri-file-list-3-line fs-4 text-primary"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1 mb-1 fw-bold">{{ $totalReportsCount }}</h3>
                                <p class="m-0 text-muted small">Diagnostic Lab Reports</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                            @if($totalAbnormal > 0)
                                <span class="badge bg-danger-subtle text-danger small"><i class="ri-alert-line me-1"></i>{{ $totalAbnormal }} Abnormal Markers</span>
                            @else
                                <span class="badge bg-success-subtle text-success small"><i class="ri-check-line me-1"></i>All Markers Normal</span>
                            @endif
                            <span class="text-muted small">{{ $totalMarkers }} Markers</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 4: Active Symptoms -->
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-{{ $patient->symptoms->count() > 0 ? 'warning' : 'success' }} rounded-circle me-3">
                                <div class="icon-box md bg-{{ $patient->symptoms->count() > 0 ? 'warning' : 'success' }}-subtle rounded-5">
                                    <i class="ri-health-book-line fs-4 text-{{ $patient->symptoms->count() > 0 ? 'warning' : 'success' }}"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1 mb-1 fw-bold">{{ $patient->symptoms->count() }}</h3>
                                <p class="m-0 text-muted small">Reported Symptoms</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top">
                            @if($patient->symptoms->count() === 0 && empty($patient->patient_other_symptoms))
                                <span class="badge bg-success-subtle text-success small">Asymptomatic</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning small">Monitoring Needed</span>
                            @endif
                            <span class="text-muted small">{{ $patient->patient_other_symptoms ? 'Custom notes' : 'No notes' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Tabbed Content Container -->
        <div class="row gx-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">
                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="patientDetailTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-overview-btn" data-bs-toggle="tab" href="#tab-overview" role="tab" aria-controls="tab-overview" aria-selected="true">
                                        <i class="ri-user-heart-line me-1"></i> Overview & Vitals
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-lifestyle-btn" data-bs-toggle="tab" href="#tab-lifestyle" role="tab" aria-controls="tab-lifestyle" aria-selected="false">
                                        <i class="ri-heart-pulse-line me-1"></i> Lifestyle & Habits
                                        <span class="badge bg-primary-subtle text-primary ms-1">{{ count($lifestyleDetails) }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-symptoms-btn" data-bs-toggle="tab" href="#tab-symptoms" role="tab" aria-controls="tab-symptoms" aria-selected="false">
                                        <i class="ri-health-book-line me-1"></i> Symptoms & Concerns
                                        <span class="badge {{ $patient->symptoms->count() > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} ms-1">{{ $patient->symptoms->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-reports-btn" data-bs-toggle="tab" href="#tab-reports" role="tab" aria-controls="tab-reports" aria-selected="false">
                                        <i class="ri-file-list-3-line me-1"></i> Lab Reports
                                        <span class="badge bg-info-subtle text-info ms-1">{{ $patient->reports->count() }}</span>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-notifications-btn" data-bs-toggle="tab" href="#tab-notifications" role="tab" aria-controls="tab-notifications" aria-selected="false">
                                        <i class="ri-notification-3-line me-1"></i> Notifications & Activity
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $patient->notifications->count() }}</span>
                                    </a>
                                </li>
                            </ul>
                            <!-- Nav tabs ends -->

                            <!-- Tab content starts -->
                            <div class="tab-content pt-3" id="patientDetailTabsContent">

                                <!-- TAB 1: Overview & Vitals -->
                                <div class="tab-pane fade show active" id="tab-overview" role="tabpanel" aria-labelledby="tab-overview-btn">
                                    <div class="row gx-3">
                                        <!-- Personal & Demographics Card -->
                                        <div class="col-lg-6">
                                            <div class="card border mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title m-0 d-flex align-items-center">
                                                        <i class="ri-user-line text-primary me-2"></i> Personal & Demographic Details
                                                    </h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-striped m-0 align-middle">
                                                        <tbody>
                                                            <tr>
                                                                <th style="width: 35%;" class="ps-3 text-muted fw-normal">Full Name</th>
                                                                <td class="pe-3 fw-medium">{{ $patient->full_name ?: '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Date of Birth</th>
                                                                <td class="pe-3">{{ $patient->patient_dob ? date('d M, Y', strtotime($patient->patient_dob)) : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Age</th>
                                                                <td class="pe-3">{{ $patient->patient_age ? $patient->patient_age . ' Years' : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Gender</th>
                                                                <td class="pe-3">{{ $patient->patient_gender ? ucwords($patient->patient_gender) : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Marital Status</th>
                                                                <td class="pe-3">{{ $patient->patient_marital_status ? ucwords($patient->patient_marital_status) : '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Occupation</th>
                                                                <td class="pe-3">{{ $patient->patient_occupation ?: '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Preferred Language</th>
                                                                <td class="pe-3">
                                                                    <span class="badge bg-primary-subtle text-primary">{{ $patient->preferred_language ?: 'English' }}</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Phone</th>
                                                                <td class="pe-3">
                                                                    @if($patient->patient_phone)
                                                                        <a href="tel:{{ $patient->patient_phone }}" class="text-primary text-decoration-none">
                                                                            {{ $patient->patient_phone }}
                                                                        </a>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Email</th>
                                                                <td class="pe-3">
                                                                    @if($patient->patient_email)
                                                                        <a href="mailto:{{ $patient->patient_email }}" class="text-primary text-decoration-none">
                                                                            {{ $patient->patient_email }}
                                                                        </a>
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="ps-3 text-muted fw-normal">Full Address</th>
                                                                <td class="pe-3">
                                                                    {{ $patient->patient_address ?: '-' }}
                                                                    @if($patient->patient_city || $patient->patient_state || $patient->patient_postal_code)
                                                                        <div class="text-muted small">
                                                                            {{ implode(', ', array_filter([$patient->patient_city, $patient->patient_state, $patient->patient_postal_code])) }}
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Vitals & Health Score Breakdown Card -->
                                        <div class="col-lg-6">
                                            <!-- Clinical Vitals -->
                                            <div class="card border mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title m-0 d-flex align-items-center">
                                                        <i class="ri-stethoscope-line text-danger me-2"></i> Clinical Measures & Vitals
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <!-- Blood Group -->
                                                        <div class="col-sm-6">
                                                            <div class="p-3 border rounded bg-light text-center">
                                                                <span class="text-muted small d-block mb-1">Blood Group</span>
                                                                <span class="fs-4 fw-bold text-danger">
                                                                    {{ $patient->patient_blood_group ? strtoupper($patient->patient_blood_group) : 'Not specified' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Blood Pressure -->
                                                        <div class="col-sm-6">
                                                            <div class="p-3 border rounded bg-light text-center">
                                                                <span class="text-muted small d-block mb-1">Blood Pressure</span>
                                                                <span class="fs-5 fw-bold text-dark">
                                                                    {{ $patient->patient_blood_pressure ?: 'Not measured' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Blood Sugar -->
                                                        <div class="col-sm-6">
                                                            <div class="p-3 border rounded bg-light text-center">
                                                                <span class="text-muted small d-block mb-1">Blood Sugar Level</span>
                                                                <span class="fs-5 fw-bold text-dark">
                                                                    {{ $patient->patient_sugar_level ?: 'Not measured' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Height & Weight -->
                                                        <div class="col-sm-6">
                                                            <div class="p-3 border rounded bg-light text-center">
                                                                <span class="text-muted small d-block mb-1">Height & Weight</span>
                                                                <span class="fs-5 fw-bold text-dark">
                                                                    {{ $patient->height_cm ? $patient->height_cm . ' cm' : '-' }} / {{ $patient->weight_kg ? $patient->weight_kg . ' kg' : '-' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- BMI Visual Breakdown -->
                                                    <div class="mt-3 p-3 border rounded">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="fw-medium">Body Mass Index (BMI)</span>
                                                            <span class="badge {{ $patient->bmi_status['badge'] }}">{{ $patient->bmi_status['label'] }}</span>
                                                        </div>
                                                        @if($patient->bmi)
                                                            <div class="progress" style="height: 10px;">
                                                                @php
                                                                    $bmiVal = $patient->bmi;
                                                                    $pct = min(max(($bmiVal / 40) * 100, 10), 100);
                                                                @endphp
                                                                <div class="progress-bar bg-{{ $patient->bmi_status['class'] }}" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $bmiVal }}" aria-valuemin="0" aria-valuemax="40"></div>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-muted small mt-1">
                                                                <span>&lt; 18.5 Underweight</span>
                                                                <span>18.5 - 24.9 Normal</span>
                                                                <span>25 - 29.9 Overweight</span>
                                                                <span>&gt; 30 Obese</span>
                                                            </div>
                                                        @else
                                                            <p class="text-muted small m-0">Height or weight missing to calculate BMI.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Health Score 4 Pillars Breakdown Card -->
                                            <div class="card border mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="card-title m-0 d-flex align-items-center">
                                                        <i class="ri-pie-chart-2-line text-success me-2"></i> Health Score Pillar Breakdown
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Pillar 1: Lab Reports -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <span><i class="ri-flask-line me-1 text-primary"></i> Lab Reports Quality</span>
                                                            <span class="fw-bold">{{ $labReportScore }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $labReportScore }}%;"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Pillar 2: Lifestyle & Habits -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <span><i class="ri-heart-pulse-line me-1 text-success"></i> Lifestyle Habits Score</span>
                                                            <span class="fw-bold">{{ $lifestyleScore }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $lifestyleScore }}%;"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Pillar 3: Symptoms -->
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <span><i class="ri-health-book-line me-1 text-warning"></i> Symptoms Profile</span>
                                                            <span class="fw-bold">{{ $symptomScore }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $symptomScore }}%;"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Pillar 4: Profile Completeness -->
                                                    <div>
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <span><i class="ri-user-follow-line me-1 text-info"></i> Profile Completeness</span>
                                                            <span class="fw-bold">{{ $completenessScore }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $completenessScore }}%;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: Lifestyle & Habits -->
                                <div class="tab-pane fade" id="tab-lifestyle" role="tabpanel" aria-labelledby="tab-lifestyle-btn">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold">Patient Lifestyle Profiling</h6>
                                            <p class="text-muted small mb-0">Routine habits, physical activity, and dietary preferences provided by patient.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-subtle text-success fs-6 py-2 px-3">
                                                <i class="ri-check-double-line me-1"></i> Lifestyle Score: {{ $lifestyleScore }}%
                                            </span>
                                        </div>
                                    </div>

                                    @if(count($lifestyleDetails) > 0)
                                        <div class="row g-3">
                                            @foreach($lifestyleDetails as $habit)
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="card border h-100 mb-0 shadow-none">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fs-3">{{ $habit['emoji'] }}</span>
                                                                    <div>
                                                                        <h6 class="mb-0 fw-semibold">{{ $habit['name'] }}</h6>
                                                                        <small class="text-muted">{{ $habit['question'] }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <span class="text-muted small d-block">Reported Answer</span>
                                                                    <span class="fw-bold text-dark">{{ $habit['answer'] }}</span>
                                                                </div>
                                                                <span class="badge {{ $habit['badge_class'] }}">{{ $habit['status_label'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="ri-heart-pulse-line fs-1 text-muted"></i>
                                            <h6 class="mt-2 text-muted">No lifestyle profiling questions answered yet.</h6>
                                        </div>
                                    @endif
                                </div>

                                <!-- TAB 3: Symptoms & Concerns -->
                                <div class="tab-pane fade" id="tab-symptoms" role="tabpanel" aria-labelledby="tab-symptoms-btn">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold">Active Symptoms & Medical Concerns</h6>
                                            <p class="text-muted small mb-0">Current health complaints and physiological symptoms flagged by the patient.</p>
                                        </div>
                                        <span class="badge {{ $patient->symptoms->count() > 0 ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} fs-6 py-2 px-3">
                                            {{ $patient->symptoms->count() }} Active Reported
                                        </span>
                                    </div>

                                    @if($patient->symptoms->count() > 0)
                                        <div class="row g-3">
                                            @foreach($patient->symptoms as $symptom)
                                                <div class="col-md-6 col-xl-4">
                                                    <div class="card border border-warning-subtle h-100 mb-0 shadow-none">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="fs-2 p-2 bg-warning-subtle rounded-3 text-center" style="min-width: 50px;">
                                                                    {{ $symptom->symptom_emoji ?: '⚠️' }}
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1 fw-bold text-dark">{{ $symptom->symptom_name }}</h6>
                                                                    <p class="text-muted small mb-0">
                                                                        {{ $symptom->symptom_desc ?: 'Symptom reported during onboarding assessment.' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-success border-0 bg-success-subtle d-flex align-items-center gap-3 p-4 mb-3" role="alert">
                                            <i class="ri-checkbox-circle-fill fs-1 text-success"></i>
                                            <div>
                                                <h5 class="alert-heading mb-1 text-success fw-bold">No Active Symptoms Reported</h5>
                                                <p class="mb-0 text-success">The patient did not select any physiological symptoms during their health assessment.</p>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Other Custom Symptoms Notes -->
                                    <div class="card border mt-3">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title m-0 d-flex align-items-center">
                                                <i class="ri-file-text-line text-primary me-2"></i> Additional Symptoms & Patient Notes
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($patient->patient_other_symptoms))
                                                <div class="p-3 bg-light rounded border">
                                                    <p class="mb-0 fst-italic text-dark">"{{ $patient->patient_other_symptoms }}"</p>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0 small">No additional custom symptoms or notes written by the patient.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 4: Diagnostic Lab Reports -->
                                <div class="tab-pane fade" id="tab-reports" role="tabpanel" aria-labelledby="tab-reports-btn">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold">Diagnostic Lab Reports & OCR Data</h6>
                                            <p class="text-muted small mb-0">Medical laboratory tests, pathology reports, and extracted bio-markers.</p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary-subtle text-primary py-2 px-3">
                                                Total Reports: {{ $totalReportsCount }}
                                            </span>
                                            @if($totalAbnormal > 0)
                                                <span class="badge bg-danger-subtle text-danger py-2 px-3">
                                                    <i class="ri-alert-line me-1"></i> {{ $totalAbnormal }} Abnormal Markers
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($patient->reports->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Report Details</th>
                                                        <th>Uploaded Date</th>
                                                        <th>File Size</th>
                                                        <th>Status</th>
                                                        <th>Quality / Score</th>
                                                        <th>Markers Breakdown</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($patient->reports as $report)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="icon-box sm bg-primary-subtle text-primary rounded">
                                                                        <i class="ri-file-pdf-line fs-5"></i>
                                                                    </div>
                                                                    <div>
                                                                        <span class="fw-bold text-dark d-block">
                                                                            {{ $report->report_title ?: ($report->file_name ?: 'Diagnostic Report #' . $report->patient_report_id) }}
                                                                        </span>
                                                                        @if(!empty($report->ocr_data) && is_array($report->ocr_data))
                                                                            <small class="text-muted">
                                                                                @if(isset($report->ocr_data['blood_group']))
                                                                                    Blood: <strong>{{ $report->ocr_data['blood_group'] }}</strong> •
                                                                                @endif
                                                                                @if(isset($report->ocr_data['hemoglobin']))
                                                                                    Hb: <strong>{{ $report->ocr_data['hemoglobin'] }}</strong>
                                                                                @endif
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $report->created_at ? $report->created_at->format('d M, Y h:i A') : '-' }}</td>
                                                            <td>{{ $report->formatted_file_size ?: '-' }}</td>
                                                            <td>
                                                                @if(strtolower($report->status) == 'processed')
                                                                    <span class="badge bg-success-subtle text-success"><i class="ri-checkbox-circle-line me-1"></i>Processed</span>
                                                                @elseif(strtolower($report->status) == 'processing')
                                                                    <span class="badge bg-warning-subtle text-warning"><i class="ri-loader-4-line me-1"></i>Processing</span>
                                                                @else
                                                                    <span class="badge bg-danger-subtle text-danger"><i class="ri-close-circle-line me-1"></i>{{ ucwords($report->status ?: 'Pending') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($report->score)
                                                                    <span class="fw-bold">{{ $report->score }}%</span>
                                                                    <small class="text-muted">({{ ucwords($report->report_quality ?: 'Good') }})</small>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <span class="badge bg-success-subtle text-success small">{{ $report->ok_count ?: 0 }} Normal</span>
                                                                    @if(($report->abnormal_count ?: 0) > 0)
                                                                        <span class="badge bg-danger-subtle text-danger small">{{ $report->abnormal_count }} Abnormal</span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                @if($report->file_path)
                                                                    <a href="{{ $report->file_path }}" target="_blank" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Open Report Document">
                                                                        <i class="ri-external-link-line me-1"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted small">No file</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5 border rounded bg-light">
                                            <i class="ri-file-list-3-line fs-1 text-muted"></i>
                                            <h6 class="mt-2 text-muted">No diagnostic lab reports uploaded by this patient yet.</h6>
                                            <p class="text-muted small mb-0">When the patient uploads blood test reports or prescriptions, they will be processed and displayed here.</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- TAB 5: Notifications & Activity -->
                                <div class="tab-pane fade" id="tab-notifications" role="tabpanel" aria-labelledby="tab-notifications-btn">
                                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-0 fw-bold">In-App Notifications & Activity Feed</h6>
                                            <p class="text-muted small mb-0">Record of alerts, reminders, and broadcast notifications delivered to this patient.</p>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-secondary py-2 px-3">
                                            {{ $patient->notifications->count() }} Total Notifications
                                        </span>
                                    </div>

                                    @if($patient->notifications->count() > 0)
                                        <div class="list-group">
                                            @foreach($patient->notifications as $notif)
                                                <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3">
                                                    <div class="icon-box sm {{ $notif->is_read ? 'bg-light text-muted' : 'bg-primary-subtle text-primary' }} rounded-circle mt-1">
                                                        <i class="ri-notification-3-line"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1 {{ $notif->is_read ? 'text-secondary' : 'fw-bold text-dark' }}">{{ $notif->title }}</h6>
                                                            <small class="text-muted">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</small>
                                                        </div>
                                                        <p class="mb-1 text-muted small">{{ $notif->body }}</p>
                                                        <div class="d-flex align-items-center gap-2 mt-1">
                                                            @if($notif->is_read)
                                                                <span class="badge bg-light text-muted small"><i class="ri-check-line me-1"></i>Read</span>
                                                            @else
                                                                <span class="badge bg-info-subtle text-info small"><i class="ri-mail-unread-line me-1"></i>Unread</span>
                                                            @endif
                                                            <span class="text-muted small">{{ $notif->created_at ? $notif->created_at->format('d M, Y h:i A') : '' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5 border rounded bg-light">
                                            <i class="ri-notification-off-line fs-1 text-muted"></i>
                                            <h6 class="mt-2 text-muted">No notification logs recorded for this patient.</h6>
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <!-- Tab content ends -->

                        </div>
                        <!-- Custom tabs ends -->
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- App body ends -->
@endsection

@section('page-js')
<style>
    @media print {
        .app-hero-header, .navbar, .sidebar, .page-header, .btn, .nav-tabs, footer, #loading-wrapper {
            display: none !important;
        }
        .app-body {
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }
    }
</style>

<script type="text/javascript">
    function showToast(msg) {
        document.getElementById('toastMessage').innerText = msg;
        const toastElement = document.getElementById('actionToast');
        const toast = new bootstrap.Toast(toastElement, { delay: 2500 });
        toast.show();
    }

    function copyToClipboard(text, successMsg) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            showToast(successMsg || 'Copied to clipboard!');
        }).catch(function() {
            // Fallback for older browsers
            let tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            showToast(successMsg || 'Copied to clipboard!');
        });
    }

    function change_status(patient_id, status) {
        $.ajax({
            url: "{{ route('patient-change-status') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                patient_id: patient_id,
                status: status
            },
            success: function(response) {
                if (status == 1) {
                    $('#td_status_' + patient_id).html('<a href="javascript:void(0)" onclick="change_status(' + patient_id + ',0)"><span class="badge bg-success py-2 px-3 fs-6"><i class="ri-checkbox-circle-line me-1"></i> Active</span></a>');
                    showToast('Patient activated successfully');
                } else {
                    $('#td_status_' + patient_id).html('<a href="javascript:void(0)" onclick="change_status(' + patient_id + ',1)"><span class="badge bg-danger py-2 px-3 fs-6"><i class="ri-close-circle-line me-1"></i> Inactive</span></a>');
                    showToast('Patient inactivated successfully');
                }
            },
            error: function() {
                showToast('Failed to update status');
            }
        });
    }
</script>
@endsection

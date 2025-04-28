<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Form - {{ setting()->name ?? 'Client Onboarding' }}</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('admin/assets/logo/favicon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('admin/assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Content -->
            <div class="content-wrapper">

                <div class="container-fluid flex-grow-1 container-p-y">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> Client Onboarding
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Validation Wizard -->
                        <div class="col-12 mb-4">
                            {{-- <small class="text-light fw-semibold">Validation</small> --}}
                            <div id="wizard-validation" class="bs-stepper mt-2">
                                <div class="bs-stepper-header overflow-auto">
                                    <div class="step" data-target="#account-details-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">1</span>
                                            <span class="bs-stepper-label mt-1">
                                                <span class="bs-stepper-title">Company Information</span>
                                                <span class="bs-stepper-subtitle">Company Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#personal-info-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">2</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Main Principle</span>
                                                <span class="bs-stepper-subtitle">Principle Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#social-links-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">3</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Other Company Info</span>
                                                <span class="bs-stepper-subtitle">Other Company Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#equipment-pricing-detail-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">4</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Equipment Pricing Details</span>
                                                <span class="bs-stepper-subtitle">Equipment Pricing Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#document-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">5</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Documents</span>
                                                <span class="bs-stepper-subtitle">Documents Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#sales-associates-reps-involved-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">6</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Sales Associates/Reps Involved</span>
                                                <span class="bs-stepper-subtitle">SA/RI Details</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="line">
                                        <i class="ti ti-chevron-right"></i>
                                    </div>
                                    <div class="step" data-target="#future-upsell-opportunity-validation">
                                        <button type="button" class="step-trigger">
                                            <span class="bs-stepper-circle">7</span>
                                            <span class="bs-stepper-label">
                                                <span class="bs-stepper-title">Future Upsell Opportunity</span>
                                                <span class="bs-stepper-subtitle">Future Upsell Opportunity Details</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bs-stepper-content">
                                    <form id="wizard-validation-form" action="{{ route('clients.store') }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <!-- Company Information -->
                                        <div id="account-details-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Account Details</h6>
                                                <small>Enter Your Account Details.</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationDbaName">DBA Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationDbaName"
                                                        id="formValidationDbaName" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationLegalName">Legal Name
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationLegalName"
                                                        id="formValidationLegalName" class="form-control"
                                                        placeholder="Your answer" aria-label="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationFullName">Contact Nane
                                                        (Full Name) <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationFullName"
                                                        id="formValidationFullName" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationBEdate">Business
                                                        Established Date <span class="text-danger">*</span></label>
                                                    <input type="date" name="formValidationBEdate"
                                                        id="formValidationBEdate" class="form-control"
                                                        placeholder="MM/DD/YYYY" aria-label="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationBaddress">Business
                                                        Address <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationBaddress"
                                                        id="formValidationBaddress" class="form-control"
                                                        placeholder="Your answer" aria-label="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationBproductAndServices">Business Product & Services
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationBproductAndServices"
                                                        id="formValidationBproductAndServices" class="form-control"
                                                        placeholder="Your answer" aria-label="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationRegion">Region <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationRegion" class="form-check-input"
                                                                type="radio" value="USA"
                                                                id="formValidationRegion-usa" checked="" />
                                                            <label class="form-check-label" for="region-usa">USA </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationRegion" class="form-check-input"
                                                                type="radio" value="Canada"
                                                                id="formValidationRegion-canada" />
                                                            <label class="form-check-label" for="region-canada">Canada
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationDBAphone_number">DBA
                                                        Phone Number <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationDBAphone_number"
                                                        id="formValidationDBAphone_number" class="form-control"
                                                        placeholder="Your answer" aria-label="Your answer" />
                                                </div>
    
                                                <div class="col-sm-12 col-sm-6">
                                                    <label class="form-label" for="formValidationbusinessemail">Email
                                                        Address <span class="text-danger">*</span></label>
                                                    <input type="email" name="formValidationbusinessemail"
                                                        id="formValidationbusinessemail" class="form-control"
                                                        placeholder="Your answer" aria-label="Your answer" />
                                                </div>
    
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev"
                                                        disabled>
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Main Principles -->
                                        <div id="personal-info-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Main Principle</h6>
                                                <small>Enter the Principle</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationProncipleFullName">Full
                                                        Name <span class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationProncipleFullName"
                                                        name="formValidationProncipleFullName" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationDateOfBirth">Date of
                                                        Birth <span class="text-danger">*</span></label>
                                                    <input type="date" id="formValidationDateOfBirth"
                                                        name="formValidationDateOfBirth" class="form-control"
                                                        placeholder="Date of birth" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationBusinessType">Business
                                                        Type <span class="text-danger">*</span></label>
                                                    <div class="col-md-12 mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationBusinessType"
                                                                class="form-check-input" type="radio"
                                                                value="Corporation"
                                                                id="formValidationBusinessType-corporation" />
                                                            <label class="form-check-label"
                                                                for="BusinessType-corporation">Corporation </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationBusinessType"
                                                                class="form-check-input" type="radio" value="LLC"
                                                                id="formValidationBusinessType-llc" />
                                                            <label class="form-check-label" for="BusinessType-llc">LLC
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationBusinessType"
                                                                class="form-check-input" type="radio"
                                                                value="Partnership"
                                                                id="formValidationBusinessType-partnership" />
                                                            <label class="form-check-label"
                                                                for="BusinessType-partnership">Partnership </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationBusinessType"
                                                                class="form-check-input" type="radio"
                                                                value="Sole Proprietorship"
                                                                id="formValidationBusinessType-sole-proprietorship" />
                                                            <label class="form-check-label"
                                                                for="BusinessType-sole-proprietorship">Sole Proprietorship
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationBusinessType"
                                                                class="form-check-input" type="radio" value="Other"
                                                                id="formValidationBusinessType-other" />
                                                            <label class="form-check-label" for="BusinessType-other">Other
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationOwnershipPercent">Ownership Percent (%) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="formValidationOwnershipPercent"
                                                        name="formValidationOwnershipPercent" class="form-control"
                                                        placeholder="Your answer" min="0" max="100" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationSSN">SSN <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationSSN" name="formValidationSSN"
                                                        class="form-control" placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationTaxID">Tax ID <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="formValidationTaxID"
                                                        name="formValidationTaxID" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationEIN">EIN (if applicable)
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationEIN" name="formValidationEIN"
                                                        class="form-control" placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationphoneNumber">Phone Number
                                                        (Cell Phone) <span class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationphoneNumber"
                                                        name="formValidationphoneNumber" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationResidentalAddress">Residental Address <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationResidentalAddress"
                                                        name="formValidationResidentalAddress" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationShippingAddress">Shipping
                                                        Address <span class="text-danger">*</span></label>
                                                    <input type="text" id="formValidationShippingAddress"
                                                        name="formValidationShippingAddress" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Other Company Information -->
                                        <div id="social-links-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Other Company Information</h6>
                                                <small>Enter Your Other Company Details</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationCompanyAnnualRevenue">Company Annual Revenue
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationCompanyAnnualRevenue"
                                                        id="formValidationCompanyAnnualRevenue" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationMonthlyCardSales">Monthly
                                                        Card Sales <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationMonthlyCardSales"
                                                        id="formValidationMonthlyCardSales" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationAverageTransaction">Average Transaction <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationAverageTransaction"
                                                        id="formValidationAverageTransaction" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationHighestAverageTicket">Highest Average Ticket
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationHighestAverageTicket"
                                                        id="formValidationHighestAverageTicket" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationFrequencofHighestAverageTicket">Frequency of
                                                        Highest Average Ticket <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                        name="formValidationFrequencofHighestAverageTicket"
                                                        id="formValidationFrequencofHighestAverageTicket"
                                                        class="form-control" placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationBusinessWebsite">Business
                                                        Website <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationBusinessWebsite"
                                                        id="formValidationBusinessWebsite" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationCardPresent">Card Present
                                                        (%) <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationCardPresent"
                                                        id="formValidationCardPresent" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationCardNotPresent">Card Not
                                                        Present (%) <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationCardNotPresent"
                                                        id="formValidationCardNotPresent" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationInternet">Internet (%)
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationInternet"
                                                        id="formValidationInternet" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Equipment / Pricing Details -->
                                        <div id="equipment-pricing-detail-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Equipment Pricing Details</h6>
                                                <small>Enter Your Equipment Pricing Details</small>
                                            </div>
    
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationMachineModelNumber">Machine - Model Number <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationMachineModelNumber"
                                                        id="formValidationMachineModelNumber" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationAgreementType">Agreement
                                                        Type <span class="text-danger">*</span></label>
                                                    <div class="col-md-12 mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationAgreementType"
                                                                class="form-check-input" type="radio" value="Rent"
                                                                id="formValidationAgreementType-rent" />
                                                            <label class="form-check-label"
                                                                for="AgreementType-rent">Rent</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationAgreementType"
                                                                class="form-check-input" type="radio" value="Lease"
                                                                id="formValidationAgreementType-lease" />
                                                            <label class="form-check-label"
                                                                for="AgreementType-lease">Lease</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input name="formValidationAgreementType"
                                                                class="form-check-input" type="radio" value="Buyout"
                                                                id="formValidationAgreementType-buyout" />
                                                            <label class="form-check-label"
                                                                for="AgreementType-buyout">Buyout</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationMonthlyCharge">Monthly
                                                        Charge <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationMonthlyCharge"
                                                        id="formValidationMonthlyCharge" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationInterchangeCategory">Interchange Category <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationInterchangeCategory"
                                                        id="formValidationInterchangeCategory" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationInterchangeRateDebitCharge">Interchange Rate +
                                                        Debit Charge <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationInterchangeRateDebitCharge"
                                                        id="formValidationInterchangeRateDebitCharge" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationOtherFees">Other Fees
                                                        (Not to be Charged) <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationOtherFees"
                                                        id="formValidationOtherFees" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Documents -->
                                        <div id="document-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Documents</h6>
                                                <small>Enter Your Documents</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationDriverLicense">Driver's
                                                        License (ID Proof) <span class="text-danger">*</span></label>
                                                    <input type="file" name="formValidationDriverLicense"
                                                        id="formValidationDriverLicense" class="form-control"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        placeholder="Upload your ID proof" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationBankingDetails">Banking
                                                        Details (Void Cheque) <span class="text-danger">*</span></label>
                                                    <input type="file" name="formValidationBankingDetails"
                                                        id="formValidationBankingDetails" class="form-control"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        placeholder="Upload your ID proof" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationMasterBusinessLicense">Master Business License
                                                        (MBL) Or Article Of Incorporation <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" name="formValidationMasterBusinessLicense"
                                                        id="formValidationMasterBusinessLicense" class="form-control"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        placeholder="Upload your ID proof" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationAdditionalDocuments">Additional Documents
                                                        (Optional) <span class="text-danger">*</span></label>
                                                    <input type="file" name="formValidationAdditionalDocuments"
                                                        id="formValidationAdditionalDocuments" class="form-control"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        placeholder="Upload your ID proof" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationFrontPictureofBusinessPlace">Front Picture of
                                                        Business Place (Optional) <span
                                                            class="text-danger">*</span></label>
                                                    <input type="file" name="formValidationFrontPictureofBusinessPlace"
                                                        id="formValidationFrontPictureofBusinessPlace"
                                                        class="form-control" accept=".pdf,.jpg,.jpeg,.png"
                                                        placeholder="Upload your ID proof" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Sales Associates / Reps Involved -->
                                        <div id="sales-associates-reps-involved-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Documents</h6>
                                                <small>Enter Your Documents</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationLeadGen">Lead Gen: <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationLeadGen"
                                                        id="formValidationLeadGen" class="form-control"
                                                        placeholder="Your answer" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label" for="formValidationCloserOrTL">Closer Or TL:
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="formValidationCloserOrTL"
                                                        id="formValidationCloserOrTL" class="form-control"
                                                        accept=".pdf,.jpg,.jpeg,.png" placeholder="Your answer" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-next">
                                                        <span
                                                            class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                                        <i class="ti ti-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Future Upsell Opportunity -->
                                        <div id="future-upsell-opportunity-validation" class="content">
                                            <div class="content-header mb-3">
                                                <h6 class="mb-0">Future Upsell Opportunity</h6>
                                                <small>Enter Your Future Upsell Opportunity</small>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationAreyouExpectinganyUpsellfromthisMerchant">Are
                                                        you Expecting any Upsell from this Merchant? <span
                                                            class="text-danger">*</span></label>
                                                    <div class="col mt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio"
                                                                name="formValidationAreyouExpectinganyUpsellfromthisMerchant"
                                                                id="formValidationAreyouExpectinganyUpsellfromthisMerchant-yes"
                                                                class="form-check-input" value="Yes" />
                                                            <label class="form-check-label"
                                                                for="AreyouExpectinganyUpsellfromthisMerchant-yes">Yes
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio"
                                                                name="formValidationAreyouExpectinganyUpsellfromthisMerchant"
                                                                id="formValidationAreyouExpectinganyUpsellfromthisMerchant-no"
                                                                class="form-check-input" value="No" />
                                                            <label class="form-check-label"
                                                                for="AreyouExpectinganyUpsellfromthisMerchant-no">No
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label"
                                                        for="formValidationPleaseSpecifytheBusinessNameorBusinessType">Please
                                                        Specify the Business Name (DBA) or Business Type <span
                                                            class="text-danger">*</span></label>
                                                    <p>For e.g ( XYZ - Grocery Store)</p>
                                                    <input type="text"
                                                        name="formValidationPleaseSpecifytheBusinessNameorBusinessType"
                                                        id="formValidationPleaseSpecifytheBusinessNameorBusinessType"
                                                        class="form-control" placeholder="Your answer"
                                                        aria-label="Your answer" />
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-label-secondary btn-prev">
                                                        <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                                                        <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                                    </button>
                                                    <button class="btn btn-success btn-next btn-submit"
                                                        type="submit">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /Validation Wizard -->
                    </div>
                </div>
                <!-- / Content -->
    
                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-fluid">
                        <div
                            class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
                            <div>
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <!-- / Layout page -->
    </div>
    <!-- Layout wrapper -->
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('admin/assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('admin/assets/js/main.js') }}"></script>

    <!-- Page JS -->

    {{-- <script src="{{asset('admin/assets/js/form-wizard-numbered.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/js/form-wizard-validation.js?v=0.0.013') }}"></script>
</body>

</html>

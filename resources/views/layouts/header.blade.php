<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description"
    content="Stack admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
<meta name="keywords"
    content="admin template, stack admin template, dashboard template, flat admin template, responsive admin template, web app">
<meta name="author" content="PIXINVENT">
<title>Dashboard eCommerce - Stack Responsive Bootstrap 4 Admin Template</title>
<link rel="apple-touch-icon" href="{{ asset("app-assets/images/ico/apple-icon-120.png") }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset("app-assets/images/ico/favicon.ico") }}">
<link
    href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i"
    rel="stylesheet">

<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/vendors.min.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/tables/datatable/datatables.min.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/extensions/unslider.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/weather-icons/climacons.min.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/fonts/meteocons/style.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/charts/morris.css") }}">
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/bootstrap.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/bootstrap-extended.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/colors.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/components.css") }}">
<!-- END: Theme CSS-->

<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/core/menu/menu-types/vertical-menu.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/core/colors/palette-gradient.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/fonts/simple-line-icons/style.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/pages/timeline.css") }}">
<!-- END: Page CSS-->

<!-- BEGIN: Custom CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset("assets/css/style.css") }}">
<!-- END: Custom CSS-->
<style>
    div.dt-buttons {
        position: relative;
        float: right;
        margin-bottom: 10px;
    }

    .btn-datatabel {
        margin: 2px;
    }

    .table tbody tr:last-child {
        border-color: #dee2e6;
    }

    .filter {
        height: 45px !important;
    }

    @media screen and (min-width: 768px) {

        /* ketika screen width lebih dari 768px */
        .dt-buttons {
            margin-top: -12px;
            margin-right: -10px;
        }

        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: -8px;
            white-space: nowrap;
            text-align: right;
            margin-right: -10px;
        }
    }

    #input-search {
        height: 45px;
    }

    /* Gaya umum untuk pagination */
    .dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }

    /* Gaya untuk setiap elemen pagination (angka dan tombol) */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 0 4px;
        padding: 5px 10px;
        cursor: pointer;
    }

    /* Gaya untuk angka aktif */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #007bff;
        color: #fff;
    }

    /* Gaya untuk tombol "Next" dan "Previous" */
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #eee;
    }

    /* Gaya untuk angka tidak dapat diklik pada pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        pointer-events: none;
        color: #ccc;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 0px solid #ccc;
        border-radius: 10px;
        margin: 3px;
        padding: 0px;
        cursor: pointer;
    }

    div.dataTables_wrapper {
        padding-top: 10px;
        width: 100%;
    }
</style>

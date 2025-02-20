<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="author" content="AFINDO">
<title>{{ $title ? $title.' - ' : '' }}Afindo Template</title>
<link rel="apple-touch-icon" href="{{ asset("app-assets/images/ico/logo.svg") }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset("app-assets/images/ico/logo.svg") }}">
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
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/forms/icheck/icheck.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/forms/icheck/custom.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/forms/toggle/switchery.min.css") }}">
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
<link rel="stylesheet" type="text/css"
    href="{{ asset("app-assets/vendors/css/pickers/daterange/daterangepicker.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/plugins/pickers/daterange/daterange.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/forms/selects/select2.min.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/pickers/pickadate/pickadate.css") }}">
<link rel="stylesheet" type="text/css" href="{{ asset("app-assets/vendors/css/extensions/sweetalert2.min.css") }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
    integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('app-assets/css/afindo.css') }}">
<!-- END: Page CSS-->

<!-- BEGIN: Custom CSS-->
{{-- <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/style.css") }}"> --}}
{{-- <link rel="stylesheet" type="text/css" href="{{ asset("app-assets/css/afindo.css") }}"> --}}
<!-- END: Custom CSS-->
<style>
    .brand-logo-mini {
        display: none;
    }

    .menu-collapsed .brand-logo {
        display: none;
    }

    .menu-collapsed .brand-logo-mini {
        display: block;
    }
</style>
{{-- @vite(["resources/css/app.css", "resources/js/app.js"]) --}}

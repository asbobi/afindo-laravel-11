<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

    <head>
        @include("layouts.header")
        @stack("styles")
        @yield("styles")
    </head>
    <!-- END: Head-->

    <!-- BEGIN: Body-->

    <body class="vertical-layout vertical-menu 2-columns fixed-navbar" data-open="click" data-menu="vertical-menu"
        data-col="2-columns">

        <!-- BEGIN: Header-->
        @include("layouts.toolbar")
        <!-- END: Header-->

        <!-- BEGIN: Main Menu-->
        @include("layouts.menu")
        <!-- END: Main Menu-->

        <!-- BEGIN: Content-->
        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="content-wrapper">
                <div class="content-header row">
                </div>
                <div class="content-body">
                    @yield("content")
                </div>
            </div>
        </div>
        <!-- END: Content-->
        @include("layouts.scripts")
        @yield("scripts")
        @stack("scripts")
    </body>

</html>

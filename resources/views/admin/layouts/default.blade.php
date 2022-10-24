<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        @include('admin.includes.head')
        @yield('head')
    </head>
    <body>

    {{header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0")}}
    {{header("Expires: Sat, 26 Jul 1997 05:00:00 GMT")}}

    @yield('css')
        <div id="wrapper">
            @include('admin.includes.navmenu')

            @yield('content')

        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
        <script src="{!! asset("public/vendor/admin/jquery/jquery.min.js") !!}"></script>
        {{--Photo Gallary --}}
        <script src="{!! asset("public/js/admin/photo-gallery.js") !!}"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="{!! asset("public/vendor/admin/bootstrap/js/bootstrap.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin//bootstrap/js/bootstrap-tagsinput.js/") !!}"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="{!! asset("public/vendor/admin/metisMenu/metisMenu.min.js") !!}"></script>

        <!-- DataTables JavaScript -->
        <script src="{!! asset("public/vendor/admin/datatables/js/jquery.dataTables.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin/datatables-plugins/dataTables.bootstrap.min.js") !!}"></script>
        <script src="{!! asset("public/vendor/admin/datatables-responsive/dataTables.responsive.js") !!}"></script>

        <!-- Custom Theme JavaScript -->
        <script src="{!! asset("public/dist/js/sb-admin-2.js") !!}"></script>

        <!-- Admin JavaScript -->
        <script src="{!! asset("public//js/admin/admin.js") !!}"></script>

        <!-- Bootstrap Dialog -->
        <script src="{{ asset('public/dist/js/bootstrap-dialog.js')}}"></script>
        <script src="{{ asset('public//dist/js/bootstrap-dialog.min.js')}}"></script>



        <!--MultiSelect-->
        <script src="{{ asset('public/vendor/admin/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
        <!-- Summer note -->
        <script src="{!! asset("public/dist/js/summernote.js") !!}"></script>
        <script>
            $(document).ready(function() {
                $('#summernote').summernote({
                    height: 250,
                });
            });
        </script>

        <!--Sweet Alert-->
        <script src="{{ asset('public/dist/js/sweetalert.min.js')}}"></script>
        <!-- Include this after the sweet alert js file -->
        @include('sweet::alert')

        @if (Session::has('sweet_alert.alert'))
            <script>
                swal({!! Session::get('sweet_alert.alert') !!});
            </script>
        @endif

        @yield('script')

            </body>
        </html>

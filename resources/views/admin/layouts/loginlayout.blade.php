<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        @include('admin.includes.head')
        @yield('head')
    </head>
    <body>
        <div id="wrapper">

            @yield('content')

        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
{{--
        <script src=""></script>
--}}

        <!-- Bootstrap Core JavaScript -->
        <script src="{!! asset("/public/vendor/admin/jquery/jquery.min.js") !!}"></script>
        <script src="{!! asset("/public/vendor/admin/bootstrap/js/bootstrap.min.js") !!}"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="{!! asset("/public/vendor/admin/metisMenu/metisMenu.min.js") !!}"></script>

        <!-- DataTables JavaScript -->
        <script src="{!! asset("/public/vendor/admin/datatables/js/jquery.dataTables.min.js") !!}"></script>
        <script src="{!! asset("/public/vendor/admin/datatables-plugins/dataTables.bootstrap.min.js") !!}"></script>
        <script src="{!! asset("/public/vendor/admin/datatables-responsive/dataTables.responsive.js") !!}"></script>

        @yield('script')

        <!-- Custom Theme JavaScript -->
        <script src="{!! asset("/public/dist/js/sb-admin-2.js") !!}"></script>



        <!-- Admin JavaScript -->
        <script src="{!! asset("/public//js/admin/admin.js") !!}"></script>

    </body>
</html>

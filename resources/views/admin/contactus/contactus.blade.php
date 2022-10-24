@extends('admin.layouts.default')
@section('title', ucfirst($module))

@section('content')
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">{{ ucfirst($module) }}</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <div>
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ ucfirst($module) }}
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover data-table"
                               id="data-table">
                            <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Message</th>
                                <th>Date-time</th>
                                {{--<th>Action</th>--}}
                            </tr>
                            </thead>
                        </table>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /.row -->



    <!-- /#page-wrapper -->
@endsection

@section('script')
    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
        $(function () {
            columns = [
                {data: 'user_name', name: 'user_name', width: '30%'},
                {data: 'message', name: 'message', width: '40%'},
                {data: 'created_at', name: 'created_at', width: '30%'},
                /*{data: 'action', name: 'action', orderable: false, searchable: false}*/];
            initDataTable('{{ url('/admin/contact-us-data') }}', columns);
        });
    </script>
@endsection


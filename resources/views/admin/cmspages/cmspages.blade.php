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
    <?php /*
    <div>
        <a href="{{ url('/admin/cms-page/create')}}" class="btn btn-primary pull-right" style="margin-bottom: 10px;margin-left: 5px">Add {{ ucfirst($module) }}</a>
    </div> <?php */ ?>
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
                                <th>Type</th>
                                <th>Updated Date</th>
                                <th>Action</th>
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
                {data: 'type', name: 'type'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}];
            initDataTable('{{ url('/admin/cmspages-data') }}', columns);
        });
    </script>
@endsection
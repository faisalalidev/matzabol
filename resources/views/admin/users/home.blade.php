@extends('admin.layouts.default')

@section('content')
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Users List</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Users List
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            @if($users)
                                @foreach($users as $user)
                                    <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe">
                                        <img alt="{{$user->fname}} {{$user->lname}}" src="http://lorempixel.com/400/200/" class="img-responsive">
                                    </div>
                                @endforeach
                                        {{ $users->links() }}
                            @endif

                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
@endsection

@section('script')
<script>
$(function() {
	columns = [{data: 'fname', name: 'fname'},
				{data: 'lname', name: 'lname'},
				{data: 'email', name: 'email'},
				{data: 'company_name', name: 'company_name'},
				{data: 'date_added', name: 'date_added'},
				{data: 'action', name: 'action', orderable: false, searchable: false}];
				
	initDataTable('/admin/user-data',columns);
});
</script>
@endsection

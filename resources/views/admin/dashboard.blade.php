@extends('admin.layouts.default')
@section('head')
        <!-- Morris Charts CSS -->
<link href="{!! URL::asset('vendor/admin/morrisjs/morris.css') !!}" rel="stylesheet">
@endsection

@section('content')
        <!-- Page Content -->
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">

        @foreach(session('modules') as $key=>$value)
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-{{ $value['bg'] }}">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa {{ $value['icon'] }} fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">{{ $value['count'] }}</div>
                                <div>{{ str_replace(["_","-"]," ",ucwords($key)) }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ url('admin/'.$key) }}">
                        <div class="panel-footer">
                            <span class="pull-left">View Details</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
    <!-- /.row -->

</div>
<!-- /#page-wrapper -->
@endsection

@section('script')
        <!-- Morris Charts JavaScript -->
<script src="{!! asset("vendor/admin/raphael/raphael.min.js") !!}"></script>
<script src="{!! asset("vendor/admin/morrisjs/morris.min.js") !!}"></script>
<script src="{!! asset("data/morris-data.js") !!}"></script>
@endsection

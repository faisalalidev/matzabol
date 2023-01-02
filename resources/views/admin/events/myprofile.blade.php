@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
        <!-- Page Content -->

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">{{ ucfirst($module) }}</h1>
        </div>

    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-defaul" >
                <div class="panel-heading">
                    {{-- {{ ucfirst($module) }}--}}
                </div>
                <div class="panel-body">
                    <div class="row">
                        @if (Session::has('success'))
                            <div class="alert alert-success">{!! Session::get('success') !!}</div>
                        @endif
                        @if (Session::has('failure'))
                            <div class="alert alert-danger">{!! Session::get('failure') !!}</div>
                        @endif

                        <div class="col-md-8">
                            <h4>
                                <i class="fa fa-user"></i> {{$user->name}}</h4>

                            <p>
                                <i class="fa fa-envelope"></i>     {{$user->email}}
                                <br />


                            <!-- Split button -->
                            <div class="btn-group">
                        <a  href="{{url('admin/myprofile/changepassword')}}">
                                <button type="button" class="btn btn-primary">
                                    Change Password</button>
                        </a>
                            </div>
                        </div>
                            <a  href="{{url('admin/myprofile/edit')}}">
                        <div class=".col-md-4" style="text-align: right">
                            <button class="btn btn-outline btn-default" style="margin-right: 5px;">Edit Profile</button>
                        </div>
                            </a>


                       </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            @if (Session::has('flash_notification.message'))
                <div class="alert alert-{{ Session::get('flash_notification.level') }}">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                    {{ Session::get('flash_notification.message') }}
                </div>
            @endif
        </div>

    </div>
</div>
<!-- /#page-wrapper -->




@endsection


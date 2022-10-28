@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
    <!-- Page Content -->

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Add {{ ucfirst($module) }}</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-defaul">
                    <div class="panel-heading">
                        Add {{ ucfirst($module) }}
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">

                                {!! Form::open(['url' => 'admin/interests/store', 'method' => 'post' ,'files' => true]) !!}
                                {!! Form::token() !!}

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    {!! Form::label('name', 'Name') !!}
                                    {!! Form::text('name',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('name') )
                                        <p class="help-block">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                                    {!! Form::label('icon', 'Icon') !!}
                                    {!! Form::text('icon',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('icon') )
                                        <p class="help-block">{{ $errors->first('icon') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                    <a href="{{ url('admin/interests') }}" class="btn btn-default">Cancel</a>
                                </div>

                                {!! Form::close() !!}

                            </div>
                            <!-- /.col-lg-6 (nested) -->
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


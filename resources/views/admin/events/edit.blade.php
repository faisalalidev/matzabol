@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
        <!-- Page Content -->
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> {{ ucfirst($module) }}</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    {{ ucfirst($module) }}
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6">

                            {!! Form::open(['url' => 'admin/myprofile/update/','method' => 'post']) !!}

                            {!!   Form::token() !!}

                            <div class="form-group">
                                {!! Form::label('name', 'Name') !!}
                                {!! Form::text('name', $admin->name, ['class' => 'form-control', 'required' => 'required']) !!}
                                @if ( $errors->has('title') )
                                    <p class="help-block">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <div class="form-group">
                                {!! Form::label('email', 'Email') !!}
                                {!! Form::text('email', $admin->email, ['class' => 'form-control']) !!}
                                @if ( $errors->has('title') )
                                    <p class="help-block">{{ $errors->first('email') }}</p>
                                @endif
                            </div>


                            <div class="form-group">
                                {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                <a href="{{ url('admin/myprofile') }}" class="btn btn-default">Cancel</a>

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
</div>
<!-- /#page-wrapper -->

@endsection

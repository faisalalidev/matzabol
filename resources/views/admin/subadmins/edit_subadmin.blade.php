@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Edit {{ ucfirst($module) }}</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Edit {{ ucfirst($module) }}
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">

                                {!! Form::open(['url' => 'admin/sub-admin/update/'.$subadmin->id, 'method' => 'post','files' => true]) !!}
                                {!! Form::token() !!}

                                <div class="form-group{{ $errors->has('full_name') ? ' has-error' : '' }}">
                                    {!! Form::label('full_name', 'Name') !!}
                                    {!! Form::text('full_name', $subadmin->full_name, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('full_name') )
                                        <p class="help-block">{{ $errors->first('full_name') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {!! Form::label('email', 'Email') !!}
                                    {!! Form::email('email', $subadmin->email, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('email') )
                                        <p class="help-block">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    {!! Form::label('password', 'Password') !!}
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                    @if ( $errors->has('password') )
                                        <p class="help-block">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    {!! Form::label('password_confirmation', 'Confirm Password:') !!}
                                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                    @if ( $errors->has('password_confirmation') )
                                        <p class="help-block">{{ $errors->first('password_confirmation') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                    {!! Form::label('phone_number', 'Phone Number') !!}
                                    {!! Form::text('phone_number', $subadmin->phone_number, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('phone_number') )
                                        <p class="help-block">{{ $errors->first('phone_number') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::label('status', 'Status') !!}
                                    {!! Form::select('status', ['Deactive', 'Active'],$subadmin->status,['id' => 'status','class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('status') )
                                        <p class="help-block">{{ $errors->first('status') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                    <a href="{{ url('admin/sub-admin') }}" class="btn btn-default">Cancel</a>
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


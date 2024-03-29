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

                                {!! Form::open(['url' => 'admin/events/store', 'method' => 'post' ,'files' => true]) !!}
                                {!! Form::token() !!}

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    {!! Form::label('name', 'Name') !!}
                                    {!! Form::text('name',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('name') )
                                        <p class="help-block">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('day') ? ' has-error' : '' }}">
                                    {!! Form::label('day', 'Day') !!}
                                    {!! Form::text('day',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('day') )
                                        <p class="help-block">{{ $errors->first('day') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('time') ? ' has-error' : '' }}">
                                    {!! Form::label('time', 'Time') !!}
                                    {!! Form::text('time',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('time') )
                                        <p class="help-block">{{ $errors->first('time') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                    {!! Form::label('date', 'Date') !!}
                                    {!! Form::text('date',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('date') )
                                        <p class="help-block">{{ $errors->first('date') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    {!! Form::label('description', 'Description') !!}
                                    {!! Form::text('description',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('description') )
                                        <p class="help-block">{{ $errors->first('description') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                    {!! Form::label('type', 'Type') !!}
                                    {!! Form::select('type', [
                                        'online' =>'Online',
                                        'inperson'=> 'In Person'],
                                        null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('type') )
                                        <p class="help-block">{{ $errors->first('type') }}</p>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                        <label for="address_address">Address</label>
                                        <input type="text" id="address-input" name="address_address" class="form-control map-input">
                                        <input type="hidden" name="address_latitude" id="address-latitude" value="0" />
                                        <input type="hidden" name="address_longitude" id="address-longitude" value="0" />
                                    <div id="address-map-container" style="width:100%;height:400px; ">
                                        <div style="width: 100%; height: 100%" id="address-map"></div>
                                    </div>
                                </div>


                                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                    {!! Form::label('image', 'Image') !!}
                                    {!! Form::file('image',null, ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('image') )
                                        <p class="help-block">{{ $errors->first('image') }}</p>
                                    @endif
                                </div>




                                {{--                                <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">--}}
{{--                                    {!! Form::label('icon', 'Icon') !!}--}}
{{--                                    {!! Form::text('icon',null, ['class' => 'form-control', 'required' => 'required']) !!}--}}
{{--                                    @if ( $errors->has('icon') )--}}
{{--                                        <p class="help-block">{{ $errors->first('icon') }}</p>--}}
{{--                                    @endif--}}
{{--                                </div>--}}

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                    <a href="{{ url('admin/events') }}" class="btn btn-default">Cancel</a>
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


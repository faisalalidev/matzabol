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
                        <div class="col-lg-12">

                            {!! Form::open(['url' => 'admin/cms-page/store', 'files' => true]) !!}

                            {!!   Form::token() !!}

                            <div class="form-group" >
                                {!! Form::label('type', 'Page Type') !!}
                                {!! Form::select('type', [
                                'terms' =>'Terms',
                                'policies'=> 'Policies'],null,['id' => 'type','class' => 'form-control', 'required' => 'required']) !!}
                                @if ( $errors->has('type') )
                                    <p class="help-block">{{ $errors->first('type') }}</p>
                                @endif
                            </div>

                            <div class="form-group" >
                                {!! Form::label('body', 'Type Newsletter Content') !!}
                                {!! Form::textarea('body',null,['id' => 'summernote', 'class'=>'form-control summernote', 'rows' => '60', 'cols' => '60']) !!}
                                @if ( $errors->has('body') )
                                    <p class="help-block">{{ $errors->first('body') }}</p>
                                @endif
                            </div>


                            <div class="form-group">
                                {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                <a href="{{ url('admin/cms-page') }}" class="btn btn-default">Cancel</a>
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


<div class="modal fade" id="myMapModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div id="map-canvas" class="span12"></div>
                        <div id="iframe_map" class="span12"></div>

                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.map modal -->


@endsection

@section('script')

@endsection

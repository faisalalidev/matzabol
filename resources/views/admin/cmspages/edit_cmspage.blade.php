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

                            {!! Form::open(['url' => 'admin/cms-page/update/'.$cms->id,'method' => 'post']) !!}

                            {!!   Form::token() !!}

                            <div class="form-group">
                                {!! Form::label('type', ucwords($cms->type)) !!}
                           </div>

                            <div class="form-group" >
                                {!! Form::label('body', 'Type Newsletter Content') !!}
                                {!! Form::textarea('body',$cms->body,['id' => 'summernote', 'class'=>'form-control summernote', 'rows' => '15', 'cols' => '60']) !!}
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
</div>
<!-- /#page-wrapper -->


@endsection


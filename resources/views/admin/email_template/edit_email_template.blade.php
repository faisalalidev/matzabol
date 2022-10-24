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
                            <div class="col-lg-12">

                            {!! Form::open(['url' => 'admin/email-template/update/'.$template->id, 'method' => 'post','files' => true]) !!}
                            {!! Form::token() !!}

                            <!-- Params Field -->
                                <div class="form-group col-sm-8">
                                    {!! Form::label('params', 'Select Param:') !!}
                                    {!! Form::select('params',[0=>'Please Select Param'] + \App\Models\EmailTemplate::$EMAIL_PARAMS, null, ['class' => 'form-control select2', 'id'=>'myInput']) !!}
                                </div>

                                <div class="form-group col-sm-4">
                                    <div class="tooltip">
                                        <button type="button" class="btn btn-info custom-btn-top-padding"
                                                onclick="copyToClipboard()"
                                                onmouseout="showTooltip()"
                                                style="width: 200px">
                                            <i class="fa fa-copy"></i>
                                            <span class="tooltiptext" id="myTooltip">Copy to clipboard</span> Copy Param
                                        </button>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <!-- Email Receiver Field -->
                                <div class="form-group col-sm-8">
                                    {!! Form::label('users', 'Select Users:') !!}
                                    {!! Form::select('users[]', $users, null, ['class' => 'form-control', 'multiple'=>'multiple','id'=>"dates-field1"]) !!}
                                </div>

                                <!-- Key Field -->
                                {{--<div class="form-group col-sm-8">
                                    {!! Form::label('key', 'Select Template Name:') !!}
                                    {!! Form::select('key',[0=>'Please Select Template Name'] + \App\Models\EmailTemplate::$TEMPLATE_NAME, null, ['class' => 'form-control select2']) !!}
                                </div>--}}

                                <div class="clearfix"></div>

                                <!-- Html Body Field -->
                                <div class="form-group col-sm-12">
                                    {!! Form::label('html_body', 'Html Body:') !!}
                                    {{--<textarea id="editor1" name="editor1" rows="10" cols="80" style="visibility: hidden; display: none;"></textarea>--}}
                                    {!! Form::textarea('html_body2', null, ['class' => 'form-control ckeditor-css','id'=>'editor1','name'=>'editor1']) !!}
                                    @if(isset($template->html_body))
                                        {!! Form::hidden('html_body', $template->html_body, ['class' => 'form-control']) !!}
                                    @else
                                        {!! Form::hidden('html_body', null, ['class' => 'form-control']) !!}
                                    @endif
                                </div>

                                <!-- Text Body Field -->
                                <div class="form-group col-sm-12">
                                    {!! Form::label('text_body', 'Text Body:') !!}
                                    @if(isset($template->text_body))
                                        {!! Form::textarea('text_body', $template->text_body, ['class' => 'form-control']) !!}
                                    @else
                                        {!! Form::textarea('text_body', null, ['class' => 'form-control']) !!}
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary', 'id'=>'submitDemo']) !!}
                                    <a href="{{ url('admin/email-template') }}" class="btn btn-default">Cancel</a>
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

@section('css')
    <style>
        .ckeditor-css {
            visibility: hidden;
            display: none;
        }

        .custom-btn-top-padding {
            margin-top: 22px;
        }

        .tooltip {
            position: relative;
            display: inline-block;
            opacity: 1;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 80%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #555 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
    </style>
@endsection

@section('script')

    <!-- CK Editor -->
    <script src="https://adminlte.io/themes/AdminLTE/bower_components/ckeditor/ckeditor.js"></script>

    <script type="text/javascript">
        $(function () {
            CKEDITOR.replace('editor1');
            var html_body = $('#html_body').val();
            if (html_body) {
                CKEDITOR.instances.editor1.setData(html_body);
            }
        });

        $('#submitDemo').on("click", function () {
            $('#html_body').val(CKEDITOR.instances.editor1.getData());
        });

        function copyToClipboard() {
            var copyText = $("#myInput option:selected").val();

            var dummy = document.createElement("input");
            document.body.appendChild(dummy);
            dummy.value = copyText;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);

            var tooltip = document.getElementById("myTooltip");
            tooltip.innerHTML = "Copied: " + dummy.value;
        }

        function showTooltip() {
            var tooltip = document.getElementById("myTooltip");
            tooltip.innerHTML = "Copy to clipboard";
        }

        $(document).ready(function () {

            $('#dates-field1').multiselect({
                includeSelectAllOption: true,
                maxHeight: 300,
                buttonWidth: '100%',
                //numberDisplayed: 5,
                nSelectedText: 'selected',
                nonSelectedText: 'None selected',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });

        });

    </script>
@endsection
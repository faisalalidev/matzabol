@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
    <!-- Page Content -->

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Send {{ ucfirst($module) }}</h1>
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

                                {!! Form::open(['url' => 'admin/notifications/store', 'method' => 'post' ,'files' => true]) !!}
                                {!! Form::token() !!}


                                <div class="form-group">
                                    {!! Form::label('device_type', 'Platforms') !!}
                                    {{--{!! Form::select('device_type', ['android'=>'Android', 'ios'=>'IOS','all' => 'All'],null, ['id' => "device-type", 'class' => 'form-control', 'required' => 'required']) !!}--}}
                                    {!! Form::select('device_type', ['ios'=>'IOS'],null, ['id' => "device-type", 'class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('device_type') )
                                        <p class="help-block">{{ $errors->first('device_type') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::label('users', 'Users') !!}
                                    <select class="form-control" name="users[]" id="dates-field2" multiple="multiple"
                                            required='required'>
                                        @foreach($users as $user)
                                            <option value="{{json_encode($user)}}">{{$user->full_name}}</option>
                                        @endforeach
                                    </select>
                                    @if ( $errors->has('users') )
                                        <p class="help-block">{{ $errors->first('users') }}</p>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                    {!! Form::label('message', 'Message') !!}
                                    {!! Form::textarea('message',null,['class'=>'form-control','maxlength'=> 80, 'required' => 'required', 'rows' => 5, 'cols' => 50]) !!}
                                    @if ( $errors->has('message') )
                                        <p class="help-block">{{ $errors->first('message') }}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                    <a href="{{ url('admin/') }}" class="btn btn-default">Cancel</a>
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


@section('script')

    <script type="text/javascript">

        $(document).ready(function () {

            $('#dates-field2').multiselect({
                includeSelectAllOption: true,
                maxHeight: 300,
                buttonWidth: '100%',
                numberDisplayed: 5,
                nSelectedText: 'selected',
                nonSelectedText: 'None selected',
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true
            });

        });

        //$('#device-type').change(function () {
        //$.get("{{ url('admin/notifications/users-type')}}/" + $(this).val(),
        $.get("{{ url('admin/notifications/users-type')}}/ios",
            function (data) {
                var html = '';
                $.each(data, function (index, element) {
                    html += "<option value='" + JSON.stringify(element) + "'>" + element.full_name + "</option>";
                });
                $('#dates-field2').html(html);
                $('#dates-field2').multiselect('rebuild');
            });
        //});

    </script>

@endsection

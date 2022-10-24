@extends('admin.layouts.default')
@section('title', ucfirst($module))
@section('content')
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">{{ ucfirst($module) }}</h1>
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
                    <!-- /.panel-heading -->
                    <div class="panel-body">

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="setting">
                                <h4>Change Admin Password</h4>
                                {{-- Settings form Start--}}
                                {!! Form::open(['url' => 'admin/settings/update/','method' => 'post', ]) !!}
                                {!!   Form::token() !!}

                                <div class="form-group">
                                    {!! Form::label('old', 'Current Password') !!}
                                    {!! Form::password('old', ['class' => 'form-control', 'required' => 'required']) !!}
                                    @if ( $errors->has('old') )
                                        <p class="help-block">{{ $errors->first('old') }}</p>
                                    @endif
                                    @if (Session::has('flash_notification.message'))
                                        <p class="help-block">{{ Session::get('flash_notification.message') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password', 'New Password') !!}
                                    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required' ]) !!}
                                    @if ( $errors->has('password') )
                                        <p class="help-block">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password_confirmation', 'Re-type Password') !!}
                                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required' => 'required']) !!}

                                </div>

                                <div class="form-group">
                                    {!! Form::submit('Save',['class'=>'btn btn-primary']) !!}
                                    <a href="{{ url('admin/') }}" class="btn btn-default">Cancel</a>

                                </div>

                                {!! Form::close() !!}
                                {{--Settings form end--}}

                            </div>

                        </div>
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

@section('script')
    <script>
        /*   $(function () {
         columns = [
         {data: 'queue', name: 'queue'},
         {data: 'attempts', name: 'attempts'},
         {data: 'reserved_at', name: 'reserved_at', searchable: false},
         {data: 'available_at', name: 'available_at', searchable: false},
         {data: 'created_at', name: 'created_at', searchable: false},
         {data: 'action', name: 'action', orderable: false, searchable: false}];
         initDataTable('{{ url('/admin/jobs-data') }}', columns);
         });

         $("#upload-img-log").change(function(){
         readURLLogo(this);
         });
         $("#upload-img-fav").change(function(){
         readURLFav(this);
         });

         function readURLLogo(input){
         var reader = new FileReader();

         reader.onload = function (e) {
         $('#upload-img-logo').attr('src', e.target.result);
         }
         reader.readAsDataURL(input.files[0]);

         }
         function readURLFav(input){
         var reader = new FileReader();

         reader.onload = function (e) {
         $('#upload-img-favicon').attr('src', e.target.result);
         }
         reader.readAsDataURL(input.files[0]);

         }
         */
    </script>

@endsection
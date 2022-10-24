@extends('admin.layouts.loginlayout')

@section('script')
    <script src="{!! asset("public/vendor/admin/jquery/jquery.min.js") !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone-with-data.js"></script>

    <script>
        var timezone = moment.tz.guess();
        $('#timezone').val(timezone);
    </script>
@endsection

@section('content')

<div class="container">
    <div class="row">

        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <img src="{!! asset('storage/app/logo.png') !!}" class="center-block" width="50">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <form  role="form" method="POST" action="{{ url('admin/login') }}">
                        {{ csrf_field() }}
                        <fieldset>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                <input id="email" placeholder="E-mail" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>


                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                <input id="password" placeholder="Password"  type="password" class="form-control" name="password" required>


                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <!--                                <a href="index.html" class="btn btn-lg btn-success btn-block">Login</a>-->
                            <button type="submit" class="btn btn-lg btn-block" style="background-color: #cc33cc; color: white">
                                Login
                            </button>
                        </fieldset>
                        <input type="hidden" name="timezone" id="timezone">
                    </form>
                </div>
            </div>
        </div>
    </div>
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

@endsection


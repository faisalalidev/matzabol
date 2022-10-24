@extends('admin.layouts.default')
@section('title','User Detalis')

@section('content')
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>{{ ucfirst($module) }} Details</h3>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">

                        <div class="chat-panel panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-comments fa-fw"></i> Chat History
                            </div>
                            <!-- /.panel-heading -->

                            <div class="panel-body">
                                <ul class="chat">
                                    @foreach($chat as $ch)
                                        @if($ch->sender_id == $chat[0]->sender_id)
                                            <li class="right clearfix">
                                                <span class="chat-img pull-right">
                                                    <img src="{{asset('storage/app/'. $ch->sender_image )}}"
                                                         alt="User Avatar"
                                                         class="img-circle" height="45px" width="50px">
                                                </span>
                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <small class=" text-muted">
                                                            <i class="fa fa-clock-o fa-fw"></i> {{$ch->created_at}}
                                                        </small>
                                                        <strong class="pull-right primary-font">{{$ch->username}}</strong>
                                                    </div>
                                                    <p class="pull-right">
                                                        {{$ch->message}}
                                                    </p>
                                                </div>
                                            </li>
                                        @else
                                            <li class="left clearfix">
                                                <span class="chat-img pull-left">
                                                    <img src="{{asset('storage/app/'. $ch->sender_image )}}"
                                                         alt="User Avatar"
                                                         class="img-circle" height="45px" width="50px">
                                                </span>
                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <strong class="primary-font">{{$ch->username}}</strong>
                                                        <small class="pull-right text-muted">
                                                            <i class="fa fa-clock-o fa-fw"></i> {{$ch->created_at}}
                                                        </small>
                                                    </div>
                                                    <p>
                                                        {{$ch->message}}
                                                    </p>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <!-- /.panel-body -->
                            <div class="panel-footer">
                                <div class="input-group">

                                </div>
                            </div>
                            <!-- /.panel-footer -->
                        </div>

                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body" style="text-align: center!important;">
                    <img class="img-responsive" style="margin: auto; padding: 10px; " src=""/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /#page-wrapper -->
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('img').on('click', function () {
                var image = $(this).attr('src');
                $('#myModal').on('show.bs.modal', function () {
                    $(".img-responsive").attr("src", image);
                });
            });
        });
    </script>
@endsection
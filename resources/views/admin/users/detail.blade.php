@extends('admin.layouts.default')
@section('title','User Detalis')

@section('content')
    <!-- Page Content -->

    <div id="page-wrapper">
        <div class="row">

            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>{{ ucfirst($module) }} Details
                            <small>
                                <i class="fa fa-flag" aria-hidden="true" style="color: red"> Both user reported each
                                    other
                                    |</i>
                                <i class="fa fa-flag" aria-hidden="true" style="color: orange"> Current user reported to
                                    this user |</i>
                                <i class="fa fa-flag" aria-hidden="true" style="color: blue"> Current user reported by
                                    this user</i>
                            </small>
                        </h3>
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> User Basic
                                        Detail</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">

                                            <tbody>

                                            @if($user['info'])
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{ $user['info']->id }}</td>

                                                </tr>
                                                <tr>
                                                    <th>Name</th>
                                                    <td>{{ $user['info']->full_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Phone Number</th>
                                                    <td>{{ $user['info']->phone_number}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Gender</th>
                                                    <td>{{ $user['info']->gender}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Date of birth</th>
                                                    <td>{{ $user['info']->dob }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td> @if($user['info']->status == 1)
                                                            <span class="label label-success">Active</span>
                                                        @else
                                                            <span class="label label-danger">Disabled</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#user_info" data-toggle="tab">{{ ucfirst($module) }}
                                    Information</a></li>
                            <li><a href="#likes" data-toggle="tab">{{ ucfirst($module) }} Like Profiles</a></li>
                            <li><a href="#boosts" data-toggle="tab">{{ ucfirst($module) }} Boost Profiles</a></li>
                            <li><a href="#dislikes" data-toggle="tab">{{ ucfirst($module) }} Dislike Profiles</a></li>
                            <li><a href="#reportedby" data-toggle="tab">{{ ucfirst($module) }} Reported By</a></li>
                            <li><a href="#threads" data-toggle="tab">{{ ucfirst($module) }} Chats</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="user_info">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Information</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @if($user['info'])
                                            <tr>
                                                <th>ID</th>
                                                <td>{{ $user['info']->id }}</td>

                                            </tr>
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $user['info']->full_name }}</td>

                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td>{{ $user['info']->country }}</td>
                                            </tr>
                                            <tr>
                                                <th>Phone Number</th>
                                                <td>{{ $user['info']->phone_number}}</td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td>{{ $user['info']->gender}}</td>
                                            </tr>
                                            <tr>
                                                <th>Date of birth</th>
                                                <td>{{ $user['info']->dob }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td> @if($user['info']->status == 1)
                                                        <span class="label label-success">Active</span>
                                                    @else
                                                        <span class="label label-danger">Disabled</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Match Notification</th>
                                                <td>
                                                    @if($user['info']->notify_new_matches == '1')
                                                        <span class="label label-success">Enabled</span>
                                                    @else
                                                        <span class="label label-warning">Disabled</span>
                                                    @endif
                                                </td>

                                            </tr>
                                            <tr>
                                                <th>Boost Notification</th>
                                                <td>
                                                    @if($user['info']->notify_booster == '1')
                                                        <span class="label label-success">Enabled</span>
                                                    @else
                                                        <span class="label label-warning">Disabled</span>
                                                    @endif
                                                </td>

                                            </tr>
                                            <tr>
                                                <th>Message Notification</th>
                                                <td>
                                                    @if($user['info']->notify_message == '1')
                                                        <span class="label label-success">Enabled</span>
                                                    @else
                                                        <span class="label label-warning">Disabled</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Boost</th>
                                                <td>{{ $user['info']->total_boost}}</td>
                                            </tr>
                                            <tr>
                                                <th>My Status</th>
                                                {{--<td style="word-break: break-all">{{ $user['info']->my_status}}</td>--}}
                                                <td style="word-break: break-all">{!! json_decode('"'.$user['info']->my_status.'"') !!}</td>
                                            </tr>
                                            <tr>
                                                <th>Marital status</th>
                                                <td>{{ $user['info']->marrital_status}}</td>
                                            </tr>
                                            <tr>
                                                <th>Religion Cast</th>
                                                <td>{{ $user['info']->religion_cast}}</td>
                                            </tr>
                                            <tr>
                                                <th>Height</th>
                                                <td>{{ $user['info']->height}}</td>
                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td>{{ $user['info']->country}}</td>
                                            </tr>
                                            <tr>
                                                <th>Ethnicity</th>
                                                <td>{{ $user['info']->ethnicity}}</td>
                                            </tr>
                                            <tr>
                                                <th>Nationality</th>
                                                <td>{{ $user['info']->nationality}}</td>
                                            </tr>
                                            <tr>
                                                <th>Language</th>
                                                <td>{{ $user['info']->language}}</td>
                                            </tr>

                                            <tr>
                                                <th>Profession</th>
                                                <td>{{ $user['info']->profession}}</td>
                                            </tr>

                                            <tr>
                                                <th>Education</th>
                                                <td>{{ $user['info']->education}}</td>
                                            </tr>

                                            <tr>
                                                <th>Univeristy Name</th>
                                                <td>{{ $user['info']->education_detail}}</td>
                                            </tr>

                                            <tr>
                                                <th>Religion</th>
                                                <td>{{ $user['info']->religion}}</td>
                                            </tr>

                                            <tr>
                                                <th>About Me</th>
                                                {{--<td style="word-break: break-all">{{ $user['info']->about_me}}</td>--}}
                                                <td style="word-break: break-all">{!! json_decode('"'.$user['info']->about_me.'"') !!}</td>
                                            </tr>

                                            <tr>
                                                <th>Images</th>
                                                <td style="height: 50px !important;">

                                                    <div id="myCarousel" class="carousel slide myCarousel2"
                                                         data-ride="carousel">
                                                        <!-- Indicators -->
                                                        <ol class="carousel-indicators">
                                                            @foreach($user['info']->userImage as $key )
                                                                <li data-target=".myCarousel2"
                                                                    data-slide-to="{{$loop->index}}"></li>
                                                            @endforeach
                                                        </ol>

                                                        <!-- Wrapper for slides -->
                                                        <div class="carousel-inner" style="height: 250px;">
                                                            @foreach($user['info']->userImage as $key => $value)
                                                                <div class="item
                                                @if($loop->first)
                                                                        active
@endif">
                                                                    <img src="{{asset('storage/app/'. $value->image )}}"
                                                                         id="{{$loop->iteration}}" data-toggle="modal"
                                                                         data-target="#myModal"
                                                                         style="margin: auto; width: 30%; padding: 10px; ">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Left and right controls -->
                                                        <a class="left carousel-control" href=".myCarousel2"
                                                           data-slide="prev">
                                                            <span class="glyphicon glyphicon-chevron-left"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="right carousel-control" href=".myCarousel2"
                                                           data-slide="next">
                                                            <span class="glyphicon glyphicon-chevron-right"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>


                                                </td>
                                            </tr>
                                        @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="likes">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Likes</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <th>Profile Name</th>
                                        <th>Like Date</th>
                                        @foreach($user['like'] as $like)
                                            <tr>
                                                <td>
                                                    <a href="{{url('admin/users/detail/' . $like->id)}}"
                                                       target="_blank">
                                                        @if($like->reported_to == 1 && $like->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: red"
                                                               title="Reported Each Other"></i>
                                                        @elseif($like->reported_to == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: orange"
                                                               title="Reported To"></i>
                                                        @elseif($like->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: blue"
                                                               title="Reported from"></i>
                                                        @endif
                                                        {{ $like->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $like->created_at->timezone(session('timezone')) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="boosts">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Boost</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <th>Profile Name</th>
                                        <th>Boost Date</th>
                                        @foreach($user['boost'] as $boost)
                                            <tr>
                                                <td>
                                                    <a href="{{url('admin/users/detail/' . $boost->id)}}"
                                                       target="_blank">
                                                        @if($boost->reported_to == 1 && $boost->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: red"
                                                               title="Reported Each Other"></i>
                                                        @elseif($boost->reported_to == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: orange"
                                                               title="Reported To"></i>
                                                        @elseif($boost->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: blue"
                                                               title="Reported from"></i>
                                                        @endif
                                                        {{ $boost->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $boost->created_at->timezone(session('timezone')) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="dislikes">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Dislikes</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <th>Profile Name</th>
                                        <th>Dislike Date</th>
                                        @foreach($user['dislike'] as $dislike)
                                            <tr>
                                                <td>
                                                    <a href="{{url('admin/users/detail/' . $dislike->id)}}"
                                                       target="_blank">
                                                        @if($dislike->reported_to == 1 && $dislike->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: red"
                                                               title="Reported Each Other"></i>
                                                        @elseif($dislike->reported_to == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: orange"
                                                               title="Reported To"></i>
                                                        @elseif($dislike->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: blue"
                                                               title="Reported from"></i>
                                                        @endif
                                                        {{ $dislike->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $dislike->created_at->timezone(session('timezone')) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="reportedby">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Reported By</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <th>Profile Name</th>
                                        <th>Type</th>
                                        <th>Message</th>
                                        <th>Reported Date</th>
                                        @foreach($user['reportedby'] as $reportedby)
                                            <tr>
                                                <td>
                                                    <a href="{{url('admin/users/detail/' . $reportedby->id)}}"
                                                       target="_blank">
                                                        @if($reportedby->reported_to == 1 && $reportedby->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: red"
                                                               title="Reported Each Other"></i>
                                                        @elseif($reportedby->reported_to == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: orange"
                                                               title="Reported To"></i>
                                                        @elseif($reportedby->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: blue"
                                                               title="Reported from"></i>
                                                        @endif
                                                        {{ $reportedby->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ str_replace('_',' ',$reportedby->type) }}
                                                </td>
                                                <td>
                                                    {{ $reportedby->message }}
                                                </td>
                                                <td>
                                                    {{ $reportedby->created_at->timezone(session('timezone')) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="threads">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Chats</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <th>Profile Name</th>
                                        <th>Thread Created Date</th>
                                        <th>View Chat History</th>
                                        @foreach($user['threads'] as $thread)
                                            <tr>
                                                <td>
                                                    <a href="{{url('admin/users/detail/' . $thread->id)}}"
                                                       target="_blank">
                                                        @if($thread->reported_to == 1 && $thread->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: red"
                                                               title="Reported Each Other"></i>
                                                        @elseif($thread->reported_to == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: orange"
                                                               title="Reported To"></i>
                                                        @elseif($thread->reported_by == 1)
                                                            <i class="fa fa-flag" aria-hidden="true"
                                                               style="color: blue"
                                                               title="Reported from"></i>
                                                        @endif
                                                        {{ $thread->full_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $thread->created_at->timezone(session('timezone')) }}
                                                </td>
                                                <td>
                                                    <a href="{{url('admin/users/chathistory/' . $thread->thread_id)}}"
                                                       target="_blank"> View Conversation</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <a href="{{ URL::previous() }}">
                                <button class="btn btn-primary">Back</button>
                            </a>
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
                    {{--<img class="img-responsive" style="margin: auto; padding: 10px; " src=""/>--}}

                    <div id="myCarousel" class="carousel slide myCarousel" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            @foreach($user['info']->userImage as $key )
                                <li data-target=".myCarousel" data-slide-to="{{$loop->index}}"></li>
                            @endforeach
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" style="height: 400px;">
                            @foreach($user['info']->userImage as $key => $value)
                                <div class="item @if($loop->first) active @endif">
                                    <img class="itemimage" src="{{asset('storage/app/'. $value->image )}}"
                                         id="{{$loop->iteration}}" data-toggle="modal"
                                         data-target="#myModal"
                                         style="margin: auto; width: 100%; padding: 10px; ">
                                </div>
                            @endforeach
                        </div>

                        <!-- Left and right controls -->
                        <a class="left carousel-control" href=".myCarousel" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href=".myCarousel" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

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
                    $('.itemimage').each(function () {
                        var itemimage = $(this).attr('src');
                        if (image == itemimage) {
                            $(this).parent().addClass('active');
                        } else {
                            $(this).parent().removeClass('active');
                        }
                    });
                });
            });
        });
    </script>
@endsection
@extends('admin.layouts.default')

@section('content')
    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3>{{ ucfirst($module) }} Details</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="col-sm-6" colspan="2">{{ ucfirst($module) }} Information</th>

                                </tr>
                                </thead>
                                <tbody>
                                {{--  <tr>
                                      <th>Id</th>
                                      <td>{{ $event->id }}</td>
                                  </tr>--}}
                                <tr>
                                    <th>CMS Type</th>
                                    <td>{{ $cms->type}}</td>
                                </tr>
                                <tr>
                                    <th>Page Content</th>
                                    <td>{!! $cms->body !!} </td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $cms->updated_at->timezone(session('timezone')) }}</td>
                                </tr>

                                </tbody>
                            </table>
                            <a href="{{url('admin/cms-page')}}">
                                <button class="btn btn-primary">Back</button>
                            </a>

                        </div>

                    </div>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>

    <!-- /#page-wrapper -->
@endsection


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
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $subadmin->full_name }}</td>
                                </tr>

                                <tr>
                                    <th>Email</th>
                                    <td>{{ $subadmin->email }}</td>
                                </tr>

                                <tr>
                                    <th>Phone #</th>
                                    <td>{{ $subadmin->phone_number }}</td>
                                </tr>

                                <tr>
                                    <th>Status</th>
                                    <td> @if($subadmin->status == 1)
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Deactive</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $subadmin->created_at->timezone(session('timezone')) }}</td>
                                </tr>

                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $subadmin->updated_at->timezone(session('timezone')) }}</td>
                                </tr>

                                </tbody>
                            </table>
                            <a href="{{url('admin/sub-admin')}}">
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


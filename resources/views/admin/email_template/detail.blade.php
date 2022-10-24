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
                                    <th>Template Name</th>
                                    <td>{{ $template->key }}</td>
                                </tr>

                                <tr>
                                    <th>Html Body</th>
                                    <td>{!! $template->html_body !!}</td>
                                </tr>

                                <tr>
                                    <th>Text Body</th>
                                    <td>{{ $template->text_body }}</td>
                                </tr>

                                <tr>
                                    <th>Created On</th>
                                    <td>{{ $template->created_at }}</td>
                                </tr>

                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $template->updated_at }}</td>
                                </tr>

                                </tbody>
                            </table>
                            <a href="{{url('admin/email-template')}}">
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


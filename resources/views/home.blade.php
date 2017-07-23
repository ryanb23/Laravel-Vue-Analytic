@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Main Page</h2></div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('rule.create') }}"><button type="button" class="btn btn-primary pull-right">Create New Experiment</button></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table exp_table">
                                <thead>
                                <tr>
                                    <th>Exp ID</th>
                                    <th>Exp Name</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($experiments as $row)
                                        <tr class="action">
                                            <td>{{ $row['id'] }}</td>
                                            <td>{{ $row['name'] }}</td>
                                            <td>{{ $row['created_at']->format('M d Y')}}</td>
                                            @if (empty($row['deleted_at']))
                                            <td>
                                                <a href="{{ route('rule.edit', [$row['id']]) }}"><button type="button" class="btn btn-sm btn-primary ">Edit</button></a>
                                                <a href="{{ route('rule.activate',[$row['id']]) }}"><button type="button" class="btn btn-sm btn-danger ">Deactivate</button></a>
                                            </td>
                                            @else
                                            <td>
                                                <a href="{{ route('rule.deactivate',[$row['id']]) }}"><button type="button" class="btn btn-sm btn-success">Re-activate</button></a>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                              </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master')

@section('title', 'Users')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('styles')
@parent
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Hoverable Table rows -->
    <div class="card">
        <div class="card-header header-elements">
            <h5 class="card-title mb-0">Users Management</h5>
            <div class="card-header-elements ms-auto py-0">
                <a class="btn btn-primary waves-effect waves-light mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Create New User</a>
            </div>
        </div>


        <div class="card-body">
            @session('success')
            <div class="alert alert-success" role="alert">
                {{ $value }}
            </div>
            @endsession
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($data as $key => $user)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $v)
                                <label class="badge bg-success">{{ $v }}</label>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}"><i class="fa-solid fa-list"></i> Show</a>
                                <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    {!! $data->links('pagination::bootstrap-5') !!}
                </table>
            </div>
        </div>

    </div>
    <!--/ Hoverable Table rows -->
</div>
@endsection
@extends('layouts.master')

@section('title', 'Permissions')

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
            <h5 class="card-title mb-0">Permission Management</h5>
            <div class="card-header-elements ms-auto py-0">
                <a class="btn btn-primary waves-effect waves-light mb-2" href="{{ route('permissions.create') }}"><i class="fa fa-plus"></i> Create New Permission</a>
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
                            <th width="100px">No</th>
                            <th>Name</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($permissions as $key => $permission)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('permissions.show',$permission->id) }}"><i class="fa-solid fa-list"></i> Show</a>
                                @can('permission-edit')
                                <a class="btn btn-primary btn-sm" href="{{ route('permissions.edit',$permission->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                @endcan

                                @can('permission-delete')
                                <form method="POST" action="{{ route('permissions.destroy', $permission->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    {!! $permissions->links('pagination::bootstrap-5') !!}
                </table>
            </div>
        </div>

    </div>
    <!--/ Hoverable Table rows -->
</div>
@endsection
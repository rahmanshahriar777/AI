@extends('layouts.master')

@section('title', 'Edit Role')

@section('scripts')
@parent
<script src="{{asset('assets/vendor/libs/cleave-zen/cleave-zen.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('styles')
@parent
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Multi Column with Form Separator -->
    <div class="row mb-6 gy-6">
        <!-- Form Separator -->
        <div class="col-xxl">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Edit Role</h5>
                    <div class="card-header-elements ms-auto py-0">
                        <a class="btn btn-dark waves-effect waves-light mb-2" href="{{ route('roles.index') }}">Back</a>
                    </div>
                </div>
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form class="card-body" method="POST" action="{{ route('roles.update', $role->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-name">Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="multicol-name" name="name" class="form-control" placeholder="admin" value="{{ $role->name }}" />
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-sm-3 col-form-label" for="multicol-permission">Permission</label>
                        <div class="col-sm-9">
                            @foreach($permission as $value)
                            <label><input type="checkbox" name="permission[{{$value->id}}]" value="{{$value->id}}" class="name" {{ in_array($value->id, $rolePermissions) ? 'checked' : ''}}>
                                {{ $value->name }}</label>
                            <br />
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6">
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary me-4">Update</button>
                                <button type="reset" class="btn btn-label-secondary">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>




</div>
@endsection
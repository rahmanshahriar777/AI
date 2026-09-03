@extends('layouts.master')

@section('title', 'Update Job Type')

@section('scripts')
@parent

@endsection

@section('styles')
@parent

@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Multi Column with Form Separator -->
    <div class="row mb-6 gy-6">
        <!-- Form Separator -->
        <div class="col-xxl">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Update job type</h5>
                    <div class="card-header-elements ms-auto py-0">
                        <a class="btn btn-dark waves-effect waves-light mb-2" href="{{ route('jobtypes.index') }}">Back</a>
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
                <form class="card-body" method="POST" action="{{ route('jobtypes.update', $jobType->id) }}">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col mb-4">
                            <label for="typeName" class="form-label">Type Name</label>
                            <input
                                type="text"
                                id="typeName"
                                name="typeName"
                                class="form-control"
                                placeholder="Enter Name"
                                value="{{ $jobType->name }}" required 
                                @if($jobType->hasUsed == 1) disabled @endif/>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col mb-0">
                            <label for="jobTypeDetails" class="form-label">Details</label>
                            <textarea
                                id="jobTypeDetails"
                                name="jobTypeDetails"
                                class="form-control"
                                placeholder="Enter Details"
                                rows="3">{{ $jobType->description }}</textarea>
                        </div>
                    </div>
                    <div class="pt-6">
                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary me-4">Submit</button>
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
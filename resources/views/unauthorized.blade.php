@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title text-danger mb-4">
                        <i class="fas fa-exclamation-triangle"></i> Unauthorized Access
                    </h3>
                    <p class="card-text mb-4">
                        You don't have permission to access this page.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', __('messages.login').' - '.__('messages.app_name'))

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="d-flex justify-content-end mb-2">
            <div class="btn-group btn-group-sm">
                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline-secondary {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'id') }}" class="btn btn-outline-secondary {{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title text-center mb-4">🎬 {{ __('messages.app_name') }}</h4>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.username') }}</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">{{ __('messages.login_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.bmc-app')

@section('page-title','Profile')

@section('content')
  <div class="card" style="max-width:640px;margin-bottom:1.2rem;">
    @include('profile.partials.update-profile-information-form')
  </div>

  <div class="card" style="max-width:640px;">
    @include('profile.partials.delete-user-form')
  </div>
@endsection
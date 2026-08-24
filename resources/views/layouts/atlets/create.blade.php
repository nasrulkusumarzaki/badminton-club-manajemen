@extends('layouts.bmc-app')

@section('page-title','Tambah Atlet')

@section('content')
  <div class="card" style="max-width:640px;">
    <h3 style="margin:0 0 1.2rem;font-family:'Plus Jakarta Sans',sans-serif;">Tambah Atlet</h3>

    <form method="POST" action="{{ route('atlets.store') }}" enctype="multipart/form-data">
      @include('layouts.atlets._form')
    </form>
  </div>
@endsection
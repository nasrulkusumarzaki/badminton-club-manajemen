@extends('layouts.bmc-app')

@section('page-title','Edit Atlet')

@section('content')
  <div class="card">
    <h3 style="margin-top:0">Edit Atlet — {{ $atlet->nama }}</h3>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('atlets.update', $atlet) }}" enctype="multipart/form-data" class="space-y-5">
                    @method('PUT')
                    @include('layouts.atlets._form')
                </form>
            </div>
        </div>
    </div>
@endsection
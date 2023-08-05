@extends('layouts.app')

@section('content')
    <h1>Edit {{ $modelName }}</h1>

    <form action="{{ route('{{ $modelName }}.update', $model->id) }}" method="post">
        @csrf
        @method('PUT')
        {{ $inputs }}
        <button type="submit">Update</button>
    </form>
@endsection

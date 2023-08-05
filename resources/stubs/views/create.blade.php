@extends('layouts.app')

@section('content')
    <h1>Create {{ $modelName }}</h1>

    <form action="{{ route('{{ $modelName }}.store') }}" method="post">
        @csrf
        {{ $inputs }}
        <button type="submit">Create</button>
    </form>
@endsection

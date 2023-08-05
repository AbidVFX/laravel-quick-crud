@extends('layouts.app')

@section('content')
    <h1>{{ $modelName }} List</h1>

    <a href="{{ route('{{ $modelName }}.create') }}">Create New {{ $modelName }}</a>

    <table class="table table-striped">
        <thead>
            <tr>
                @foreach ($inputs as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($models as $model)
                <tr>
                    @foreach ($inputs as $input)
                        <td>{{ $model->$input }}</td>
                    @endforeach
                    <td><a href="{{ route(strtolower($modelName) . '.edit', $model->id) }}">Edit</a></td>
                    <td><button>Delete</button></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

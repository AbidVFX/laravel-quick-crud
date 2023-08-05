<!DOCTYPE html>
<html>
<head>
    <title>Generated Files</title>
</head>
<body>
    <h1>Generated Files for {{ $moduleName }}</h1>
    <ul>
        @forelse($files as $file)
            <li>{{ $file->getPathname() }}</li>
        @empty
            <li>No files generated for {{ $moduleName }}</li>
        @endforelse
    </ul>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Generated Files</title>
</head>
<body>
    <h1>Generated Files for {{ $moduleName }}</h1>
    <ul>
        @forelse($files as $file)
            <li>{{ $file->getPathname() }}</li>
        @empty
            <li>No files generated for {{ $moduleName }}</li>
        @endforelse
    </ul>
</body>
</html>

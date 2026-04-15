<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>
</head>
<body>
    <h1>Edit Role</h1>

    @if ($errors->any())
        <div>
            <p>Please fix the following errors:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}" required>
        </div>
        <div>
            <button type="submit">Update</button>
        </div>
    </form>

    <p><a href="{{ route('admin.roles.index') }}">Back to Roles</a></p>
</body>
</html>

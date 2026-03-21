@extends('layout')

@section('title', 'Users List')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Users List</h2>
            <a href="{{ route('users.create') }}" class="btn btn-success">+ Create User</a>
        </div>
        
        @if($users->count())
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('users.show', $user) }}" class="btn btn-primary">View</a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination">
                {{ $users->links() }}
            </div>
        @else
            <p style="text-align: center; padding: 20px; color: #7f8c8d;">No users found. <a href="{{ route('users.create') }}">Create one</a></p>
        @endif
    </div>
@endsection

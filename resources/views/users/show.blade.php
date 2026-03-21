@extends('layout')

@section('title', 'User Details')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>{{ $user->name }}</h2>
            <div>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
        
        <table style="width: 100%; max-width: 500px;">
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">ID:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->id }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">Name:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">Email:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->email }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">Email Verified At:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i') : 'Not verified' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">Created At:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 10px; border-bottom: 1px solid #ddd;">Updated At:</td>
                <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{ $user->updated_at->format('Y-m-d H:i') }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 20px;">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>
@endsection

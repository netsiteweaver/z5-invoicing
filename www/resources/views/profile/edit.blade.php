@extends('layouts.app')

@section('title', 'Your Profile')

@section('content')
<div class="space-y-6">
  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Profile Information</h3>
      <p class="mt-1 text-sm text-gray-500">Update your account's profile information and email address.</p>

      <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
          <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="name" />
          @error('name')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="username" />
          @error('email')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex items-center justify-end">
          <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Update Password</h3>
      <p class="mt-1 text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>

      <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
          <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
          <input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="current-password" />
          @error('updatePassword.current_password')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
          <input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="new-password" />
          @error('updatePassword.password')
          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end">
          <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">Save</button>
        </div>
      </form>
    </div>
  </div>

  
</div>
@endsection

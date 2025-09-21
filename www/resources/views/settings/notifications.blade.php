@extends('layouts.app')

@section('title', 'Notifications')



@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Recipients</h3>
        <p class="text-sm text-gray-500 mb-4">Emails are sent only for events that have at least one recipient configured.</p>

        @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // Group events by entity (prefix before dot)
            $grouped = [];
            foreach ($events as $key => $label) {
                [$entity, $action] = explode('.', $key, 2);
                $grouped[$entity][$action] = [ 'key' => $key, 'label' => $label ];
            }
            // Ensure consistent action order
            $actionOrder = ['created', 'updated', 'deleted'];
        @endphp

        <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
            @csrf

            <div class="space-y-6">
                @foreach($grouped as $entity => $actions)
                    <div>
                        <h2 class="font-bold text-gray-800 mb-2 uppercase zaza">{{ $entity }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach($actionOrder as $act)
                                @php
                                    $ek = $actions[$act]['key'] ?? null;
                                    $el = ucfirst($act);
                                    $existing = $ek ? $rules->get($ek) : null;
                                    $selected = $existing?->recipients?->pluck('recipient_value')->map(fn($v) => (int)$v)->toArray() ?? [];
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-medium text-gray-600">{{ $el }}</label>
                                        @if($ek)
                                            @php $isOn = count(old("recipients.$ek", $selected)) > 0; @endphp
                                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $isOn ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $isOn ? 'On' : 'Off' }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($ek)
                                        <select name="recipients[{{ $ek }}][]" multiple size="5" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" {{ in_array($u->id, old("recipients.$ek", $selected)) ? 'selected' : '' }}>
                                                    {{ $u->name }} ({{ $u->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <div class="text-xs text-gray-400">N/A</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple users.</p>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection



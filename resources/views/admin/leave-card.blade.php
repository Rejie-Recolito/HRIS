@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight">Leave Card for {{ $employee->lastname }}, {{ $employee->firstname }}</h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium">Employee</h3>
                    <p>{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }}</p>
                    <p class="text-sm text-gray-500">Department: {{ $employee->department }} | Job: {{ $employee->job_title }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="p-4 bg-white rounded shadow">
                        <h4 class="font-semibold">Totals</h4>
                        <p>Vacation: <strong>{{ $totals['vacation'] }}</strong></p>
                        <p>Sick: <strong>{{ $totals['sick'] }}</strong></p>
                    </div>

                    <div class="p-4 bg-white rounded shadow">
                        <h4 class="font-semibold">Assign Leave Credit</h4>
                        <form method="POST" action="{{ route('employees.leave_card.store', $employee->id) }}">
                            @csrf
                            <div class="mb-2">
                                <label for="type" class="block text-sm">Type</label>
                                <select name="type" id="type" class="w-full border rounded px-2 py-1">
                                    <option value="vacation">Vacation</option>
                                    <option value="sick">Sick</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="amount" class="block text-sm">Amount</label>
                                <input type="number" name="amount" id="amount" class="w-full border rounded px-2 py-1" required>
                            </div>
                            <div class="mb-2">
                                <label for="notes" class="block text-sm">Notes (optional)</label>
                                <textarea name="notes" id="notes" class="w-full border rounded px-2 py-1"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded shadow p-4">
                    <h4 class="font-semibold mb-3">History</h4>
                    @if($credits->isEmpty())
                        <p class="text-gray-500">No leave credits assigned yet.</p>
                    @else
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Date</th>
                                    <th class="text-left">Type</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-left">Assigned By</th>
                                    <th class="text-left">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($credits as $c)
                                    <tr class="border-t">
                                        <td class="py-2">{{ $c->created_at->format('Y-m-d') }}</td>
                                        <td class="py-2">{{ ucfirst($c->type) }}</td>
                                        <td class="py-2 text-right">{{ $c->amount }}</td>
                                        <td class="py-2">{{ optional($c->assigner)->name ?? 'System' }}</td>
                                        <td class="py-2">{{ $c->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

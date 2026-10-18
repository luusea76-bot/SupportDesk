@extends('layouts.app')

@section('title', "Edit {$customer->name}")
@section('breadcrumb', "Customers / {$customer->name} / Edit")

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Customer Profile</h1>
            <p class="text-sm text-slate-500 mt-0.5">Update contact details and company information</p>
        </div>
        <a href="{{ route('customers.show', $customer) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to customer
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Customer Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="company_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">{{ old('notes', $customer->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

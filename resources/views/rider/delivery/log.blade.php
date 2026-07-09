<x-rider-layout>
    <x-slot name="header">Log Delivery Attempt</x-slot>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-5">
        <div class="font-mono text-sm font-bold text-slate-900">{{ $parcel->tracking_code }}</div>
        <div class="text-base font-semibold text-slate-800 mt-1">{{ $parcel->receiver_name }}</div>
        <div class="text-sm text-slate-500">{{ $parcel->receiver_phone }}</div>
        <div class="text-sm text-slate-500 mt-1">{{ $parcel->receiver_address }}</div>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('rider.delivery.store', $parcel->parcel_id) }}"
          x-data="{ outcome: 'success' }" class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 space-y-5">
        @csrf

        <div class="space-y-3">
            <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3.5 cursor-pointer transition-colors"
                   :class="outcome === 'success' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200'">
                <input type="radio" name="outcome" value="success" x-model="outcome" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500" checked>
                <span class="text-base font-semibold text-slate-800">Delivered Successfully</span>
            </label>

            <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3.5 cursor-pointer transition-colors"
                   :class="outcome === 'failed' ? 'border-red-500 bg-red-50' : 'border-slate-200'">
                <input type="radio" name="outcome" value="failed" x-model="outcome" class="w-5 h-5 text-red-600 focus:ring-red-500">
                <span class="text-base font-semibold text-slate-800">Delivery Failed</span>
            </label>
        </div>

        <div x-show="outcome === 'failed'" x-cloak>
            <x-input-label value="Reason" />
            <textarea name="failure_reason" rows="3" maxlength="200"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-base text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                      placeholder="e.g. Receiver not available, wrong address, refused">{{ old('failure_reason') }}</textarea>
        </div>

        <button type="submit"
                class="block w-full text-center bg-indigo-600 text-white font-semibold py-3.5 rounded-xl text-base active:bg-indigo-700 transition-colors">
            Submit
        </button>

        <a href="{{ route('rider.dashboard') }}" class="block text-center text-sm text-slate-500 py-2">Cancel</a>
    </form>
</x-rider-layout>

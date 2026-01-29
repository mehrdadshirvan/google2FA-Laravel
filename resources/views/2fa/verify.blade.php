<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('2fa') }}
        </h2>
    </x-slot>
    <h3>تایید حساب</h3>
    <form method="POST" action="{{ route('2fa.active') }}">
        @csrf
        <input type="text" name="one_time_password" placeholder="کد ۶ رقمی">
        <button type="submit" class="bg-blue-400 border-blue-700 p-2 ">تایید</button>
    </form>

</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('2fa') }}
        </h2>
    </x-slot>
    <h3>فعال‌سازی Google Authenticator</h3>

    <p>این QR رو با Google Authenticator اسکن کن</p>

    <div>
        <img src="{!! $QR_Image['simple'] !!}"
             height="150px"
             width="150px"
             alt="">
    </div>

    <form method="POST" action="{{ route('2fa.store') }}">
        @csrf
        <input type="text" name="one_time_password" placeholder="کد ۶ رقمی">
        <button type="submit">تایید</button>
    </form>

</x-app-layout>

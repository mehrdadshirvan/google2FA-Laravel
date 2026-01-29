<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\CommonMark\Renderer\Inline\ImageRenderer;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Google2FAController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();

        session([
            'google2fa_secret' => $secretKey
        ]);

        // ساخت لینک otpauth:// برای اپلیکیشن Google Authenticator
        $otpAuthUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );

        // ساخت تصویر QR
        $QR_Image = [];
        $QR_Image['simple'] = 'data:image/svg+xml;base64,' . base64_encode(QrCode::format('svg')->size(1000)->generate($otpAuthUrl));



        return view('2fa.enable', compact('QR_Image', 'secretKey'));
    }

    public function store(Request $request)
    {
        $google2fa = new Google2FA();
        $secret = session('google2fa_secret');

        if ($google2fa->verifyKey($secret, $request->one_time_password)) {
            $user = auth()->user();

            $user->google2fa_secret = encrypt($secret);
            $user->google2fa_enabled = true;
            $user->save();

            session()->forget('google2fa_secret');

            return redirect('/dashboard')->with('success', 'احراز هویت دو مرحله‌ای فعال شد');
        }

        return back()->withErrors(['کد وارد شده اشتباه است']);
    }

    public function create()
    {
        return view('2fa.verify');
    }

    public function active2FA(Request $request)
    {

        if(!auth()->check()){
            return redirect('/login');
        }
        $auth = auth()->user();
        if($auth->google2fa_enabled){
            $google2fa = new Google2FA();
            if ($google2fa->verifyKey(decrypt($auth->google2fa_secret), $request->get('one_time_password'))) {
                session(['google2fa_passed' => true]);

                return redirect('/dashboard');
            }else{
                Auth::logout($auth);
            }
            return redirect('/login');
        }
        return redirect('/dashboard');
    }
}

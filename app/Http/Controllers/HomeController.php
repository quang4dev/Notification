<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SendPushNotification;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kutia\Larafirebase\Facades\Larafirebase;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('home');
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function updateToken(Request $request): JsonResponse
    {
        try{
            $request->user()->update(['fcm_token'=>$request->get('token')]);
            return response()->json([
                'success'=>true
            ]);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function notification(Request $request): RedirectResponse
    {
        $request->validate([
            'title'=>'required',
            'message'=>'required'
        ]);

        try{
            $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

            \Notification::send(null,new SendPushNotification($request->title,$request->message,$fcmTokens));

            /* or */

            //auth()->user()->notify(new SendPushNotification($title,$message,$fcmTokens));

            /* or */

            Larafirebase::withTitle($request->title)
                ->withBody($request->message)
                ->sendMessage($fcmTokens);

            return redirect()->back()->with('success','Notification Sent Successfully!!');

        }catch(\Exception $e){
            report($e);
            return redirect()->back()->with('error','Something goes wrong while sending notification.');
        }
    }
}

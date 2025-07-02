<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ZoomController extends Controller
{
    public function redirectToZoom()
    {
        $clientId = config('services.zoom.client_id');
        $redirectUri = route('zoom.callback');
        $url = "https://zoom.us/oauth/authorize?response_type=code&client_id={$clientId}&redirect_uri={$redirectUri}";

        return redirect($url);
    }

    public function handleZoomCallback(Request $request)
    {
        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('home')->with('error', 'Zoom authorization failed.');
        }

        $response = Http::asForm()->post('https://zoom.us/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => route('zoom.callback'),
            'client_id' => config('services.zoom.client_id'),
            'client_secret' => config('services.zoom.client_secret'),
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            // Store access_token and other data in the session or database
            Session::put('zoom_access_token', $data['access_token']);
            return redirect()->route('home')->with('success', 'Zoom authorization successful.');
        } else {
            return redirect()->route('home')->with('error', 'Failed to obtain Zoom access token.');
        }
    }
}

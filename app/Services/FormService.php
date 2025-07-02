<?php

namespace App\Services;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FormService
{
    /**
 * @param Request $request
 * @param string $service
 * @param int $option [1,2,3,4]
 * // 1 send via email
 * // 2 store in database
 * // 3 do both
 * // 4 debug print to screen
 *
 * @return void|string
 */
public static function sendRequest(Request $request, string $service, int $option = 1)
{
    $validatedData = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:50',
        'message' => 'required|string|max:1000',
    ]);

    if ($option == 1 || $option == 3) {
        self::sendEmail($request);
    }

    if ($option == 2 || $option == 3) {
        self::saveToDatabase($request, $service);
    }

    if ($option == 4) {
        self::printToScreen($request);
    }

    return '';
}


    public static function sendEmail(Request $request)
    {
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ];

        Mail::to(config('define.email_groups.support'))
            ->send(new ContactUs($details));
    }

    public static function saveToDatabase(Request $request, string $service)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'service' => $service,
        ];

        DB::table('contact_us')->insert($data);
    }

    public static function printToScreen(Request $request)
    {
        echo '<pre>';
        print_r($request->all());
        echo '</pre>';
    }
}

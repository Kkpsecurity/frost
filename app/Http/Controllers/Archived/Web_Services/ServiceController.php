<?php namespace App\Http\Controllers\Web\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FormService;
use App\Helpers\Helpers;

class ServiceController extends Controller
{


        public function initiate(Request $request, $service, $action) {

            if($service == "contact_us") {

                if(FormService::sendRequest($request, $action)) {
                    return back()->with('message', 'Success');
                }

                return back()->with('message', 'Some Error');
            }


        }

        public function getSiteConfigData() {
            return response()->json(Helpers::SiteConfigsKVP([
                'student_lesson_complete_seconds',
                'student_poll_seconds'
            ]));
        }


}

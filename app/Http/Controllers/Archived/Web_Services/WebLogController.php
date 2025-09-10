<?php 

namespace App\Http\Controllers\Web\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebLogController extends Controller
{
    public function log(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'error' => 'required|string',
            'errorInfo' => 'required|string',
            'device' => 'array',
            'device.model' => 'sometimes|string',
            'device.type' => 'sometimes|string',
            'device.vendor' => 'sometimes|string',
            'os' => 'array',
            'os.name' => 'sometimes|string',
            'os.version' => 'sometimes|string',
            'browser' => 'array',
            'browser.name' => 'sometimes|string',
            'browser.version' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $data = $request->only(['error', 'errorInfo', 'device', 'os', 'browser']);
        Log::info('WebLogController::log', $data);
        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;

class CenterController extends Controller
{
    use PageMetaDataTrait;

    public function dashboard()
    {
        $content = array_merge([], self::renderPageMeta('admin_center_dashboard'));
        return view('admin.center.dashboard', compact('content'));
    }

    public function server_logs(Request $request)
    {
        $logFile = storage_path("logs/laravel.log");

        $perPage = 40;
        $current_page = $request->get('page') ?: 1;
        $starting_line = ($current_page - 1) * $perPage;

        $logs = [];
        // if (File::exists($logFile)) {
        //     $totalLines = intval(shell_exec("wc -l < " . escapeshellarg($logFile)));
        //     $lines = collect(File::lines($logFile))->take(-200);

        //     $logPattern = '/\[(?P<timestamp>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?P<environment>\w+).(?P<log_level>\w+): (?P<message>.+?) {\"(?P<key>.+?)\":(?P<value>.+?),(\"exception\":(?P<exception>.+?))?(\n\[stacktrace\]\n(?P<stacktrace>.*?))?\n\n/';

        //     foreach ($lines as $line) {
        //         if (preg_match($logPattern, $line, $matches)) {
        //             $logEntry = [
        //                 'timestamp' => $matches['timestamp'],
        //                 'environment' => $matches['environment'],
        //                 'log_level' => $matches['log_level'],
        //                 'message' => $matches['message'],
        //                 'key' => $matches['key'],
        //                 'value' => $matches['value'],
        //                 'exception' => isset($matches['exception']) ? $matches['exception'] : null,
        //                 'stacktrace' => isset($matches['stacktrace']) ? $matches['stacktrace'] : null
        //             ];
        //             $logs[] = $logEntry;
        //         } else {
        //             $logs[] = ['raw' => $line]; // In case some lines don't match the pattern
        //         }
        //      }

        //     $logs = new LengthAwarePaginator($logs, $totalLines, $perPage, $current_page, [
        //         'path' => $request->url(),
        //         'query' => $request->query(),
        //     ]);
        // }

        //  $content = array_merge([], self::renderPageMeta('admin_center_server_logs'));
        // return view('admin.center.server_logs', compact('content', 'logs'));
    }

    public function impersonate($id)
    {
        // Get the user instance
        $userToImpersonate = User::find($id);

        // Current logged-in user will impersonate the given user
        auth()->user()->impersonate($userToImpersonate);

        return redirect()->route('classroom.dashboard');
    }
}
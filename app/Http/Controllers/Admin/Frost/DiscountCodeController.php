<?php

namespace App\Http\Controllers\Admin\Frost;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Http\Controllers\Controller;
use App\Traits\PageMetaDataTrait;
use App\Models\DiscountCode;
use App\Models\CourseAuth;
use App\Models\Course;
use App\Models\Order;
use App\Services\RCache;

class DiscountCodeController extends Controller
{
    use PageMetaDataTrait;

    public function index(Request $request): View
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $query = DiscountCode::with('Course')
            ->withCount(['Orders as times_used' => fn($q) => $q->whereNotNull('completed_at')]);

        // Filter: client
        if ($client = $request->get('client')) {
            $query->where('client', 'ilike', '%' . $client . '%');
        }

        // Filter: course
        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        // Filter: status
        switch ($request->get('status', 'all')) {
            case 'active':
                $query->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
                break;
            case 'expired':
                $query->where('expires_at', '<=', now());
                break;
            case 'unlimited':
                $query->whereNull('max_count');
                break;
        }

        $discountCodes = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $stats = [
            'total'    => DiscountCode::count(),
            'active'   => DiscountCode::where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
            'expired'  => DiscountCode::where('expires_at', '<=', now())->count(),
            'clients'  => DiscountCode::whereNotNull('client')->distinct('client')->count('client'),
        ];

        $courses = Course::where('is_active', true)->orderBy('title')->get(['id', 'title']);

        $clientList = DiscountCode::whereNotNull('client')
            ->distinct()
            ->orderBy('client')
            ->pluck('client');

        $content = array_merge([
            'discount_codes' => $discountCodes,
            'stats'          => $stats,
            'courses'        => $courses,
            'client_list'    => $clientList,
            'filters'        => [
                'client'    => $request->get('client'),
                'course_id' => $request->get('course_id'),
                'status'    => $request->get('status', 'all'),
            ],
        ], self::renderPageMeta('Client Discount Codes'));

        return view('admin.discount-codes.index', compact('content'));
    }

    public function create(): View
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $courses = Course::where('is_active', true)->orderBy('title')->get(['id', 'title']);

        $content = array_merge(
            ['courses' => $courses],
            self::renderPageMeta('Create Discount Code')
        );

        return view('admin.discount-codes.create', compact('content'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $validated = $request->validate([
            'code'       => 'required|string|max:32|unique:discount_codes,code',
            'client'     => 'nullable|string|max:32',
            'course_id'  => 'nullable|integer|exists:course_units,course_id',
            'set_price'  => 'nullable|numeric|min:0',
            'percent'    => 'nullable|integer|min:1|max:100',
            'max_count'  => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        // Only one discount type at a time
        if (!empty($validated['set_price']) && !empty($validated['percent'])) {
            return back()->withInput()->withErrors(['percent' => 'Choose either a fixed price or a percentage — not both.']);
        }

        DiscountCode::create($validated);

        // Reload cache so the new code is available immediately
        RCache::LoadModelCache(DiscountCode::class, true);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', "Discount code '{$validated['code']}' created.");
    }

    public function show(int $id): View
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $discountCode = DiscountCode::with('Course')->findOrFail($id);

        // Eager-load CourseAuth + StudentUnits so we can count lesson progress per student
        $orders = Order::with([
            'User',
            'Course',
            'CourseAuth.StudentUnits.StudentLessons',
        ])
            ->where('discount_code_id', $id)
            ->whereNotNull('completed_at')
            ->orderBy('created_at')
            ->get();

        // Pre-build total lessons per course (RCache — no extra DB hit)
        $totalLessonsMap = [];
        foreach ($orders as $order) {
            $courseId = $order->course_id;
            if ($courseId && !isset($totalLessonsMap[$courseId])) {
                $course = RCache::Courses($courseId);
                $totalLessonsMap[$courseId] = $course ? $course->GetLessons()->count() : 0;
            }
        }

        $content = array_merge([
            'discount_code'     => $discountCode,
            'orders'            => $orders,
            'total_lessons_map' => $totalLessonsMap,
        ], self::renderPageMeta('Discount Code: ' . $discountCode->code));

        return view('admin.discount-codes.show', compact('content'));
    }

    public function edit(int $id): View
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $discountCode = DiscountCode::findOrFail($id);
        $courses = Course::where('is_active', true)->orderBy('title')->get(['id', 'title']);

        $content = array_merge([
            'discount_code' => $discountCode,
            'courses'       => $courses,
        ], self::renderPageMeta('Edit Discount Code'));

        return view('admin.discount-codes.edit', compact('content'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $discountCode = DiscountCode::findOrFail($id);

        $validated = $request->validate([
            'code'       => "required|string|max:32|unique:discount_codes,code,{$id}",
            'client'     => 'nullable|string|max:32',
            'course_id'  => 'nullable|integer',
            'set_price'  => 'nullable|numeric|min:0',
            'percent'    => 'nullable|integer|min:1|max:100',
            'max_count'  => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        if (!empty($validated['set_price']) && !empty($validated['percent'])) {
            return back()->withInput()->withErrors(['percent' => 'Choose either a fixed price or a percentage — not both.']);
        }

        $discountCode->update($validated);

        // Force-reload the discount codes cache so the change takes effect immediately
        RCache::LoadModelCache(DiscountCode::class, true);

        return redirect()->route('admin.discount-codes.show', $id)
            ->with('success', "Discount code '{$discountCode->code}' updated.");
    }

    public function destroy(int $id): RedirectResponse
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $discountCode = DiscountCode::findOrFail($id);
        $code = $discountCode->code;

        // Block deletion if the code has been used
        $timesUsed = Order::where('discount_code_id', $id)->whereNotNull('completed_at')->count();
        if ($timesUsed > 0) {
            return back()->withErrors(['delete' => "Cannot delete '{$code}' — it has been used {$timesUsed} time(s). Expire it instead."]);
        }

        $discountCode->delete();
        RCache::LoadModelCache(DiscountCode::class, true);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', "Discount code '{$code}' deleted.");
    }

    public function exportCsv(int $id): StreamedResponse
    {
        if (!auth('admin')->check()) {
            abort(403);
        }

        $discountCode = DiscountCode::findOrFail($id);

        $orders = Order::with(['User', 'Course', 'CourseAuth.StudentUnits.StudentLessons'])
            ->where('discount_code_id', $id)
            ->whereNotNull('completed_at')
            ->orderBy('created_at')
            ->get();

        // Total lessons per course for the CSV
        $totalLessonsMap = [];
        foreach ($orders as $order) {
            $courseId = $order->course_id;
            if ($courseId && !isset($totalLessonsMap[$courseId])) {
                $course = RCache::Courses($courseId);
                $totalLessonsMap[$courseId] = $course ? $course->GetLessons()->count() : 0;
            }
        }

        $filename = 'discount-usage-' . strtolower($discountCode->code) . '.csv';

        return response()->streamDownload(function () use ($orders, $discountCode, $totalLessonsMap) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Code', 'Client', 'Student', 'Email', 'Course', 'Lessons', 'Enrolled At', 'Completed At']);
            foreach ($orders as $order) {
                $completedLessons = 0;
                if ($order->CourseAuth) {
                    foreach ($order->CourseAuth->StudentUnits as $su) {
                        $completedLessons += $su->StudentLessons->whereNotNull('completed_at')->count();
                    }
                }
                $totalLessons = $totalLessonsMap[$order->course_id] ?? 0;

                fputcsv($out, [
                    $discountCode->code,
                    $discountCode->client ?? '',
                    optional($order->User)->fullname() ?? '',
                    optional($order->User)->email ?? '',
                    optional($order->Course)->title ?? '',
                    $totalLessons > 0 ? "{$completedLessons} / {$totalLessons}" : $completedLessons,
                    Carbon::parse($order->created_at)->tz('America/New_York')->format('Y-m-d H:i'),
                    $order->CourseAuth?->completed_at
                        ? Carbon::parse($order->CourseAuth->completed_at)->tz('America/New_York')->format('Y-m-d H:i')
                        : '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}

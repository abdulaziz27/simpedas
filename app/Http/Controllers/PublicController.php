<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\School;
use App\Models\NonTeachingStaff;
use App\Models\Article;
use App\Models\Setting;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function index()
    {
        $stats = [];
        if (auth()->check()) {
            if (auth()->user()->hasRole('admin_dinas')) {
                $stats = [
                    'total_sekolah' => School::count(),
                    'total_guru' => Teacher::count(),
                    'total_siswa_aktif' => Student::where('status_siswa', 'aktif')->count(),
                    'total_siswa_tamat' => Student::where('status_siswa', 'tamat')->count(),
                    'total_non_teaching_staff' => NonTeachingStaff::count(),
                ];
            } elseif (auth()->user()->hasRole('admin_sekolah') && auth()->user()->school_id) {
                $schoolId = auth()->user()->school_id;
                $stats = [
                    'total_guru' => Teacher::where('school_id', $schoolId)->count(),
                    'total_siswa' => Student::where('sekolah_id', $schoolId)->count(),
                    'total_non_teaching_staff' => NonTeachingStaff::where('school_id', $schoolId)->count(),
                ];
            }
        }

        // Get latest 3 articles for homepage
        $latestArticles = Article::published()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get();

        // Get public stats for guest users
        $publicStats = [
            'total_sekolah' => School::count(),
            'total_guru' => Teacher::count(),
            'total_siswa_aktif' => Student::where('status_siswa', 'aktif')->count(),
        ];

        // Get settings for About Us and Contact
        $aboutSettings = Setting::whereIn('key', [
            'about_visi',
            'about_misi',
            'about_tugas_pokok',
            'about_fungsi',
        ])->get()->keyBy('key');

        $contactSettings = Setting::whereIn('key', [
            'contact_address',
            'contact_phone',
            'contact_email',
            'contact_hours',
            'contact_map_url'
        ])->get()->keyBy('key');

        // Get latest galleries (active only, limit 3 for homepage)
        $latestGalleries = Gallery::active()
            ->ordered()
            ->take(3)
            ->get();

        return view('public.index', compact('stats', 'latestArticles', 'publicStats', 'aboutSettings', 'contactSettings', 'latestGalleries'));
    }

    public function searchGuru(Request $request)
    {
        $q = $request->input('q');
        $teachers = Teacher::with('school')
            ->when($q, function ($query) use ($q) {
                $query->search($q);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('public.search-guru', compact('teachers', 'q'));
    }

    public function searchSiswa(Request $request)
    {
        $q = $request->input('q');
        $students = Student::with('school')
            ->when($q, function ($query) use ($q) {
                $query->search($q);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('public.search-siswa', compact('students', 'q'));
    }

    public function searchNonTeachingStaff(Request $request)
    {
        $q = $request->input('q');
        $nonTeachingStaff = NonTeachingStaff::with('school')
            ->when($q, function ($query) use ($q) {
                $query->where('full_name', 'like', '%' . $q . '%')
                    ->orWhere('nip_nik', 'like', '%' . $q . '%')
                    ->orWhere('position', 'like', '%' . $q . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        return view('public.search-non-teaching-staff', compact('nonTeachingStaff', 'q'));
    }

    public function searchSekolah(Request $request)
    {
        $q = $request->input('q');
        $schools = School::when($q, function ($query) use ($q) {
            $query->where('name', 'like', '%' . $q . '%')
                ->orWhere('npsn', 'like', '%' . $q . '%')
                ->orWhere('headmaster', 'like', '%' . $q . '%')
                ->orWhere('kecamatan', 'like', '%' . $q . '%')
                ->orWhere('desa', 'like', '%' . $q . '%')
                ->orWhere('kabupaten_kota', 'like', '%' . $q . '%')
                ->orWhere('provinsi', 'like', '%' . $q . '%');
        })
            ->latest()
            ->paginate(9)
            ->withQueryString();
        return view('public.search-sekolah', compact('schools', 'q'));
    }

    public function detailGuru($id)
    {
        $teacher = Teacher::with(['school', 'documents'])->findOrFail($id);
        return view('public.detail-guru', compact('teacher'));
    }

    public function detailSiswa($id)
    {
        $student = Student::with(['school', 'reports', 'certificates'])->findOrFail($id);
        return view('public.detail-siswa', compact('student'));
    }

    public function detailNonTeachingStaff($id)
    {
        $nonTeachingStaff = NonTeachingStaff::with('school')->findOrFail($id);
        return view('public.detail-non-teaching-staff', compact('nonTeachingStaff'));
    }

    public function detailSekolah($id)
    {
        $school = School::findOrFail($id);
        return view('public.detail-sekolah', compact('school'));
    }

    public function statistik(Request $request)
    {
        return $this->statistikDetail($request, 'sekolah');
    }

    public function statistikDetail(Request $request, $type)
    {
        $filter = $request->input('filter');
        // Data untuk chart jumlah (vertikal)
        $jumlahChart = $this->generateChartData($type, $filter, false);
        // Data untuk chart persentase (horizontal)
        $persenChart = $this->generateChartData($type, $filter, true);
        $total = array_sum($jumlahChart['data']['datasets'][0]['data']);
        $data = $jumlahChart['data'];
        $options = $jumlahChart['options'] ?? [];
        $barData = $persenChart['data'];
        $barOptions = $persenChart['options'] ?? [];
        $allLabels = $jumlahChart['all_labels'] ?? $data['labels'];
        return view('public.statistik', compact('type', 'data', 'options', 'barData', 'barOptions', 'total', 'allLabels', 'filter'));
    }

    // Tambahkan $asPercentage parameter
    protected function generateChartData($type, $filter = null, $asPercentage = false)
    {
        switch ($type) {
            case 'siswa':
                $query = Student::join('schools', 'students.sekolah_id', '=', 'schools.id')
                    ->select(DB::raw('schools.education_level as label'), DB::raw('count(students.id) as value'));
                break;
            case 'guru':
                $query = Teacher::join('schools', 'teachers.school_id', '=', 'schools.id')
                    ->select(DB::raw('schools.education_level as label'), DB::raw('count(teachers.id) as value'));
                break;
            case 'non-guru':
                $query = \App\Models\NonTeachingStaff::join('schools', 'non_teaching_staff.school_id', '=', 'schools.id')
                    ->select(DB::raw('schools.education_level as label'), DB::raw('count(non_teaching_staff.id) as value'));
                break;
            default:
                $query = School::select(DB::raw('education_level as label'), DB::raw('count(*) as value'));
        }

        $data = $query->groupBy('label')->get();

        $allLabels = ['TK', 'SD', 'SMP', 'KB', 'PKBM'];
        $dataMap = $data->pluck('value', 'label')->all();

        $values = [];
        foreach ($allLabels as $label) {
            $values[] = $dataMap[$label] ?? 0;
        }

        $labels = $allLabels;

        if ($filter && $filter !== 'all' && in_array($filter, $labels)) {
            $idx = array_search($filter, $labels);
            $labels = [$labels[$idx]];
            $values = [$values[$idx]];
        }

        $total = array_sum($values);
        $percentages = $total > 0 ? array_map(function ($v) use ($total) {
            return round($v / $total * 100, 1);
        }, $values) : array_fill(0, count($values), 0);
        if ($asPercentage) {
            // Untuk chart persentase: stacked bar, satu bar dua warna
            $datasetAktual = [
                'label' => 'Aktual',
                'data' => $percentages,
                'backgroundColor' => '#0d524a',
                'percentages' => $percentages,
                'datalabels' => ['display' => true],
                'borderRadius' => 12,
                'barPercentage' => 0.4, // lebih tipis
                'categoryPercentage' => 0.7,
                'stack' => 'total',
            ];
            $datasetSisa = [
                'label' => 'Sisa',
                'data' => array_map(function ($p) {
                    return 100 - $p;
                }, $percentages),
                'backgroundColor' => '#f5f7fa',
                'datalabels' => ['display' => false],
                'borderRadius' => 12,
                'barPercentage' => 0.4, // lebih tipis
                'categoryPercentage' => 0.7,
                'stack' => 'total',
            ];
            $options = [
                'plugins' => [
                    'legend' => false,
                    'tooltip' => [
                        'callbacks' => [
                            'label' => 'function(context) { return context.dataset.label + ": " + context.parsed.x + "%"; }'
                        ]
                    ]
                ],
                'scales' => [
                    'x' => [
                        'min' => 0,
                        'max' => 100,
                        'stacked' => true,
                        'ticks' => ['callback' => 'function(value){return value+"%";}']
                    ],
                    'y' => [
                        'stacked' => true
                    ]
                ]
            ];
            $datasets = [$datasetAktual, $datasetSisa];
        } else {
            // Untuk chart jumlah: data = jumlah, datalabels = jumlah
            $dataset = [
                'label' => 'Jumlah',
                'data' => $values,
                'backgroundColor' => '#0d524a',
                'percentages' => $percentages,
            ];
            $options = [
                'plugins' => [
                    'legend' => false,
                    'tooltip' => [
                        'callbacks' => [
                            'label' => 'function(context) { return context.label + ": " + context.parsed.y; }'
                        ]
                    ]
                ]
            ];
            $datasets = [$dataset];
        }
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets
            ],
            'options' => $options,
            'all_labels' => $allLabels,
        ];
    }

    /**
     * Display a listing of published articles.
     */
    public function articles(Request $request)
    {
        $query = Article::published()->with('author');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest('published_at')->paginate(9)->withQueryString();

        return view('public.articles.index', compact('articles'));
    }

    /**
     * Display the specified article.
     */
    public function articleDetail($slug)
    {
        $article = Article::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $article->incrementViews();

        // Get related articles (same author or similar title)
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->where(function ($query) use ($article) {
                $query->where('author_id', $article->author_id)
                    ->orWhere('title', 'like', '%' . substr($article->title, 0, 20) . '%');
            })
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.articles.show', compact('article', 'relatedArticles'));
    }

    /**
     * Display all galleries.
     */
    public function galleries(Request $request)
    {
        $query = Gallery::active()->ordered();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $galleries = $query->paginate(12)->withQueryString();
        $categories = Gallery::select('category')
            ->where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->toArray();

        return view('public.galleries.index', compact('galleries', 'categories'));
    }
}

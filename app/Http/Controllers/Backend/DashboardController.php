<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $data = [];
        $data['totalUsers'] = \App\Models\User::count();
        
        // TRT Haber RSS
        $data['trt_news'] = cache()->remember('trt_haber_rss', 3600, function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://www.trthaber.com/manset_articles.rss');
                if ($response->successful()) {
                    $xml = simplexml_load_string($response->body());
                    $news = [];
                    $count = 0;
                    foreach ($xml->channel->item as $item) {
                        if ($count >= 5) break;
                        $news[] = [
                            'title' => (string)$item->title,
                            'link' => (string)$item->link,
                            'date' => \Carbon\Carbon::parse((string)$item->pubDate)->translatedFormat('d M Y, H:i')
                        ];
                        $count++;
                    }
                    return $news;
                }
            } catch (\Exception $e) {
                return [];
            }
            return [];
        });

        // Finans Verileri (Döviz & Altın)
        $data['finans'] = cache()->remember('finans_data', 1800, function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://finans.truncgil.com/today.json');
                if ($response->successful()) {
                    $json = $response->json();
                    
                    $formatCurrency = function($val) {
                        if (!$val) return null;
                        $val = str_replace('.', '', $val);
                        $val = str_replace(',', '.', $val);
                        return number_format((float)$val, 2, ',', '.');
                    };

                    return [
                        'USD' => $formatCurrency($json['USD']['Satış'] ?? null),
                        'EUR' => $formatCurrency($json['EUR']['Satış'] ?? null),
                        'Gold' => $formatCurrency($json['gram-altin']['Satış'] ?? null),
                    ];
                }
            } catch (\Exception $e) {
                return null;
            }
            return null;
        });

        if ($user->hasRole('Client')) {
            $data['modules'] = [
                ['key' => 'product_management',   'title' => 'Ürün Yönetimi',     'icon' => 'ki-abstract-26', 'color' => 'primary'],
                ['key' => 'project_management',   'title' => 'Proje Yönetimi',    'icon' => 'ki-abstract-28', 'color' => 'warning'],
                ['key' => 'blog_management',      'title' => 'Blog Yönetimi',     'icon' => 'ki-abstract-27', 'color' => 'success'],
                ['key' => 'service_management',   'title' => 'Hizmet Yönetimi',   'icon' => 'ki-abstract-26', 'color' => 'primary'],
                ['key' => 'slider_management',    'title' => 'Slider Yönetimi',   'icon' => 'ki-abstract-33', 'color' => 'success'],
                ['key' => 'media_gallery',        'title' => 'Medya Galerisi',    'icon' => 'ki-abstract-35', 'color' => 'info'],
                ['key' => 'news_management',      'title' => 'Haberler',          'icon' => 'ki-abstract-41', 'color' => 'danger'],
                ['key' => 'welcome_message',      'title' => 'Açılış Mesajı',     'icon' => 'ki-abstract-44', 'color' => 'primary'],
                ['key' => 'reference_management', 'title' => 'Referans Yönetimi', 'icon' => 'ki-abstract-47', 'color' => 'warning'],
            ];

            // Son 14 gün ziyaretçi istatistikleri
            $client_id = $user->client?->id;
            $visits = [];
            $dates = [];
            $totalMonthlyVisits = 0;
            $totalNews = 0;
            $totalProducts = 0;

            if ($client_id) {
                $totalMonthlyVisits = \App\Models\PageView::where('client_id', $client_id)
                                             ->whereMonth('visited_at', now()->month)
                                             ->whereYear('visited_at', now()->year)
                                             ->count();
                                             
                $totalNews = \App\Models\News::where('client_id', $client_id)->count();
                $totalProducts = \App\Models\Product::where('client_id', $client_id)->count();

                for ($i = 13; $i >= 0; $i--) {
                    $date = now()->subDays($i)->toDateString();
                    $displayDate = now()->subDays($i)->translatedFormat('d M');
                    $count = \App\Models\PageView::where('client_id', $client_id)
                                                 ->where('visited_at', $date)
                                                 ->count();
                    $visits[] = $count;
                    $dates[] = $displayDate;
                }
            } else {
                for ($i = 13; $i >= 0; $i--) {
                    $visits[] = 0;
                    $dates[] = now()->subDays($i)->translatedFormat('d M');
                }
            }
            $data['visits_data'] = json_encode($visits);
            $data['visits_dates'] = json_encode($dates);
            $data['totalMonthlyVisits'] = number_format($totalMonthlyVisits, 0, ',', '.');
            $data['totalNews'] = $totalNews;
            $data['totalProducts'] = $totalProducts;

            return view('pages.backend.dashboard.client', $data);
        } elseif ($user->hasRole('Moderator')) {
            return view('pages.backend.dashboard.moderator', $data);
        }

        // Admin Statistics
        $data['totalClients'] = \App\Models\Client::count();
        $data['latestClients'] = \App\Models\Client::whereHas('user')->with('user')->latest()->take(5)->get();
        
        $data['domainExpiringClients'] = \App\Models\Client::whereHas('user')
            ->whereNotNull('domain_expires_at')
            ->where('domain_expires_at', '<=', now()->addDays(30))
            ->orderBy('domain_expires_at', 'asc')
            ->get();
            
        $data['sslExpiringClients'] = \App\Models\Client::whereHas('user')
            ->whereNotNull('ssl_expires_at')
            ->where('ssl_expires_at', '<=', now()->addDays(30))
            ->orderBy('ssl_expires_at', 'asc')
            ->get();

        
        $expiringProducts = collect();
        foreach ($data['domainExpiringClients'] as $client) {
            $expiringProducts->push([
                'client' => $client,
                'type' => 'Domain',
                'expires_at' => $client->domain_expires_at,
                'days_left' => $client->domainRemainingDays()
            ]);
        }
        foreach ($data['sslExpiringClients'] as $client) {
            $expiringProducts->push([
                'client' => $client,
                'type' => 'SSL',
                'expires_at' => $client->ssl_expires_at,
                'days_left' => $client->sslRemainingDays()
            ]);
        }
        $data['expiringProducts'] = $expiringProducts->sortBy('expires_at')->values();

        return view('pages.backend.dashboard.admin', $data);
    }
}

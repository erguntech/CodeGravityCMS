<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:logs.view', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Activity::with('causer')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function($row){
                    return '<span class="badge badge-light-primary fw-bold">#'.$row->id.'</span>';
                })
                ->addColumn('causer', function($row){
                    return $row->causer ? $row->causer->name : '<span class="text-muted">Sistem</span>';
                })
                ->editColumn('description', function($row){
                    $events = [
                        'created' => '<span class="badge badge-light-success">Oluşturuldu</span>',
                        'updated' => '<span class="badge badge-light-warning">Güncellendi</span>',
                        'deleted' => '<span class="badge badge-light-danger">Silindi</span>',
                        'login' => '<span class="badge badge-light-info">Giriş Yapıldı</span>',
                        'logout' => '<span class="badge badge-light-secondary">Çıkış Yapıldı</span>',
                    ];
                    return $events[$row->description] ?? $row->description;
                })
                ->addColumn('subject', function($row){
                    if ($row->subject_type) {
                        $modelName = class_basename($row->subject_type);
                        return $modelName . ' (#' . $row->subject_id . ')';
                    }
                    return 'Auth';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d.m.Y H:i');
                })
                ->addColumn('action', function($row){
                    return '<div class="d-flex justify-content-end flex-shrink-0">
                                <button class="btn btn-icon btn-light-primary btn-sm view-log-details" data-id="'.$row->id.'" title="Detaylar">
                                    <i class="ki-duotone ki-eye fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    </i>
                                </button>
                            </div>';
                })
                ->rawColumns(['id', 'description', 'action', 'causer'])
                ->make(true);
        }

        return view('pages.backend.logs.index');
    }
}

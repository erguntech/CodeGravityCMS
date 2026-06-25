<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SettingsController extends Controller
{
    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select(['id', 'name', 'email', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    return '<span class="badge badge-light-primary fw-bold">#' . $row->id . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d.m.Y H:i');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center flex-shrink-0">
                                <a href="javascript:void(0)" class="btn btn-icon btn-light-primary btn-sm me-1" title="Görüntüle">
                                    <i class="ki-duotone ki-eye fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    </i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-icon btn-light-warning btn-sm me-1" title="Düzenle">
                                    <i class="ki-duotone ki-pencil fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm" title="Sil">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                                </a>
                            </div>';
                    return $btn;
                })
                ->rawColumns(['id', 'action'])
                ->make(true);
        }

        return view('pages.backend.settings.datatables');
    }
}

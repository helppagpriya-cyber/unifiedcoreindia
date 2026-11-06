<?php

namespace App\Http\Controllers;

use App\Models\ErpSoftwareModule;
use App\Models\ErpSoftwareVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ErpSoftwarePackageController extends Controller
{
    public function version(Request $request, ErpSoftwareVersion $version): StreamedResponse
    {
        return $this->download($request, $version->package_path, $version->package_original_name);
    }

    public function module(Request $request, ErpSoftwareModule $module): StreamedResponse
    {
        return $this->download($request, $module->package_path, $module->package_original_name);
    }

    private function download(Request $request, ?string $path, ?string $filename): StreamedResponse
    {
        abort_unless($request->user()->hasAnyRole([
            'Super Admin', 'Admin', 'ERP Manager', 'EGJewel Executive',
            'Project Manager', 'Supervisor', 'Scanner Operator', 'QC Operator', 'Data Entry Operator',
        ]), 403);

        abort_unless(filled($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download(
            $path,
            $filename ?: basename($path),
            ['Content-Type' => 'application/zip'],
        );
    }
}

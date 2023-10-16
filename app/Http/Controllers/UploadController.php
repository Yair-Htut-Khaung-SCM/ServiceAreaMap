<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Map;

class UploadController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display CSV upload screen
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('upload.index');
    }

    /**
     * Upload meshcsv
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function meshcsv(Request $request)
    {
        

        Storage::disk('public')->put(config('application.map.mesh_list_csv_file'), $request->file('meshcsv')->get(), 'public');

        try {

            Map::meshListToTileImages($request->file('meshcsv')->get());

        } catch(\Exception $e) {
            
            return redirect()->route('upload.index')->with('mesh_flash_message', 'Failed to submit the CSV. Please check the format.');
        }

        // Double submission prevention
        $request->session()->regenerateToken();
        return redirect()->route('upload.index')->with('mesh_flash_message', 'CSV submission has been completed.');

    }
}


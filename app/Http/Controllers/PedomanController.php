<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedomanController extends Controller
{
    /**
     * Tampilkan Buku Panduan / Pedoman PPL (Embed Google Drive PDF).
     */
    public function index()
    {
        $driveViewUrl = 'https://drive.google.com/file/d/1zWaxZW57ThQLwIZAZAxpyPqK9_WIpV5f/view?usp=drive_link';
        $driveEmbedUrl = 'https://drive.google.com/file/d/1zWaxZW57ThQLwIZAZAxpyPqK9_WIpV5f/preview';

        return view('pedoman.index', compact('driveViewUrl', 'driveEmbedUrl'));
    }
}

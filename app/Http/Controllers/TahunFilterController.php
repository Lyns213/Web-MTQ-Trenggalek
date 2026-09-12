<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TahunFilterController extends Controller
{
    public function filter(Request $request)
    {
        $request->validate([
            "tahun_id" => "required|exists:tahuns,id"
        ]);

        session(["selected_tahun_id" => $request->tahun_id]);

        return redirect()->back();
    }
}

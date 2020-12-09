<?php

namespace App\Http\Controllers;

use App\AttendanceClass;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function show(Request $request, $id)
    {
        if ($id === '') {
            return response([
                'error' => 'cannot generate QR Code'
            ]);
        }

        $get_qr_data = AttendanceClass::where('qr_code_data', $id)->first();

        if ($get_qr_data) {
            return view('qrcode', compact('get_qr_data'));
        }

        return response([
            'error' => 'cannot generate QR Code'
        ]);
    }
}

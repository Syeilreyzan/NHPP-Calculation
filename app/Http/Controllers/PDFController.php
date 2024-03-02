<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;

class PDFController extends Controller
{
    public function generatePDF()
    {
        if (session('pdf_data')) {
            $data = session('pdf_data');
            $date = date('dmy-His', strtotime($data['date']));
            $filename = 'time-table-' . $date . '.pdf';
            $pdf = PDF::loadView('pdf.pdfView', $data);
            return $pdf->download($filename);
        } else {
            return redirect()->route('dashboard');
        }
        Session::forget('pdf_data');
    }
}

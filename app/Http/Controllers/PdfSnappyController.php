<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Knp\Snappy\Pdf;

class PdfSnappyController extends Controller
{
    public function pdf()
    {
        // dd($time);
        if (session('pdf_data')) {
            $data = session('pdf_data');
            $date = date('dmy-His', strtotime($data['date']));
            $filename = 'time-table-' . $date . '.pdf';
            $pdf = SnappyPdf::loadView('pdf.pdfView', $data);
            // return $pdf->download($filename);
            return $pdf->inline();
        } else {
            return;
        }
    }
}

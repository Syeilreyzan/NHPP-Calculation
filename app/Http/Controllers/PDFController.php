<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;

class PDFController extends Controller
{
    public function generatePdf()
    {
        if (session('pdf_data')) {
            $data = session('pdf_data');
            $date = date('dmy-His', strtotime($data['date']));
            $filename = 'single-system' . $date . '.pdf';
            $pdf = PDF::loadView('pdf.pdfView', $data);
            return $pdf->download($filename);
        } else {
            return redirect()->route('dashboard');
        }
        Session::forget('pdf_data');
    }

    public function generatePdfMultiple()
    {
        if(session('pdf_data_multiple')){
            $dataMultiple = session('pdf_data_multiple');
            $date = date('dmy-His', strtotime($dataMultiple['date']));
            $filename = 'multi-system' . $date . '.pdf';
            $pdf = PDF::loadView('pdf.pdfViewMultiple', $dataMultiple);
            return $pdf->download($filename);
        } else {
            return redirect()->route('multiple-system');
        }
        Session::forget('pdf_data_multiple');
    }
}

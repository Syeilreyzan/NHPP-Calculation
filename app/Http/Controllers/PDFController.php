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

    // public function previewPDF()
    // {
    //     $data = [
    //         'title' => 'NHPP Single System',
    //         'date' => date('j F Y, g:i:s a'),
    //         'failureTimes' => '$this->failureTimes',
    //         'numberOfFailure' => '$this->numberOfFailure',
    //         'endObservationTime' => '$this->endObservationTime',
    //         'total' => '$this->total',
    //         'slope' => '$this->slope',
    //         'lambda' => '$this->lambda',
    //         'eta' => '$this->eta',
    //         'instantenousMtbfs' => '$this->instantenousMtbfs',
    //         'cumulativeMtbfs' => '$this->cumulativeMtbfs',
    //         'predictedNumberFailures' => '$this->predictedNumberFailures',
    //         'time' => '$this->time',
    //     ];
    //     return view('pdf.pdfView', compact('data'));
    // }
}

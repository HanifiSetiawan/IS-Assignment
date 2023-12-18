<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Tcpdf\Fpdi;


class PDFSignatureController extends Controller
{
    public function showForm()
    {
        return view('pdf_upload_form');
    }

    public function signAndDownload(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf',
        ]);

        $uploadedFile = $request->file('pdf_file');
        $signedPdfPath = 'signed-pdfs/' . $uploadedFile->getClientOriginalName();
        $fullSignedPdfPath = storage_path('app/public/' . $signedPdfPath);

        $this->signPdf($uploadedFile->path(), $fullSignedPdfPath);

        return response()->download($fullSignedPdfPath)->deleteFileAfterSend(true);
    }
    
    private function signPdf($inputPath, $outputPath)
    {
        // Path ke sertifikat
        $certificate = 'file://' . base_path() . '/storage/app/certificate/Hanifi.crt';

        // Informasi tambahan untuk tanda tangan
        $info = [
            'Name' => 'Hanifi Setiawan',
            'Location' => 'Surabaya',
            'Reason' => 'Generate Demo PDF',
            'ContactInfo' => '',
        ];

        // Set tanda tangan pada file PDF
        PDF::setSignature($certificate, $certificate, 'tcpdfdemo', '', 2, $info);

        // Set properti dokumen PDF
        PDF::SetFont('helvetica', '', 12);
        PDF::SetCreator('Hanifi Setiawan');
        PDF::SetTitle('signed-pdf');
        PDF::SetAuthor('Hanifi');
        PDF::SetSubject('Signed PDF');

        // Tambahkan halaman baru ke PDF
        PDF::AddPage();

        // Baca konten PDF dari file yang diunggah
        $pdfContent = file_get_contents($inputPath);

        // Tulis konten PDF ke dokumen PDF yang ditandatangani
        PDF::writeHTML($pdfContent, true, false, true, false, '');

        //dd($outputPath);

        // Simpan dokumen PDF yang ditandatangani ke file
        PDF::Output($outputPath, 'F');

        // Reset TCPDF untuk penggunaan berikutnya
        PDF::reset();
    }

    public function downloadPdf(Request $request){
        $cipher = "AES-256-CBC";
        $options = 0;
        $iv = str_repeat("0", openssl_cipher_iv_length($cipher));
        $decryptedEmail = openssl_decrypt(Auth::user()->email, $cipher, Auth::user()->keyAES, $options, $iv);
        $username = Auth::user()->username;
    
        $certificate = 'file://'.base_path().'/storage/app/certificate/Hanifi.crt';
    
        // signature information
        $info = array(
            'Name' => Auth::user()->username,
            'Location' => 'Indonesia',
            'Reason' => 'Generate Digitally Signed PDF',
            'ContactInfo' => $decryptedEmail,
        );
    
        $request->validate([
            'file' => 'required|file|mimes:pdf',
        ]);
        $file = $request->file('file');
        Log::info('Processing file: ' . $file->getClientOriginalName());
    
        $pdf = new Fpdi();
        $pdf->AddPage();
    
        // Set signature
        $pdf->setSignature($certificate, $certificate, '', '', 2, $info);
        // Add content to the PDF
        $pdf->setSourceFile($file->getRealPath());
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx, 10, 10, 200, 200);
    
        // Output the PDF
        $pdf->Output(public_path($file->getClientOriginalName().'-digitally-signed.pdf'), 'D');
    }
    

}

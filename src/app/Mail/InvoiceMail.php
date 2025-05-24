<?php

namespace App\Mail;

use App\Models\Pesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pesanan $pesanan;

    public function __construct(Pesanan $pesanan)
    {
        $this->pesanan = $pesanan;
    }

    public function build(): self
    {
        // Generate PDF from invoice view
        $pdf = Pdf::loadView('frontend.invoice', [
            'pesanan' => $this->pesanan,
        ])->setPaper('a4', 'portrait');

        return $this->subject('Invoice Pesanan #' . $this->pesanan->nomor_pesanan)
                    ->markdown('emails.invoice', [
                        'pesanan' => $this->pesanan,
                    ])
                    ->attachData(
                        $pdf->output(),
                        'invoice-' . $this->pesanan->nomor_pesanan . '.pdf',
                        ['mime' => 'application/pdf']
                    );
    }
}

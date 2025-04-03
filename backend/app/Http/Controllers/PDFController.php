<?php

namespace App\Http\Controllers;

use App\Mail\PdfMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{

    public function uploadImage(Request $request)
    {
        // Validate the uploaded data
        $request->validate([
            'image' => 'required|string',
        ]);

        // Get the base64 encoded image data from the request
        $base64String = $request->input('image');

        // Remove the data:image/png;base64, part if present
        $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);

        // Decode the base64 string into binary data
        $imageData = base64_decode($base64String);

        // Set the directory path where images will be stored
        $imageDirectory = public_path('invoices'); // path to 'public/invoices' directory

        // Ensure the directory exists, if not, create it
        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true); // Create directory with write permissions
        }

        // Generate a unique file name for the image
        $fileName = 'invoice_' . time() . '.png'; // Change the file extension as needed

        // Define the path where the image will be saved
        $imagePath = $imageDirectory . '/' . $fileName;

        // Store the image on the server
        file_put_contents($imagePath, $imageData);

        Mail::to($request->email ?? 'recipient@example.com') // Replace with the recipient's email address
            ->queue(new PdfMail($imagePath));


        // Optionally, return the URL of the saved image
        return response()->json([
            'message' => 'Image uploaded successfully!',
            'image_url' => asset('invoices/' . $fileName),
        ]);
    }
}

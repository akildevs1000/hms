<?php
namespace App\Http\Controllers;

use App\Models\MailSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SMTPController extends Controller
{
    /**
     * Create or update SMTP settings for a specific company.
     */
    public function storeOrUpdate(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'company_id'   => 'required|integer|exists:companies,id',
                'mailer'       => 'nullable|string',
                'host'         => 'nullable|string',
                'port'         => 'nullable|integer',
                'username'     => 'nullable|string',
                'password'     => 'nullable|string',
                'encryption'   => 'nullable|string',
                'from_address' => 'nullable|email',
                'from_name'    => 'nullable|string',
            ]);

            // Create or update the mail settings
            $mailSetting = MailSetting::updateOrCreate(
                ['company_id' => $validated['company_id']],
                [
                    'mailer'       => $validated['mailer'] ?? 'smtp',
                    'host'         => $validated['host'] ?? 'smtp.gmail.com',
                    'port'         => $validated['port'] ?? 587,
                    'username'     => $validated['username'] ?? 'user@example.com',
                    'password'     => $validated['password'] ?? 'secret',
                    'encryption'   => $validated['encryption'] ?? 'tls',
                    'from_address' => $validated['from_address'] ?? 'noreply@example.com',
                    'from_name'    => $validated['from_name'] ?? 'Company Name',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'SMTP settings saved successfully.',
                'data'    => $mailSetting,
            ]);
        } catch (Exception $e) {
            Log::error('SMTP Save Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save SMTP settings.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
            ]);

            $smtp = MailSetting::where('company_id', $request->company_id)->first();

            if (! $smtp) {

                return response()->json([
                    'mailer'       => 'smtp',
                    'host'         => 'smtp.gmail.com',
                    'port'         => 587,
                    'username'     => 'your@gmail.com',
                    'password'     => '********',
                    'encryption'   => 'tls',
                    'from_address' => 'noreply@example.com',
                    'from_name'    => 'from name',
                ]);
            }

            return response()->json($smtp);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching SMTP config.', 'error' => $e->getMessage()], 500);
        }
    }
}

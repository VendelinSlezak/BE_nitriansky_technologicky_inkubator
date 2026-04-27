<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $data = $response->json();

        if ($response->failed() || !($data['success'] ?? false)) {
            $fail('Overenie reCAPTCHA zlyhalo.');
            return;
        }

        // if (isset($data['score']) && $data['score'] < config('services.recaptcha.min_score', 0.5)) {
        //     $fail('Vaša aktivita vyzerá ako automatizovaná. Skúste to znova.');
        // }
    }
}
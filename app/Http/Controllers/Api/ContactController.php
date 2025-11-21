<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @tags Contacts
 */
class ContactController extends Controller
{
    /**
     * Store a new contact message
     *
     * Creates a new contact form submission with name, email, and message.
     *
     * @group Contacts
     *
     * @bodyParam name string required Full name of the sender. Example: Ahmet Yılmaz
     * @bodyParam email string required Email address of the sender. Example: ornek@sirket.com
     * @bodyParam message string required The message content. Example: Size nasıl yardımcı olabiliriz?
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Ahmet Yılmaz",
     *     "email": "ornek@sirket.com",
     *     "message": "Size nasıl yardımcı olabiliriz?",
     *     "status": "unread",
     *     "created_at": "2025-11-08T10:00:00.000000Z",
     *     "updated_at": "2025-11-08T10:00:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "email": ["The email field is required."],
     *     "message": ["The message field is required."]
     *   }
     * }
     */
    public function store(Request $request): ContactResource|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'name.max' => 'Ad Soyad en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta en fazla 255 karakter olabilir.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'message.max' => 'Mesaj en fazla 5000 karakter olabilir.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Doğrulama başarısız',
                'errors' => $validator->errors(),
            ], 422);
        }

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return new ContactResource($contact);
    }
}

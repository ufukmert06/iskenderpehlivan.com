<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\Api\ContactResource;
use App\Models\Contact;

/**
 * @tags Contacts
 */
class ContactController extends Controller
{
    /**
     * Store a new contact message
     *
     * Creates a new contact form submission with name, email, service, and message.
     *
     * @group Contacts
     *
     * @bodyParam name string required Full name of the sender. Example: Ahmet Yılmaz
     * @bodyParam email string required Email address of the sender. Example: ornek@sirket.com
     * @bodyParam service string optional Type of service requested. Example: Individual Counseling
     * @bodyParam message string required The message content. Example: Size nasıl yardımcı olabiliriz?
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "name": "Ahmet Yılmaz",
     *     "email": "ornek@sirket.com",
     *     "service": "Individual Counseling",
     *     "message": "Size nasıl yardımcı olabiliriz?",
     *     "status": "unread",
     *     "created_at": "2025-11-08T10:00:00.000000Z",
     *     "updated_at": "2025-11-08T10:00:00.000000Z"
     *   }
     * }
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "email": ["The email field is required."],
     *     "message": ["The message field is required."]
     *   }
     * }
     */
    public function store(StoreContactRequest $request): ContactResource
    {
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'service' => $request->service,
            'message' => $request->message,
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return new ContactResource($contact);
    }
}

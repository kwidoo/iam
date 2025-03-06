<?php

namespace App\Http\Controllers;

use App\Exceptions\UserCreationException;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Kwidoo\Contacts\Contracts\Contact;
use Kwidoo\Contacts\Contracts\ContactService;
use Kwidoo\Contacts\Contracts\VerificationService;
use App\Models\User;
use Kwidoo\Contacts\Models\Contact as ModelsContact;
use RuntimeException;

class RegistrationController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $method = $request->input('method');

            if (!method_exists($this, 'registerFor' . ucfirst($method)) || !config('iam.allow_' . $method)) {
                throw new UserCreationException('Invalid registration method.');
            }
            $contact = $this->{'registerFor' . ucfirst($method)}($request);
            DB::commit();
            event('core.user.registered', [$contact]);

            if (config('iam.should_verify') || $request->input('otp')) {
                $verificationService = app()->make(VerificationService::class, ['contact' => $contact]);
                $verificationService->create($contact);

                return response()->json([
                    'message' => "User registered successfully. Please verify your {$contact->type}."
                ], 201);
            }

            return response()->json([
                'message' => 'User registered successfully.'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json(['error' => 'Registration failed.'], 500);
        }
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Kwidoo\Contacts\Models\Contact
     */
    protected function registerForEmail(RegisterRequest $request): Contact
    {
        $password = $request->input('otp') ? null : Hash::make($request->input('password'));
        $user = $this->createUser([
            'password' => $password,
        ]);

        $contact = $this->createContact($user, [
            'type'  => 'email',
            'value' => $request->input('email')
        ]);

        return $contact;
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Kwidoo\Contacts\Models\Contact
     */
    protected function registerForPhone(RegisterRequest $request): Contact
    {
        $user = $this->createUser([
            'password' => Hash::make($request->input('password'))
        ]);

        $contact =  $this->createContact($user, [
            'type'  => 'phone',
            'value' => ltrim($request->input('phone'), '+')
        ]);

        return $contact;
    }

    /**
     * @param array $userData
     *
     * @return User
     */
    protected function createUser(array $userData): User
    {
        return User::create($userData);
    }

    /**
     * @param User $user
     * @param array $contactData ['type' => 'email', 'value' => '+371...']
     *
     * @return \Kwidoo\Contacts\Models\Contact
     */
    protected function createContact(User $user, array $contactData): Contact
    {
        $contactService = app()->make(ContactService::class, ['model' => $user]);
        $uuid = $contactService->create($contactData['type'], $contactData['value']);

        $contactModel = config('contacts.model');
        if (!$contactModel) {
            throw new RuntimeException('Unable to determine contact model from configuration.');
        }

        return $contactModel::find($uuid);
    }
}

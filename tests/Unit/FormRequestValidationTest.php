<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Http\Requests\InvitationRequest;

class FormRequestValidationTest extends TestCase
{
    public function test_store_organization_request_requires_name()
    {
        // 'name' is required.
        $data = [
            'description' => 'Test Organization Description',
            'logo' => 'logo.png'
        ];

        $rules = (new StoreOrganizationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_store_organization_request_passes_with_valid_data()
    {
        $data = [
            'name' => 'Valid Organization',
            'description' => 'A valid description',
            'logo' => 'logo.png'
        ];

        $rules = (new StoreOrganizationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_update_organization_request_allows_optional_fields_to_be_missing()
    {
        // Since fields are "sometimes" required, an empty request should pass.
        $data = [];
        $rules = (new UpdateOrganizationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_update_organization_request_requires_name_if_present()
    {
        // If 'name' is provided, it must not be empty.
        $data = ['name' => ''];
        $rules = (new UpdateOrganizationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_invitation_request_requires_contact_fields()
    {
        // Both 'contact_type' and 'contact_value' are required.
        $data = [];
        $rules = (new InvitationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('contact_type', $errors);
        $this->assertArrayHasKey('contact_value', $errors);
    }

    public function test_invitation_request_fails_with_invalid_contact_type()
    {
        // Provide an invalid contact type (not one of: email, phone, code)
        $data = [
            'contact_type' => 'invalid',
            'contact_value' => 'test@example.com'
        ];
        $rules = (new InvitationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('contact_type', $validator->errors()->toArray());
    }

    public function test_invitation_request_passes_with_valid_data_and_optional_expires_at()
    {
        $data = [
            'contact_type'  => 'email',
            'contact_value' => 'test@example.com',
            'expires_at'    => '2025-12-31'
        ];
        $rules = (new InvitationRequest())->rules();
        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }
}

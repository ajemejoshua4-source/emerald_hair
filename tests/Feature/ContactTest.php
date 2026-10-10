<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_enquiry_is_stored(): void
    {
        $this->post(route('contact.send'), [
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'phone' => '08012345678',
            'subject' => 'Wig enquiry',
            'message' => 'Do you have this in 24 inches?',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', ['email' => 'ada@example.com', 'status' => 'new']);
    }

    public function test_enquiry_requires_a_message(): void
    {
        $this->post(route('contact.send'), [
            'name' => 'Ada',
            'message' => '',
        ])->assertSessionHasErrors('message');

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_newsletter_signup_is_stored_once(): void
    {
        $this->post(route('newsletter'), ['email' => 'list@example.com'])->assertRedirect();
        $this->post(route('newsletter'), ['email' => 'list@example.com'])->assertRedirect();

        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_newsletter_requires_a_valid_email(): void
    {
        $this->post(route('newsletter'), ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    }
}

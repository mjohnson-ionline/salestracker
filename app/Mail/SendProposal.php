<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SendProposal extends Mailable
{
    use Queueable, SerializesModels;

	private $proposal;
	private $client;
	private $user;
	private $token;

	/**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $proposal, $client, $user )
	{
		$this->proposal = $proposal;
		$this->client = $client;
		$this->user = $user;
	}

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
	    return new Envelope(
		    from: new Address($this->user->email, $this->user->first_name . ' ' . $this->user->last_name),
		    subject: 'Your Proposal From iOnline',
	    );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        return new Content(
	        markdown: 'emails.proposal',
	        with: [
		        'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
	            'canva_link' => $this->proposal->canva_link,
	            'proposal_id' => $this->proposal->id,
	            'phone' => $this->user->phone,
		        'staff_full_name' => $this->user->first_name . ' ' . $this->user->last_name,
		        'token' => $this->proposal->token,
	        ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}

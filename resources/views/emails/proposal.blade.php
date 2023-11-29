<x-mail::message>
# Your Proposal with iOnline

Hello {{ $first_name }},

Thank you so much for your interest in our proposal. We are excited to work with you and help you achieve your goals.

Please <a href="{{ $canva_link }}" target="_blank">click here</a> to review the full proposal as we discussed. This link will allow you to view the full presentation of the proposal.

If you would like to proceed, please <a href="{{ config('app.url') }}/sign/{{ $proposal_id }}/{{ $token }}" target="_blank">click this link</a> which will take you to a secure area ask for your digital signature. Once signed, we will be in contact with you to arrange the next steps on the project.

If you have any questions, please give me a call on {{ $phone }} or reply to this email.

Thanks,<br>
{{ $staff_full_name }}
</x-mail::message>
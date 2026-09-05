<x-mail::message>
# Contact enquiry

**From:** {{ $enquiry->name }} &lt;{{ $enquiry->email }}&gt;  
**Phone:** {{ $enquiry->phone ?: '—' }}  
**Subject:** {{ $enquiry->subject ?: '—' }}

{{ $enquiry->message }}

<x-mail::button :url="route('admin.enquiries.show', $enquiry)">
View in admin
</x-mail::button>
</x-mail::message>

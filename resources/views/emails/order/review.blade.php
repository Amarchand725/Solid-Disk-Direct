@component('mail::message')
<table width="100%" style="text-align: center; margin-bottom: 20px;">
    <tr>
        <td>
            @if(isset(settings()->black_logo) && !empty(settings()->black_logo))
                <img src="{{ asset('storage').'/'.settings()->black_logo }}" style="height: 40px;" alt="{{ settings()->name }}" />
            @else
                <img src="{{ asset('storage/images/default.png') }}" style="height: 40px;" alt="Default" />
            @endif
        </td>
    </tr>
</table>
# Thank You for Your Purchase, {{ $customerName }}!

We hope you're enjoying your recent purchase from **{{ $storeName }}**.

We’d really appreciate it if you could take a moment to leave a quick review of your experience.

@component('mail::button', ['url' => $reviewLink])
Leave a Review
@endcomponent

If you have any questions, feel free to reply to this email.  
Thanks again for shopping with us!

Best regards,  
**The {{ $storeName }} Team**

@endcomponent

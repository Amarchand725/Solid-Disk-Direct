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

# Dear {{ $data['contact_name'] ?? 'Support Team' }},

I hope this message finds you well.

I am reaching out to request a quotation for the following part number:

---

**Part Number:**  
{{ $data['product_name'] ?? 'N/A' }}

**Quantity:**  
{{ $data['quantity'] ?? 'N/A' }}

---

Please include the following details in your quotation:

- Unit price and total cost  
- Delivery timeline  
- Payment terms  
- Warranty or support details  

---

### Company Details

- **Company Name:** {{ $data['company_name'] ?? 'N/A' }}  
- **Contact Person:** {{ $data['contact_person'] ?? 'N/A' }}  
- **Phone:** {{ $data['phone'] ?? 'N/A' }}  
- **Email:** {{ $data['email'] ?? 'N/A' }}  

---

Please let me know if you require any further information to process this request.

Looking forward to your prompt response.

Warm regards,  
**{{ $data['contact_person'] ?? '' }}**  
@if(!empty($data['job_title'])){{ $data['job_title'] }}<br>@endif
{{ $data['company_name'] ?? '' }}  
{{ $data['phone'] ?? '' }}  
{{ $data['email'] ?? '' }}

@endcomponent
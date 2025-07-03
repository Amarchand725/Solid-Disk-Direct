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

# New Support Request

**Name:** {{ $data['name'] }}  
**Email:** {{ $data['email'] }}  
**Phone:** {{ $data['phone'] }}  
**Subject:** {{ $data['subject'] }}

---

**Message:**  
{{ $data['message'] }}

---

Thanks,  
{{ config('app.name') }}

@endcomponent

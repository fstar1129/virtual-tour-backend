@component('mail::message')
<div class="user-junket-submit">
    <div class="info" style="width: 730px;">
        <h1>
            <span>Welcome&nbsp;to&nbsp;</span>
            <span class="c-cyan">Junket!</span>
        </h1>
        <p>
            Thank you for creating an account to join the community of experience creators.<br/>
            Please take a moment to confirm your email address below before you can log in. 
        </p>
    </div>
    <div style="display:flex;width: 780px;">
        <div class="info" style="width: 310px;">
            <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="cursor: pointer">
                                                <a href="{{ $url }}" style="cursor: pointer;" class="button button-green" target="_blank">Confirm My Email Address</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @include('mail/partials/signature')
        </div>
        <div style="display:flex">
            <img src="{{URL::asset('/images/confirm-img.png')}}" height="100%" style="margin-right: 30px;">
            <img src="{{URL::asset('/images/phone-confirm-email.png')}}" height="100%" style="vertical-align: top;">
        </div>
    </div>
</div>
@endcomponent


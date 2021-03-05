@component('mail::message')
<div class="user-junket-submit">
    <div class="info" style="width: 730px;">
        <h1>
            <span>Welcome&nbsp;to&nbsp;</span>
            <span class="c-cyan">Junket!</span>
        </h1>
        <p>
        Junket places hundreds of adventures and curated tours from around the world at your fingertips. Now you can experience an area when you want, how you want, with whom you want.
        </p>
        <p>Not only can you enjoy Junkets, but you can create them too! For information on how to create a Junket, visit <a href="https://wejunket.com/business" style="color: #5496D3">https://wejunket.com/business</a>.</p>
        <p>Having issues? Contact our support at <a href="#" style="color: #5496D3">contact@wejunket.com</a>.
        </p>
    </div>
    <div style="display:flex;width: 780px;">
        <div class="info" style="width: 310px;padding-top: 125px">
            @include('mail/partials/signature')
        </div>
        <div style="display:flex">
            <img src="{{URL::asset('/images/confirm-img.png')}}" height="100%" style="margin-right: 30px;">
            <img src="{{URL::asset('/images/phone-confirm-email.png')}}" height="100%" style="vertical-align: top;">
        </div>
    </div>
</div>
@endcomponent

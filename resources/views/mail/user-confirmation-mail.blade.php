@component('mail::message')
<div class="user-junket-submit">
    <div class="info">
        <h1>
            <span>Welcome&nbsp;</span>
            <span class="c-cyan">aboard!</span>
        </h1>
        <div style="display:flex">
            <div class="info-description" style="width: 650px">
                <p>Thanks for confirming your email address! Your account has now been created.
                    Please see some useful resources and information to get started below.
                </p>
                <p><strong>Login to the CMS</strong> to start creating your Junkets <a href="https://cms.wejunket.com/#/login" style="color: #5496D3">here</a>
                </p>
                <p>See video tutorials on how to create your junkets and adventures on our 
                <a href="https://youtu.be/5mGjnydvW9E" style="color: #5496D3">Youtube page</a> and in dashboard message center
                </p>
                <p>
                    <strong>See list of requirements for creating a Junket:</strong>
                </p>
                <p>
                Each stop and each Junket must have an image. Image size must
                 be in landscape orientation at 800 x 1200 pixels to 1200 x 1600 pixels
                  (verify) and no greater than 5mb.
                </p>
                <p>Each stop must have audio in mp3 format
                </p>
                <p>If embedding video, you must use a youtube link</p>
                <p>Each stop and Junket must have text</p>
                <p>Adventures must have a minimum of 16 locations. </p>
                <p>See our guide for creating adventures <a href="https://youtu.be/mUYYgdBnYfM" style="color: #5496D3">(link)</a> .</p>
            </div>
        </div>
        @include('mail/partials/signature')
    </div>
    <img src="{{URL::asset('/images/confirm-email-img1.png')}}" height="100%" style="margin-top: 20px;">
</div>
@endcomponent

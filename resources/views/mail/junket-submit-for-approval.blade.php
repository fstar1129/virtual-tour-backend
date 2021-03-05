@component('mail::message')
<div class="user-junket-submit">
    <div class="info">
        <h1>
            <span>Your&nbsp;Junket&nbsp;has&nbsp;been&nbsp;</span>
            <span class="c-cyan">Submitted!</span>
        </h1>
        <div style="display:flex">
            <div class="info-description">
                <p>
                Thanks for submitting your Junket! We'll review it as soon as possible.<br/>
                <span style="color:#74787e">It may take up to two business days to review your Junket.</span>
                </p>
                <p>
                In the meantime, you can prepare to market and promote your Junket <br/>
                with this <a href="https://wejunket.com/promotion-guide" style="color:#5496D3;cursor:pointer">promotion guide</a>.
                </p>
                <p>
                Thanks for your patience!
                </p>
            </div>
        </div>
        @include('mail/partials/signature')
    </div>
    <img src="{{URL::asset('/images/phone-junket-submit1.png')}}" height="100%">
</div>
@endcomponent
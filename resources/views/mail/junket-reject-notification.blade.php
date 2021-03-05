@component('mail::message')
<div class="user-junket-submit">
    <div class="info">
        <h1>
            <span>Junket&nbsp;Not&nbsp;Approved</span>
        </h1>
        <div style="display:flex">
            <div class="info-description">
                <p>
                Reason: "{{ $tour_details["reason"] }}" <br/>
                </p>
            </div>
        </div>
        @include('mail/partials/signature')
    </div>
    <img src="{{URL::asset('/images/approval-img.png')}}" height="100%">
</div>
@endcomponent
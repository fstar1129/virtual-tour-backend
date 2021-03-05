@component('mail::message')
<div class="user-junket-submit">
    <div class="info">
        <h1>
            <span>Junket&nbsp;</span>
            <span class="c-cyan">Approved!</span>
        </h1>
        <div style="display:flex">
            <div class="info-description">
                <p>
                Congratulations, your Junket "{{ $tour_details["tour_title"] }}" has been approved!<br/>
                </p>
            </div>
        </div>
        @include('mail/partials/signature')
    </div>
    <img src="{{URL::asset('/images/approval-img.png')}}" height="100%">
</div>
@endcomponent
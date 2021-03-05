@component('mail::message')
<div class="user-junket-submit">
    <div class="info" style="width: 760px;">
        <h1>
            <span>You successfully changed your </span>
            <span class="c-cyan">Junket Plan.</span>
        </h1>
        <p style="font-size: 22px;">
            This confirms that your Junket plan has been changed.
        </p>
    </div>
    <div style="display:flex;width: 780px;">
        <div class="info" style="width: 310px;">
            <p style="font-size: 22px;color:#5496d3;margin-bottom: 5px;">Your active plan:</p>
            <p style="text-transform: capitalize;font-weight: 700">
                {{$details["pricing_plan"]->plan_name}} ${{$details["pricing_plan"]->cost}}/month 
                @if ($details["pricing_plan"]->billing_cycle == "year")
                    (Billed Annually)
                @endif
            </p>
            <p>Happy exploring!</p>
            @include('mail/partials/signature')
        </div>
        <div style="display:flex">
            <img src="{{URL::asset('/images/confirmation-change-plan-img.png')}}" height="100%" style="margin-right: 30px;">
            <img src="{{URL::asset('/images/phone-confirm-email.png')}}" height="100%" style="vertical-align: top;">
        </div>
    </div>
</div>
@endcomponent

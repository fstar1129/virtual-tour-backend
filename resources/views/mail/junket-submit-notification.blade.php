@component('mail::message')
<div class="user-junket-submit">
    <div class="info">
        <h1>
            <span>A&nbsp;user&nbsp;has&nbsp;</span>
            <span class="c-cyan">submitted</span>
            <span>&nbsp;a&nbsp;Junket!</span>
        </h1>
        <div style="display:flex">
            <div class="info-title">
                <p>User:</p>
                <p>Junket Name:</p>
                <p>Junket location:</p>
                <p>Number of Stops:</p>
                <p>Plan:</p>
            </div>
            <div class="info-description">
                <p>{{ $tour_details["user_name"] }}</p>
                <p>{{ $tour_details["tour_title"] }}</p>
                <p>{{ $tour_details["tour_location"] }}</p>
                <p>{{ $tour_details["number_of_stops"] }}</p>
                <p style="text-transform: capitalize">{{ $tour_details["plan"] }}</p>
            </div>
        </div>
        <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="center">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="cursor: pointer">
                                            <a href="{{ $tour_details["review_url"] }}" style="cursor: pointer;" class="button button-green" target="_blank">Click to Review </a>
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
    <img src="{{URL::asset('/images/phone-junket-submit.png')}}" height="100%">
</div>
@endcomponent

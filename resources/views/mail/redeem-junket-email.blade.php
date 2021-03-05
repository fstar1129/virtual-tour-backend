@component('mail::message')
<div class="user-junket-submit">
    <div class="info" style="width: 730px;margin-bottom: 0">
        <h1>
            <span>{{ $tour_details["user_name"] }}, </span>
            <span class="c-cyan">Welcome&nbsp;to&nbsp;Junket!</span>
        </h1>
        <p>
            Junket allows you to take a tour anywhere, anytime. It's an adventure and tour 
            guide in your pocket that lets you experience an area on your terms, where 
            and when you want, and with whom you want!
        </p>
        <p>
            Please follow the instructions below to use 
            your Junket. You will have seven days to 
            use your Junket after you redeem it.
        </p>
    </div>
    <div style="display:flex;width: 780px;">
        <div class="info" style="width: 400px;">
            <p style="display:none">
                <a href="" style="color: #5496D3;font-size: 16px;font-weight: 700;text-decoration: underline;cursor: pointer;">See how Junket works.</a>
            </p>
            <p style="color: black;"><strong>{{ $tour_details["tour_title"] }}</strong></p>
            <p style="color: black;"><strong>{{ $tour_details["tour_location"] }}</strong></p>
            <p style="color: black;"><strong style="margin-right: 40px;">Stops: {{ $tour_details["number_of_stops"] }}</strong><strong>Audio: {{ intval($tour_details["total_audio"] / 60) }}min</strong></p>
            <p style="color: #5496D3;font-weight: 700">
                1. Download the Junket app and sign in
            </p>
            <div style="margin-bottom:15px;text-align: left;">
                <a href="https://play.google.com/store/apps/details?id=com.junket.app" style="cursor:pointer">
                    <img src="{{URL::asset('/images/google-play.png')}}" />
                </a>
                <a href="https://apps.apple.com/us/app/junket-explore-your-world/id1297242830" style="cursor:pointer">
                    <img src="{{URL::asset('/images/apple-play.png')}}" />
                </a>
            </div>
            <p style="color: #5496D3;font-weight: 700">
                2. Press button below to launch your Junket. 
            </p>
            <table class="action" cellpadding="0" cellspacing="0" style="text-align: left">
                @foreach ($tour_details['promo_codes'] as $promo_code)
                    <tr style="cursor: pointer">
                        <td style="cursor: pointer;text-align: left;padding-bottom: 10px;">
                            Device{{ $loop->index + 1 }}: <a href="https://wejunket.com/junket/{{ $tour_details['tour_id'] }}?pc={{ $promo_code }}" style="cursor: pointer;font-family: Avenir,Helvetica,sans-serif;border-radius:10px;" class="button button-green" target="_blank">
                                <span style="cursor: pointer;">LAUNCH JUNKET</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
            <p style="font-size: 14px !important;color: black !important">1. You have 7 days to use the link.</p>
            <p style="font-size: 14px !important;color: black !important">2. If you want to use the Junket on one device, that is fine. You do not have to use both links.</p>
            <p style="font-size: 14px !important;color: black !important">3. Links can only be used once.</p>
            <p style="color: #5496D3;font-weight: 700">
                3. Enjoy
            </p>
            @include('mail/partials/signature')
            <p style="margin-top:20px;">
                <a href="https://wejunket.com/faq" style="color: #5496D3;font-size: 16px;font-weight: 700;text-decoration: underline;cursor: pointer;">Read Our FAQ</a>
            </p>
        </div>
        <div style="display:flex">
            <div class="smartphone">
                <div class="content" style="background:black;position:relative">
                    <p style="color:white;text-align:center">{{ $tour_details['tour_title'] }}</p>
                    <img src="{{ $tour_details['tour_image'] }}" height="40%" style="margin-top: -73px;">
                    <button style="background: linear-gradient(45deg, #EA3844, #60A7DB, #6FC396);margin-top: 10px;border-radius: 5px;color: white;width:100%;border: none;height: 30px;">BEGIN JUNKET</button>
                    <p style="font-size:12px;margin-top:10px;color: white;height: 130px;overflow: hidden;">
                        {{ $tour_details['tour_description'] }}
                    </p>
                    <img src="{{URL::asset('/images/audio-bar.png')}}" style="position: absolute;bottom: 0;left: 0;">
                </div>
            </div>
        </div>
    </div>
</div>
@endcomponent

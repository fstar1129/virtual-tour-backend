<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
</head>
<body>
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
        .inner-body {
			width: 100%;
		}
        .header {
			height: 200px;
            background-image: url({{URL::asset("/images/header-background.png")}});
        }
		.user-junket-submit {
			
		}
		.user-junket-submit .info {
            display: inline-block;
			margin-bottom: 30px;
			margin-right: 50px;
            vertical-align: top;
		}
		.user-junket-submit .info .info-title{
			font-size: 22px;
			font-weight: bold;
			margin-right: 20px;
		}
		.user-junket-submit .info .info-description{
			font-size: 22px;
		}
		
		.user-junket-submit .info h1 {
			font-size: 36px;
			font-weight: bold;
			font-family: Arial;
		}
		.user-junket-submit .info h1 .c-cyan {
			color: #5496D3;
		}
		.footer {
			width: 100%;
			background-repeat: no-repeat;
			background-size: 100% 100%;
            height: 160px;
            background-color: white;
            background-image: url({{URL::asset("/images/footer-background.png")}});
		}
		.footer-info {
			margin-top: 100px;
			font-size: 16px;
			color: white;
		}
		.footer-info a {
            color: white;
            text-align: center;
		}
    </style>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    {{ $header ?? '' }}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" cellpadding="0" cellspacing="0">
                                <!-- Body content -->
                                <tr>
                                    <td align="center">
                                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                                        {{ $subcopy ?? '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{ $footer ?? '' }}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

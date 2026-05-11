<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.interview_invitation.subject', ['vacancy' => $vacancy->title]) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #f5f5f3;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            padding: 40px 16px;
        }

        .wrapper {
            max-width: 560px;
            margin: 0 auto;
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #000;
        }

        /* Card */
        .card {
            background: #ffffff;
            border: 1px solid #000000;
            border-radius: 24px;
            overflow: hidden;
        }

        /* Header stripe */
        .card-header {
            padding: 36px 40px 32px;
            border-bottom: 1px solid #e8e8e6;
        }

        .greeting {
            font-size: 13px;
            color: #888;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .headline {
            font-size: 26px;
            font-weight: 700;
            color: #000;
            line-height: 1.25;
            letter-spacing: -0.5px;
        }

        /* Body */
        .card-body {
            padding: 32px 40px;
        }

        .text {
            font-size: 15px;
            line-height: 1.7;
            color: #444;
            margin-bottom: 24px;
        }

        /* Vacancy block */
        .vacancy-block {
            background: #f8f8f7;
            border: 1px solid #e8e8e6;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }

        .vacancy-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 6px;
        }

        .vacancy-title {
            font-size: 18px;
            font-weight: 700;
            color: #000;
            margin-bottom: 4px;
        }

        .vacancy-company {
            font-size: 13px;
            color: #666;
        }

        /* Token block */
        .token-block {
            background: #000;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }

        .token-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 10px;
        }

        .token-link {
            display: block;
            font-size: 13px;
            color: #fff;
            word-break: break-all;
            text-decoration: none;
            line-height: 1.5;
            opacity: 0.85;
        }

        /* CTA button */
        .btn-wrap {
            text-align: center;
            margin-bottom: 28px;
        }

        .btn {
            display: inline-block;
            background: #000;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 14px 36px;
            border-radius: 100px;
        }

        /* Expiry note */
        .expiry {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8f8f7;
            border: 1px solid #e8e8e6;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #888;
        }

        .expiry-dot {
            width: 6px;
            height: 6px;
            background: #bbb;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Footer */
        .card-footer {
            padding: 20px 40px;
            border-top: 1px solid #e8e8e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-brand {
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }

        .footer-note {
            font-size: 11px;
            color: #bbb;
        }

        /* Below card */
        .below {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #bbb;
            line-height: 1.6;
        }

        .below a {
            color: #888;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="logo">
        <span class="logo-text">RECRU</span>
    </div>

    <div class="card">

        <div class="card-header">
            <p class="greeting">{{ __('emails.interview_invitation.greeting') }}</p>
            <h1 class="headline">{!! __('emails.interview_invitation.headline') !!}</h1>
        </div>

        <div class="card-body">

            <p class="text">
                {!! __('emails.interview_invitation.intro', [
                    'firstName' => $candidate->first_name,
                    'lastName' => $candidate->last_name
                ]) !!}
            </p>

            <div class="vacancy-block">
                <p class="vacancy-label">{{ __('emails.interview_invitation.vacancy_label') }}</p>
                <p class="vacancy-title">{{ $vacancy->title }}</p>
                <p class="vacancy-company">{{ $user->tenant->name }}</p>
            </div>

            <div class="btn-wrap">
                <a href="{{ $interviewUrl }}" class="btn">{{ __('emails.interview_invitation.button') }}</a>
            </div>

            <div class="expiry">
                <div class="expiry-dot"></div>
                {{ __('emails.interview_invitation.expiry', ['expiresAt' => $interview->token_expires_at->format('d.m.Y H:i')]) }}
            </div>

        </div>

        <div class="card-footer">
            <span class="footer-brand">RECRU</span>
            <span class="footer-note">{{ __('emails.interview_invitation.footer_note') }}</span>
        </div>

    </div>

    <div class="below">
        {{ __('emails.interview_invitation.ignore') }}<br>
        <a href="#">{{ __('emails.interview.privacy') }}</a> · <a href="#">{{ __('emails.interview_invitation.unsubscribe') }}</a>
    </div>

</div>

</body>
</html>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.interview_finished.subject', ['firstName' => $candidate->first_name, 'lastName' => $candidate->last_name, 'vacancy' => $vacancy->title]) }}</title>
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

        .card {
            background: #ffffff;
            border: 1px solid #000000;
            border-radius: 24px;
            overflow: hidden;
        }

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

        .card-body {
            padding: 32px 40px;
        }

        .text {
            font-size: 15px;
            line-height: 1.7;
            color: #444;
            margin-bottom: 24px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 28px;
        }

        .info-block {
            background: #f8f8f7;
            border: 1px solid #e8e8e6;
            border-radius: 14px;
            padding: 16px 20px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 700;
            color: #000;
        }

        .info-sub {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .status-block {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #000;
            border-radius: 14px;
            padding: 20px 24px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: #fff;
            border-radius: 50%;
            flex-shrink: 0;
            opacity: 0.5;
        }

        .status-text {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            line-height: 1.4;
        }

        .status-sub {
            font-size: 12px;
            color: #aaa;
            margin-top: 2px;
        }

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
            <p class="greeting">{{ __('emails.interview_finished.greeting') }}</p>
            <h1 class="headline">{!! __('emails.interview_finished.headline') !!}</h1>
        </div>

        <div class="card-body">

            <p class="text">
                {!! __('emails.interview_finished.intro', [
                    'hrName'    => $hr->name,
                    'firstName' => $candidate->first_name,
                    'lastName'  => $candidate->last_name,
                    'vacancy'   => $vacancy->title,
                ]) !!}
            </p>

            <div class="info-grid">
                <div class="info-block">
                    <p class="info-label">{{ __('emails.interview_finished.candidate_label') }}</p>
                    <p class="info-value">{{ $candidate->first_name }} {{ $candidate->last_name }}</p>
                    <p class="info-sub">{{ $candidate->email }}</p>
                </div>
                <div class="info-block">
                    <p class="info-label">{{ __('emails.interview_finished.vacancy_label') }}</p>
                    <p class="info-value">{{ $vacancy->title }}</p>
                    <p class="info-sub">{{ $interview->created_at->format('d.m.Y') }}</p>
                </div>
            </div>

            <div class="status-block">
                <div class="status-dot"></div>
                <div>
                    <div class="status-text">{{ __() }}</div>
                    <div class="status-sub">{{ $candidate->first_name }} {{ $candidate->last_name }} · {{ $vacancy->title }}</div>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <span class="footer-brand">RECRU</span>
            <span class="footer-note">{{ __('emails.interview_finished.footer_note') }}</span>
        </div>

    </div>

    <div class="below">
        <a href="#">{{ __('emails.interview_finished.privacy') }}</a> · <a href="#">{{ __('emails.interview_finished.unsubscribe') }}</a>
    </div>

</div>

</body>
</html>

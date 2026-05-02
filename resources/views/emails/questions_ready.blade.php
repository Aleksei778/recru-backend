<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.questions_ready.subject', ['firstName' => $candidate->first_name, 'lastName' => $candidate->last_name]) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #f5f5f3;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            padding: 40px 16px;
        }

        .wrapper { max-width: 520px; margin: 0 auto; }

        .logo { text-align: center; margin-bottom: 28px; }
        .logo-text { font-size: 20px; font-weight: 700; letter-spacing: -0.5px; color: #000; }

        .card {
            background: #fff;
            border: 1px solid #000;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 28px 32px 24px;
            border-bottom: 1px solid #e8e8e6;
        }

        .tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff;
            background: #000;
            border-radius: 100px;
            padding: 4px 10px;
            margin-bottom: 14px;
        }

        .headline {
            font-size: 22px;
            font-weight: 700;
            color: #000;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .card-body { padding: 28px 32px; }

        .text {
            font-size: 14px;
            line-height: 1.65;
            color: #555;
            margin-bottom: 24px;
        }

        .info-block {
            background: #f8f8f7;
            border: 1px solid #e8e8e6;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            font-size: 13px;
            line-height: 1.5;
        }

        .info-row + .info-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e8e8e6;
        }

        .info-label { color: #999; white-space: nowrap; }
        .info-value { color: #000; font-weight: 600; text-align: right; }

        .btn-wrap { text-align: center; }

        .btn {
            display: inline-block;
            background: #000;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 12px 28px;
            border-radius: 100px;
        }

        .card-footer {
            padding: 16px 32px;
            border-top: 1px solid #e8e8e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-brand { font-size: 13px; font-weight: 700; color: #000; }
        .footer-note { font-size: 11px; color: #bbb; }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="logo">
        <span class="logo-text">RECRU</span>
    </div>

    <div class="card">

        <div class="card-header">
            <div class="tag">{{ __('emails.questions_ready.tag') }}</div>
            <h1 class="headline">{{ __('emails.questions_ready.headline') }}</h1>
        </div>

        <div class="card-body">

            <p class="text">
                {!! __('emails.questions_ready.intro', ['hrName' => $user->first_name]) !!}
            </p>

            <div class="info-block">
                <div class="info-row">
                    <span class="info-label">{{ __('emails.questions_ready.candidate_label') }}</span>
                    <span class="info-value">{{ $candidate->first_name }} {{ $candidate->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('emails.questions_ready.vacancy_label') }}</span>
                    <span class="info-value">{{ $interview->vacancy->title }}</span>
                </div>
            </div>

        </div>

        <div class="card-footer">
            <span class="footer-brand">RECRU</span>
            <span class="footer-note">{{ __('emails.questions_ready.footer_note') }}</span>
        </div>

    </div>

</div>

</body>
</html>

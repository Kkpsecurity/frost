<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #212a3e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #212a3e;
            margin: 0;
        }
        .field-group {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #212a3e;
            margin-bottom: 5px;
        }
        .field-value {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #212a3e;
        }
        .message-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            border-left: 4px solid #17a2b8;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
        </div>

        <div class="field-group">
            <div class="field-label">Name:</div>
            <div class="field-value">{{ $data['name'] }}</div>
        </div>

        <div class="field-group">
            <div class="field-label">Email:</div>
            <div class="field-value">{{ $data['email'] }}</div>
        </div>

        @if(!empty($data['phone']))
        <div class="field-group">
            <div class="field-label">Phone:</div>
            <div class="field-value">{{ $data['phone'] }}</div>
        </div>
        @endif

        @if(!empty($data['subject']))
        <div class="field-group">
            <div class="field-label">Subject:</div>
            <div class="field-value">
                @php
                    $subjectOptions = [
                        'general' => 'General Inquiry',
                        'enrollment' => 'Course Enrollment',
                        'support' => 'Technical Support',
                        'licensing' => 'Licensing Questions',
                        'partnership' => 'Partnership Opportunities',
                        'other' => 'Other'
                    ];
                    echo $subjectOptions[$data['subject']] ?? ucfirst($data['subject']);
                @endphp
            </div>
        </div>
        @endif

        <div class="field-group">
            <div class="field-label">Message:</div>
            <div class="message-content">
                {!! nl2br(e($data['message'])) !!}
            </div>
        </div>

        <div class="footer">
            <p>This message was sent from the contact form on {{ config('app.name') }}</p>
            <p><strong>Submitted:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Trial Has Ended</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .email-header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }

        .email-header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .email-body {
            padding: 40px 30px;
        }

        .email-body h2 {
            color: #10b981;
            font-size: 24px;
            margin: 0 0 20px 0;
        }

        .email-body p {
            margin: 15px 0;
            font-size: 16px;
            line-height: 1.6;
        }

        .trial-summary {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .trial-summary h3 {
            margin: 0 0 15px 0;
            color: #10b981;
            font-size: 18px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            gap: 15px;
        }

        .stat-item {
            flex: 1;
            text-align: center;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
        }

        .stat-item .number {
            font-size: 32px;
            font-weight: bold;
            color: #10b981;
        }

        .stat-item .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .plan-options {
            margin: 30px 0;
        }

        .plan-card {
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 25px;
            margin: 15px 0;
            position: relative;
            transition: all 0.3s ease;
        }

        .plan-card.featured {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .featured-badge {
            position: absolute;
            top: -12px;
            right: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .plan-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .plan-price {
            font-size: 28px;
            font-weight: bold;
            color: #10b981;
        }

        .plan-price .period {
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }

        .plan-features {
            margin: 15px 0;
        }

        .plan-feature {
            display: flex;
            align-items: center;
            margin: 8px 0;
            font-size: 14px;
            color: #666;
        }

        .plan-feature::before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-right: 10px;
        }

        .button-container {
            text-align: center;
            margin: 20px 0;
        }

        .email-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(21, 128, 61, 0.2);
        }

        .secondary-button {
            display: inline-block;
            background-color: #ffffff;
            color: #10b981 !important;
            padding: 13px 38px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid #10b981;
            margin-left: 10px;
        }

        .free-plan-info {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }

        .free-plan-info h3 {
            margin: 0 0 10px 0;
            color: #10b981;
            font-size: 16px;
        }

        .free-plan-info p {
            margin: 0;
            color: #1e3a8a;
            font-size: 14px;
        }

        .testimonial {
            background-color: #f9fafb;
            border-left: 4px solid #6b7280;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            font-style: italic;
        }

        .testimonial p {
            margin: 0 0 10px 0;
            color: #374151;
        }

        .testimonial-author {
            font-weight: bold;
            color: #10b981;
            font-style: normal;
        }

        .email-footer {
            background-color: #f9fafb;
            text-align: center;
            padding: 30px 20px;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer a {
            color: #10b981;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .email-container {
                width: 100%;
                border-radius: 0;
                margin: 0;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .summary-stats {
                flex-direction: column;
            }

            .stat-item {
                margin-bottom: 10px;
            }

            .plan-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .plan-price {
                margin-top: 10px;
            }

            .secondary-button {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>⏰ Your Trial Period Has Ended</h1>
            <p>Continue enjoying premium features</p>
        </div>

        <div class="email-body">
            <h2>Hi {{ $subscription->user->name }},</h2>

            <p>Your 7-day free trial of SnapshotAlbums has come to an end. We hope you enjoyed exploring all the premium features!</p>

            <div class="trial-summary">
                <h3>📊 Your Trial Summary</h3>
                <p style="color: #991b1b; margin-bottom: 15px;">Here's what you accomplished during your trial:</p>

                <div class="summary-stats">
                    <div class="stat-item">
                        <div class="number">{{ $eventsCreated ?? 0 }}</div>
                        <div class="label">Events Created</div>
                    </div>
                    <div class="stat-item">
                        <div class="number">{{ $photosCollected ?? 0 }}</div>
                        <div class="label">Photos Collected</div>
                    </div>
                </div>
            </div>

            <p><strong>Don't lose access to your premium features!</strong> Choose a plan below to continue using SnapshotAlbums without interruption.</p>

            <div class="plan-options">
                <h3 style="text-align: center; color: #10b981; margin-bottom: 20px;">Choose Your Plan</h3>

                @foreach($availablePlans ?? [] as $plan)
                <div class="plan-card {{ $plan->is_popular ? 'featured' : '' }}">
                    @if($plan->is_popular)
                    <div class="featured-badge">Most Popular</div>
                    @endif

                    <div class="plan-header">
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-price">
                            ${{ number_format($plan->price, 0) }}
                            <span class="period">/month</span>
                        </div>
                    </div>

                    <div class="plan-features">
                        @foreach($plan->features as $feature)
                        <div class="plan-feature">{{ $feature }}</div>
                        @endforeach
                    </div>

                    <div class="button-container">
                        <a href="{{ config('app.frontend_url') }}/payment?planId={{ $plan->id }}" class="email-button">Select {{ $plan->name }}</a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="free-plan-info">
                <h3>💡 Prefer a Free Option?</h3>
                <p>You can continue using SnapshotAlbums with our free plan, which includes basic features for small events. However, you'll lose access to premium features like unlimited events, advanced storage, and priority support.</p>
            </div>

            <div class="testimonial">
                <p>"SnapshotAlbums made collecting photos from our wedding guests so easy! The QR code system was genius, and having everything automatically backed up to Google Drive gave us peace of mind. Totally worth the subscription!"</p>
                <div class="testimonial-author">— Sarah & John, Premium Members</div>
            </div>

            <div class="button-container">
                <a href="{{ config('app.frontend_url') }}/pricing" class="email-button">View All Plans</a>
                <a href="{{ config('app.frontend_url') }}/dashboard" class="secondary-button">Go to Dashboard</a>
            </div>

            <p style="margin-top: 30px;">Have questions about our plans? Our team is here to help you choose the best option for your needs.</p>

            <p style="margin-top: 30px;">
                <strong>Thank you for trying SnapshotAlbums!</strong><br>
                The SnapshotAlbums Team
            </p>
        </div>

        <div class="email-footer">
            <p>© {{ date('Y') }} SnapshotAlbums. All rights reserved.</p>
            <p style="margin-top: 15px;">
                <a href="{{ config('app.frontend_url') }}/contact">Contact Support</a> |
                <a href="{{ config('app.frontend_url') }}/faq">FAQ</a> |
                <a href="{{ config('app.frontend_url') }}/pricing">Pricing</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This email was sent to {{ $subscription->user->email }}
            </p>
        </div>
    </div>
</body>

</html>

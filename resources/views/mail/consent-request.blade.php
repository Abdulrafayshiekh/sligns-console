<!doctype html>
<html>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5">
    <p>Hello {{ $reassignment->client->name }},</p>
    <p>{{ $reassignment->fromCoach->name }} is leaving {{ $reassignment->company->name }}. We would like to move you to {{ $reassignment->toCoach?->name ?? 'a new coach' }}.</p>
    <p>Your new coach will see your app activity. Private notes from your previous coach are never shared.</p>
    <p><a href="{{ $url }}" style="display: inline-block; padding: 10px 18px; background: {{ $reassignment->company->interface_color }}; color: #fff; text-decoration: none; border-radius: 8px">Review and agree</a></p>
    <p style="color: #6b7280; font-size: 13px">This link expires in 14 days.</p>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Blade Directive Test</title>
</head>
<body>
    <h1>Blade Directive Test</h1>

    <h2>Debug Info:</h2>
    <p>Auth::guard('admin')->check(): {{ Auth::guard('admin')->check() ? 'true' : 'false' }}</p>

    @if(Auth::guard('admin')->check())
        <p>User ID: {{ Auth::guard('admin')->user()->id }}</p>
        <p>User Email: {{ Auth::guard('admin')->user()->email }}</p>
        <p>Role ID: {{ Auth::guard('admin')->user()->role_id }}</p>
        <p>Role ID Type: {{ gettype(Auth::guard('admin')->user()->role_id) }}</p>
        <p>IsSysAdmin(): {{ Auth::guard('admin')->user()->IsSysAdmin() ? 'true' : 'false' }}</p>
        <p>IsAdministrator(): {{ Auth::guard('admin')->user()->IsAdministrator() ? 'true' : 'false' }}</p>
        <p>IsAnyAdmin(): {{ Auth::guard('admin')->user()->IsAnyAdmin() ? 'true' : 'false' }}</p>
    @else
        <p>User not authenticated</p>
    @endif

    <h2>Blade Directives Test:</h2>

    @sysadmin
        <div style="background: green; color: white; padding: 10px; margin: 10px 0;">
            ✅ @sysadmin directive is working! This should only be visible to sys admins.
        </div>
    @endsysadmin

    @isAnyAdmin
        <div style="background: blue; color: white; padding: 10px; margin: 10px 0;">
            ✅ @isAnyAdmin directive is working! This should be visible to any admin.
        </div>
    @endisAnyAdmin

    @administrator
        <div style="background: orange; color: white; padding: 10px; margin: 10px 0;">
            ✅ @administrator directive is working! This should be visible to administrators and above.
        </div>
    @endadministrator

    <div style="background: gray; color: white; padding: 10px; margin: 10px 0;">
        This content is always visible (no directive).
    </div>
</body>
</html>

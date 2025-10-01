{{-- Account Content Header Component --}}
<div class="content-header">
    @switch($activeSection)
        @case('profile')
            <div class="content-title">
                <i class="fas fa-user"></i>
                Profile Information
            </div>
            <div class="content-description">Manage your personal information and account settings</div>
        @break

        @case('settings')
            <div class="content-title">
                <i class="fas fa-cog"></i>
                Account Settings
            </div>
            <div class="content-description">Configure your preferences and privacy settings</div>
        @break

        @case('orders')
            <div class="content-title">
                <i class="fas fa-shopping-bag"></i>
                Order History
            </div>
            <div class="content-description">View your course enrollments and purchase history</div>
        @break

        @case('payments')
            <div class="content-title">
                <i class="fas fa-credit-card"></i>
                Payment Methods
            </div>
            <div class="content-description">Manage your payment methods and billing information</div>
        @break

        @default
            <div class="content-title">
                <i class="fas fa-user"></i>
                Profile Information
            </div>
            <div class="content-description">Manage your personal information and account settings</div>
    @endswitch
</div>

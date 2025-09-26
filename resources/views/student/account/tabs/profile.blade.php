{{-- Modern Profile Tab Content --}}
<div class="row">
    <div class="col-lg-4">
        {{-- Modern Avatar Section --}}
        <div class="modern-card mb-4">
            <div class="card-body profile-avatar-section">
                @if($profileData['avatar']['current_avatar'])
                    <img src="{{ $profileData['avatar']['current_avatar'] }}" alt="Profile Avatar" class="profile-avatar">
                @else
                    <div class="profile-avatar bg-light mx-auto d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fa-3x text-muted"></i>
                    </div>
                @endif

                <div class="profile-name">{{ $profileData['basic_info']['full_name'] }}</div>
                <div class="profile-role">{{ $profileData['basic_info']['role'] }}</div>

                <div class="mb-3">
                    @if($profileData['email_verified'])
                        <span class="status-badge verified">
                            <i class="fas fa-check-circle"></i>Email Verified
                        </span>
                    @else
                        <span class="status-badge" style="background: #fef3c7; color: #92400e;">
                            <i class="fas fa-exclamation-triangle"></i>Email Not Verified
                        </span>
                    @endif
                </div>

                <button class="status-badge action">
                    <i class="fas fa-camera"></i>Change Avatar
                </button>
            </div>
        </div>

        {{-- Modern Account Summary --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-chart-bar"></i>Account Summary
                </div>

                <div class="summary-item">
                    <span class="summary-label">Member Since</span>
                    <span class="summary-value">{{ $profileData['basic_info']['member_since'] }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Last Login</span>
                    <span class="summary-value">{{ $profileData['basic_info']['last_login'] }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Active Courses</span>
                    <span class="summary-badge count">{{ $ordersData['active_courses'] }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Account Status</span>
                    @if($profileData['is_active'])
                        <span class="summary-badge active">Active</span>
                    @else
                        <span class="summary-badge" style="background: #fecaca; color: #dc2626;">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Modern Basic Information Form --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user-edit"></i>Basic Information
                    </div>

                    <form action="{{ route('account.profile.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('fname') is-invalid @enderror"
                                       id="fname" name="fname"
                                       value="{{ old('fname', $profileData['basic_info']['first_name']) }}" required>
                                @error('fname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('lname') is-invalid @enderror"
                                       id="lname" name="lname"
                                       value="{{ old('lname', $profileData['basic_info']['last_name']) }}" required>
                                @error('lname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email', $profileData['basic_info']['email']) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-modern btn-modern-primary">
                                <i class="fas fa-save"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modern Student Information --}}
        @if(!empty($profileData['student_info']))
        <div class="modern-card">
            <div class="card-body">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap"></i>Student Information
                    </div>

                    <div class="row">
                        @foreach($profileData['student_info'] as $key => $value)
                            <div class="col-md-6 mb-3">
                                <div class="summary-item" style="border-bottom: 1px solid #f1f5f9; padding: 0.75rem 0;">
                                    <span class="summary-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="summary-value">{{ $value ?: 'Not specified' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

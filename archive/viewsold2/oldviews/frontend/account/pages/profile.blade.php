<?php

$student_info = $user->student_info;

$formFields = [
    'fname' => ['label' => 'First Name', 'value' => $user->fname],
    'initials' => ['label' => 'Initial', 'value' => $student_info['initials'] ?? ''],
    'lname' => ['label' => 'Last Name', 'value' => $user->lname],
    'suffix' => ['label' => 'Suffix', 'value' => $student_info['suffix'] ?? ''],
    'email' => ['label' => 'Email', 'value' => $user->email],
    'dob' => ['label' => 'Date of Birth', 'value' => $student_info['dob'] ?? ''],
    'phone' => ['label' => 'Phone', 'value' => $student_info['phone'] ?? ''],
];
?>
<div class="row profile-view">
    <div class="col-lg-12">
        <div class="row align-items-center my-custom-row">
            <div class="col-lg-2">
                <i class="fas fa-user-circle fa-4x my-custom-icon"></i>
            </div>
            <div class="col-lg-10">
                <h2 class="title my-custom-title">{{ __('Edit Profile ') }}</h2>
                <span class="lead my-custom-lead">{{ __('Update your Profile') }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-12">

        <div class="card-body bg-lightgray">
            <form action="{{ route('account.profile.update') }}" class="form" method="POST">
                @csrf
                @method('PUT')

                <?php foreach ($formFields as $fieldName => $fieldDetails): ?>
                <div class="form-group mb-3">
                    <label for="<?php echo htmlspecialchars($fieldName); ?>" class="form-label"><?php echo htmlspecialchars($fieldDetails['label']); ?></label>
                    @if ($fieldName == 'email')
                        <input type="email" class="form-control" id="<?php echo htmlspecialchars($fieldName); ?>" name="<?php echo htmlspecialchars($fieldName); ?>"
                            value="<?php echo htmlspecialchars($fieldDetails['value']); ?>">
                    @elseif($fieldName == 'dob')
                        <input type="date" class="form-control" id="<?php echo htmlspecialchars($fieldName); ?>" name="<?php echo htmlspecialchars($fieldName); ?>"
                            value="<?php echo htmlspecialchars($fieldDetails['value']); ?>">
                    @elseif($fieldName == 'suffix')
                        <select class="form-control" style="width: 340px !important" id="<?php echo htmlspecialchars($fieldName); ?>"
                            name="<?php echo htmlspecialchars($fieldName); ?>">
                            <option value="">Select Suffix</option>
                            <option value="Jr" <?php echo $fieldDetails['value'] == 'Jr' ? 'selected' : ''; ?>>Jr</option>
                            <option value="Sr" <?php echo $fieldDetails['value'] == 'Sr' ? 'selected' : ''; ?>>Sr</option>
                            <option value="I" <?php echo $fieldDetails['value'] == 'I' ? 'selected' : ''; ?>>I</option>
                            <option value="II" <?php echo $fieldDetails['value'] == 'II' ? 'selected' : ''; ?>>II</option>
                            <option value="III" <?php echo $fieldDetails['value'] == 'III' ? 'selected' : ''; ?>>III</option>
                            <option value="IV" <?php echo $fieldDetails['value'] == 'IV' ? 'selected' : ''; ?>>IV</option>
                            <option value="V" <?php echo $fieldDetails['value'] == 'V' ? 'selected' : ''; ?>>V</option>
                        </select>
                    @elseif($fieldName == 'phone')
                        <input type="text" class="form-control" id="<?php echo htmlspecialchars($fieldName); ?>" name="<?php echo htmlspecialchars($fieldName); ?>"
                            value="<?php echo htmlspecialchars($fieldDetails['value']); ?>">
                    @else
                        <input type="text" class="form-control" id="<?php echo htmlspecialchars($fieldName); ?>" name="<?php echo htmlspecialchars($fieldName); ?>"
                            value="<?php echo htmlspecialchars($fieldDetails['value']); ?>">
                    @endif
                </div>
                <?php endforeach; ?>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

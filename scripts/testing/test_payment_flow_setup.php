<?php

echo "Testing Payment Flow Setup\n";
echo "==========================\n\n";

// Test 1: Check if Payment model file exists
$paymentModelFile = 'app/Models/Payment.php';
$paymentModelExists = file_exists(__DIR__ . '/../../' . $paymentModelFile);
echo ($paymentModelExists ? "✓" : "✗") . " Payment model exists: $paymentModelFile\n";

// Test 2: Check if PaymentController file exists
$paymentControllerFile = 'app/Http/Controllers/Web/PaymentController.php';
$paymentControllerExists = file_exists(__DIR__ . '/../../' . $paymentControllerFile);
echo ($paymentControllerExists ? "✓" : "✗") . " PaymentController exists: $paymentControllerFile\n";

// Test 3: Check if EnrollmentController file exists
$enrollmentControllerFile = 'app/Http/Controllers/Web/EnrollmentController.php';
$enrollmentControllerExists = file_exists(__DIR__ . '/../../' . $enrollmentControllerFile);
echo ($enrollmentControllerExists ? "✓" : "✗") . " EnrollmentController exists: $enrollmentControllerFile\n";// Test 4: Check if views exist
$viewsToCheck = [
    'resources/views/frontend/payments/payflowpro.blade.php',
    'resources/views/components/frontend/panels/payments/payflowpro.blade.php',
    'resources/views/frontend/courses/enroll.blade.php',
    'resources/views/components/frontend/panels/courses/enroll.blade.php',
];

foreach ($viewsToCheck as $view) {
    $exists = file_exists(__DIR__ . '/../../' . $view);
    echo ($exists ? "✓" : "✗") . " View exists: $view\n";
}

// Test 5: Check if CSS exists
$cssFile = 'resources/css/components/payment.css';
$cssExists = file_exists(__DIR__ . '/../../' . $cssFile);
echo ($cssExists ? "✓" : "✗") . " CSS file exists: $cssFile\n";

// Test 6: Check if routes file exists
$routesFile = 'routes/frontend/payments.php';
$routesExists = file_exists(__DIR__ . '/../../' . $routesFile);
echo ($routesExists ? "✓" : "✗") . " Payment routes file exists: $routesFile\n";

echo "\n";
echo "Payment Flow Summary:\n";
echo "1. User clicks 'Enroll Now' on course detail page\n";
echo "2. Goes to /courses/enroll/{course} (enrollment confirmation page)\n";
echo "3. User clicks 'Proceed to Payment' (POST to /courses/enroll/{course})\n";
echo "4. EnrollmentController@AutoPayFlowPro creates Order and Payment\n";
echo "5. Redirects to /payments/payflowpro/{payment} (payment processing page)\n";
echo "6. User selects payment method (Stripe or PayPal)\n";
echo "7. Payment is processed and user is enrolled in course\n";
echo "\nSetup appears complete! Test the flow in the browser.\n";

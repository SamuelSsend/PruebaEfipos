<?php
require_once 'vendor/autoload.php';
 
$stripe = array(
    'secret_key' => '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',
    'publishable_key' => 'pk_live_51PiC3vFu6OKWgJ6CcFgPeTC0yrMZHKuUgpRuyr7nI0XGZv59xM5Ynn4hU6K6p7H5QyyAk5M5cUn9LtvnYwmC9Dwy00obYjAvNT',
);
 
\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>
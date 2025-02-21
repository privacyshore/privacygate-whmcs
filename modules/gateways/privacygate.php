<?php
require_once __DIR__ . '/PrivacyGate/vendor/autoload.php';
require_once __DIR__ . '/PrivacyGate/const.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function privacygate_MetaData()
{
    return array(
        'DisplayName' => 'PrivacyGate',
        'APIVersion' => '1.1',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false
    );
}

function privacygate_config()
{
    // Global variable required
    global $customadminpath;

    // Build callback URL.
    $isHttps = (isset ($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

    $protocol = $isHttps ? "https://" : "http://";
    $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $url = substr($url, 0, strpos($url, $customadminpath));
    $callbackUrl = $url . "modules/gateways/callback/privacygate.php";

    $webhookDescription = "<p>Please copy/paste <b>$callbackUrl</b> url in <a href=\"https://dash.privacygate.io/settings\" target=\"_blank\">Settings &gt; Webhook subscriptions &gt; Add an endpoint</a></p>";

    if (!$isHttps) {
        $webhookDescription .= '<p style="color:red;">Please activate ssl for webhook notifications!!!</p>';
    }

    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'PrivacyGate <a href=“https://dash.privacygate.io/” target=“_blank” rel=“noopener”>(Learn more)</a>'
        ),
        'apiKey' => array(
            'FriendlyName' => 'API Key',
            'Description' => 'Get API Key <a href="https://dash.privacygate.io/settings" target="_blank">Settings &gt; API keys &gt; Create an API key</a>',
            'Type' => 'text'
        ),
        'secretKey' => array(
            'FriendlyName' => 'Shared Secret',
            'Description' => 'Get the Shared Key <a href="https://dash.privacygate.io/settings" target="_blank">Settings &gt; Show Shared Secrets</a>',
            'Type' => 'text'
        ),
        'webhookUrl' => array(
            'FriendlyName' => 'Webhook subscription url',
            'Type' => '',
            'Size' => '',
            'Default' => '',
            'Description' => $webhookDescription
        ),
        'readme' => array(
            'FriendlyName' => '',
            'Type' => '',
            'Size' => '',
            'Default' => '',
            'Description' => 'Read the readme.txt file for instructions on how to use this module'
        )
    );
}

function privacygate_link($params)
{
    if (!isset($params) || empty($params)) {
        die('Missing or invalid $params data.');
    }

    $description = '';

    try {
        $description = Capsule::table('tblinvoiceitems')
            ->where("invoiceid", "=", $params['invoiceid'])
            ->value('description');
        // Truncate descriptions longer than 200 per Commerce API requirements
        $description = (strlen($description) > 200) ? substr($description,0,197).'...' : $description;
    } catch (Exception $e) {
    }

    $chargeData = array(
        'local_price' => array(
            'amount' => $params['amount'],
            'currency' => $params['currency']
        ),
        'pricing_type' => 'fixed_price',
        'name' => $params['description'],
        'description' => empty($description) ? $params['description'] : $description,
        'metadata' => [
            METADATA_SOURCE_PARAM => METADATA_SOURCE_VALUE,
            METADATA_INVOICE_PARAM => $params['invoiceid'],
            METADATA_CLIENT_PARAM => $params['clientdetails']['userid'],
            'firstName' => isset($params['clientdetails']['firstname']) ? $params['clientdetails']['firstname'] : null,
            'lastName' => isset($params['clientdetails']['lastname']) ? $params['clientdetails']['lastname'] : null,
            'email' => isset($params['clientdetails']['email']) ? $params['clientdetails']['email'] : null
        ],
        'redirect_url' => $params['returnurl'] . "&paymentsuccess=true",
        'cancel_url' => $params['returnurl'] . "&paymentfailed=true"
    );

    \PrivacyGate\ApiClient::init($params['apiKey']);
    $chargeObj = \PrivacyGate\Resources\Charge::create($chargeData);

    $form = '<form action="' . $chargeObj->hosted_url . '" method="GET">';
    $form .= '<input type="submit" value="' . $params['langpaynow'] . '" />';
    $form .= '</form>';

    return $form;
}

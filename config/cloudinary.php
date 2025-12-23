<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | An HTTP or HTTPS URL to notify your application (a webhook) when the 
    | process of uploads, deletes, and any API that accepts notification_url 
    | has completed.
    |
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Cloudinary settings. Cloudinary is a cloud 
    | hosted media management service for all file uploads, storage, 
    | manipulations and delivery.
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    /**
     * Upload Preset From Cloudinary Dashboard
     */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /**
     * Route to get cloud_image_url for a file
     */
    'upload_route' => 'cloudinary/upload',

    /**
     * Cloud name from Cloudinary Dashboard
     */
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),

    /**
     * API Key from Cloudinary Dashboard
     */
    'api_key' => env('CLOUDINARY_API_KEY'),

    /**
     * API Secret from Cloudinary Dashboard
     */
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    /**
     * Secure (HTTPS) delivery URL
     */
    'secure' => true,
];

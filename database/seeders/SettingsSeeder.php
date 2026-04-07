<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ═══════════════════════════════════════════
            // GENERAL SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'general', 'key' => 'site_name', 'value' => 'AutoScout24', 'type' => 'string', 'label' => 'Site Name', 'description' => 'The name of your marketplace', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Europe\'s largest online car marketplace – Find, buy and sell new and used cars', 'type' => 'text', 'label' => 'Site Description', 'description' => 'Short site description for meta tags', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Your way to the perfect car', 'type' => 'string', 'label' => 'Tagline', 'description' => 'Displayed on homepage', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'general', 'key' => 'site_url', 'value' => 'https://www.autoscout24safetrade.com', 'type' => 'string', 'label' => 'Site URL', 'description' => 'Frontend application URL', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'general', 'key' => 'contact_email', 'value' => 'info@autoscout24safetrade.com', 'type' => 'string', 'label' => 'Contact Email', 'description' => 'Primary contact email', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'general', 'key' => 'support_email', 'value' => 'support@autoscout24safetrade.com', 'type' => 'string', 'label' => 'Support Email', 'description' => 'Support email address', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'general', 'key' => 'contact_phone', 'value' => '+49 89 444 56 0', 'type' => 'string', 'label' => 'Phone', 'description' => 'Contact phone number', 'is_public' => true, 'sort_order' => 7],
            ['group' => 'general', 'key' => 'contact_address', 'value' => 'Bothestraße 11-15, 81675 München, Germany', 'type' => 'text', 'label' => 'Address', 'description' => 'Physical address', 'is_public' => true, 'sort_order' => 8],
            ['group' => 'general', 'key' => 'default_locale', 'value' => 'en', 'type' => 'string', 'label' => 'Default Language', 'description' => 'Default interface language', 'is_public' => true, 'sort_order' => 9, 'options' => json_encode([['value' => 'en', 'label' => 'English'], ['value' => 'de', 'label' => 'Deutsch'], ['value' => 'fr', 'label' => 'Français'], ['value' => 'it', 'label' => 'Italiano'], ['value' => 'nl', 'label' => 'Nederlands'], ['value' => 'es', 'label' => 'Español'], ['value' => 'ro', 'label' => 'Română']])],
            ['group' => 'general', 'key' => 'default_currency', 'value' => 'EUR', 'type' => 'string', 'label' => 'Default Currency', 'description' => 'Default pricing currency', 'is_public' => true, 'sort_order' => 10],
            ['group' => 'general', 'key' => 'default_country', 'value' => 'DE', 'type' => 'string', 'label' => 'Default Country', 'description' => 'Default country', 'is_public' => true, 'sort_order' => 11],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Europe/Berlin', 'type' => 'string', 'label' => 'Timezone', 'description' => 'Application timezone', 'is_public' => false, 'sort_order' => 12],
            ['group' => 'general', 'key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'User Registration', 'description' => 'Allow new registrations', 'is_public' => true, 'sort_order' => 13],
            ['group' => 'general', 'key' => 'dealer_registration_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Dealer Registration', 'description' => 'Allow dealer sign-up', 'is_public' => true, 'sort_order' => 14],
            ['group' => 'general', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'description' => 'Show maintenance page', 'is_public' => true, 'sort_order' => 15],
            ['group' => 'general', 'key' => 'dark_mode_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Dark Mode', 'description' => 'Enable dark mode', 'is_public' => true, 'sort_order' => 16],

            // ═══════════════════════════════════════════
            // SAFETRADE SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'safetrade', 'key' => 'enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Enable SafeTrade', 'description' => 'Enable the SafeTrade escrow system', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'safetrade', 'key' => 'escrow_fee_percent', 'value' => '2.5', 'type' => 'float', 'label' => 'Escrow Fee %', 'description' => 'Fee percentage for escrow protection', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'safetrade', 'key' => 'min_transaction_amount', 'value' => '500', 'type' => 'integer', 'label' => 'Min Transaction Amount', 'description' => 'Minimum amount for SafeTrade', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'safetrade', 'key' => 'max_transaction_amount', 'value' => '500000', 'type' => 'integer', 'label' => 'Max Transaction Amount', 'description' => 'Maximum transaction (0=unlimited)', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'safetrade', 'key' => 'payment_deadline_hours', 'value' => '48', 'type' => 'integer', 'label' => 'Payment Deadline', 'description' => 'Hours to complete payment', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'safetrade', 'key' => 'delivery_deadline_days', 'value' => '14', 'type' => 'integer', 'label' => 'Delivery Deadline', 'description' => 'Days to deliver vehicle', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'safetrade', 'key' => 'inspection_period_days', 'value' => '3', 'type' => 'integer', 'label' => 'Inspection Period', 'description' => 'Days for buyer inspection', 'is_public' => true, 'sort_order' => 7],
            ['group' => 'safetrade', 'key' => 'auto_release_days', 'value' => '7', 'type' => 'integer', 'label' => 'Auto-Release Days', 'description' => 'Days before auto-release', 'is_public' => true, 'sort_order' => 8],
            ['group' => 'safetrade', 'key' => 'disputes_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Enable Disputes', 'description' => 'Allow dispute filing', 'is_public' => true, 'sort_order' => 9],
            ['group' => 'safetrade', 'key' => 'dispute_deadline_days', 'value' => '14', 'type' => 'integer', 'label' => 'Dispute Deadline', 'description' => 'Days to file dispute after delivery', 'is_public' => true, 'sort_order' => 10],
            ['group' => 'safetrade', 'key' => 'dispute_resolution_days', 'value' => '30', 'type' => 'integer', 'label' => 'Resolution Timeframe', 'description' => 'Target days to resolve', 'is_public' => false, 'sort_order' => 11],
            ['group' => 'safetrade', 'key' => 'max_evidence_files', 'value' => '10', 'type' => 'integer', 'label' => 'Max Evidence Files', 'description' => 'Files per evidence submission', 'is_public' => false, 'sort_order' => 12],
            ['group' => 'safetrade', 'key' => 'bank_transfer_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Bank Transfer', 'description' => 'Accept bank transfers', 'is_public' => true, 'sort_order' => 13],
            ['group' => 'safetrade', 'key' => 'paypal_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'PayPal', 'description' => 'Accept PayPal', 'is_public' => true, 'sort_order' => 14],
            ['group' => 'safetrade', 'key' => 'stripe_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Stripe', 'description' => 'Accept Stripe', 'is_public' => true, 'sort_order' => 15],
            ['group' => 'safetrade', 'key' => 'escrow_bank_name', 'value' => 'AutoScout24 SafeTrade GmbH', 'type' => 'string', 'label' => 'Escrow Bank Name', 'description' => 'Displayed to buyers', 'is_public' => true, 'sort_order' => 16],
            ['group' => 'safetrade', 'key' => 'escrow_iban', 'value' => 'DE89 3704 0044 0532 0130 00', 'type' => 'string', 'label' => 'Escrow IBAN', 'description' => 'Escrow account IBAN', 'is_public' => true, 'sort_order' => 17],
            ['group' => 'safetrade', 'key' => 'escrow_bic', 'value' => 'COBADEFFXXX', 'type' => 'string', 'label' => 'Escrow BIC', 'description' => 'Escrow account BIC', 'is_public' => true, 'sort_order' => 18],
            ['group' => 'safetrade', 'key' => 'escrow_account_holder', 'value' => 'AutoScout24 GmbH — Escrow Services', 'type' => 'string', 'label' => 'Escrow Account Holder', 'description' => 'Account holder name displayed to buyers', 'is_public' => true, 'sort_order' => 19],

            // ═══════════════════════════════════════════
            // SEO SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'AutoScout24 - Buy & Sell Cars Online | Europe\'s Marketplace', 'type' => 'string', 'label' => 'Meta Title', 'description' => 'Default page title', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Find your perfect car on AutoScout24. Browse thousands of new and used vehicles from private sellers and dealers across Europe.', 'type' => 'text', 'label' => 'Meta Description', 'description' => 'Default meta description', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'seo', 'key' => 'meta_keywords', 'value' => '["cars","vehicles","buy cars","sell cars","used cars","new cars","autoscout24","europe"]', 'type' => 'json', 'label' => 'Meta Keywords', 'description' => 'Default meta keywords', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'seo', 'key' => 'og_image', 'value' => '/og-image.jpg', 'type' => 'string', 'label' => 'OG Image', 'description' => 'Social sharing image', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'seo', 'key' => 'organization_name', 'value' => 'AutoScout24 GmbH', 'type' => 'string', 'label' => 'Organization', 'description' => 'Schema.org organization', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'seo', 'key' => 'organization_logo', 'value' => '/logo.svg', 'type' => 'string', 'label' => 'Org Logo', 'description' => 'Schema.org logo URL', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'seo', 'key' => 'structured_data_extra', 'value' => null, 'type' => 'text', 'label' => 'Extra JSON-LD', 'description' => 'Additional structured data', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'seo', 'key' => 'google_analytics_id', 'value' => null, 'type' => 'string', 'label' => 'GA4 ID', 'description' => 'Google Analytics 4 ID', 'is_public' => true, 'sort_order' => 8],
            ['group' => 'seo', 'key' => 'google_tag_manager_id', 'value' => null, 'type' => 'string', 'label' => 'GTM ID', 'description' => 'Google Tag Manager ID', 'is_public' => true, 'sort_order' => 9],
            ['group' => 'seo', 'key' => 'facebook_pixel_id', 'value' => null, 'type' => 'string', 'label' => 'FB Pixel', 'description' => 'Facebook Pixel ID', 'is_public' => true, 'sort_order' => 10],
            ['group' => 'seo', 'key' => 'hotjar_id', 'value' => null, 'type' => 'string', 'label' => 'Hotjar ID', 'description' => 'Hotjar site ID', 'is_public' => true, 'sort_order' => 11],
            ['group' => 'seo', 'key' => 'robots_index', 'value' => '1', 'type' => 'boolean', 'label' => 'Allow Indexing', 'description' => 'Allow search engine indexing', 'is_public' => true, 'sort_order' => 12],
            ['group' => 'seo', 'key' => 'sitemap_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Sitemap', 'description' => 'Generate XML sitemap', 'is_public' => false, 'sort_order' => 13],
            ['group' => 'seo', 'key' => 'robots_extra', 'value' => null, 'type' => 'text', 'label' => 'Robots Extra', 'description' => 'Extra robots.txt rules', 'is_public' => false, 'sort_order' => 14],

            // ═══════════════════════════════════════════
            // EMAIL SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'email', 'key' => 'from_name', 'value' => 'AutoScout24', 'type' => 'string', 'label' => 'Sender Name', 'description' => 'Email sender name', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'email', 'key' => 'from_email', 'value' => 'noreply@autoscout24safetrade.com', 'type' => 'string', 'label' => 'Sender Email', 'description' => 'From email address', 'is_public' => false, 'sort_order' => 2],
            ['group' => 'email', 'key' => 'reply_to', 'value' => 'support@autoscout24safetrade.com', 'type' => 'string', 'label' => 'Reply-To', 'description' => 'Reply-to email', 'is_public' => false, 'sort_order' => 3],
            ['group' => 'email', 'key' => 'mail_driver', 'value' => 'smtp', 'type' => 'string', 'label' => 'Mail Driver', 'description' => 'Email sending driver', 'is_public' => false, 'sort_order' => 4],
            ['group' => 'email', 'key' => 'logo_url', 'value' => '/logo.svg', 'type' => 'string', 'label' => 'Email Logo', 'description' => 'Logo in email header', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'email', 'key' => 'primary_color', 'value' => '#FF6B00', 'type' => 'color', 'label' => 'Email Color', 'description' => 'Email template primary color', 'is_public' => false, 'sort_order' => 6],
            ['group' => 'email', 'key' => 'email_footer', 'value' => '© 2026 AutoScout24 GmbH. All rights reserved.', 'type' => 'text', 'label' => 'Footer Text', 'description' => 'Email footer text', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'email', 'key' => 'email_signature', 'value' => 'Best regards, The AutoScout24 Team', 'type' => 'text', 'label' => 'Signature', 'description' => 'Email signature', 'is_public' => false, 'sort_order' => 8],
            ['group' => 'email', 'key' => 'notify_new_registration', 'value' => '1', 'type' => 'boolean', 'label' => 'New Registration', 'description' => 'Alert on new user', 'is_public' => false, 'sort_order' => 9],
            ['group' => 'email', 'key' => 'notify_new_listing', 'value' => '0', 'type' => 'boolean', 'label' => 'New Listing', 'description' => 'Alert on new vehicle', 'is_public' => false, 'sort_order' => 10],
            ['group' => 'email', 'key' => 'notify_new_transaction', 'value' => '1', 'type' => 'boolean', 'label' => 'New Transaction', 'description' => 'Alert on SafeTrade tx', 'is_public' => false, 'sort_order' => 11],
            ['group' => 'email', 'key' => 'notify_new_dispute', 'value' => '1', 'type' => 'boolean', 'label' => 'New Dispute', 'description' => 'Alert on dispute', 'is_public' => false, 'sort_order' => 12],
            ['group' => 'email', 'key' => 'notify_contact_messages', 'value' => '1', 'type' => 'boolean', 'label' => 'Contact Messages', 'description' => 'Alert on contact form', 'is_public' => false, 'sort_order' => 13],
            ['group' => 'email', 'key' => 'admin_notification_email', 'value' => 'admin@autoscout24safetrade.com', 'type' => 'string', 'label' => 'Admin Email', 'description' => 'Where admin alerts go', 'is_public' => false, 'sort_order' => 14],
            ['group' => 'email', 'key' => 'max_emails_per_hour', 'value' => '20', 'type' => 'integer', 'label' => 'Max Emails/Hour', 'description' => 'Rate limit per user', 'is_public' => false, 'sort_order' => 15],
            ['group' => 'email', 'key' => 'verification_resend_cooldown', 'value' => '5', 'type' => 'integer', 'label' => 'Resend Cooldown', 'description' => 'Minutes between resends', 'is_public' => false, 'sort_order' => 16],

            // ═══════════════════════════════════════════
            // LISTING SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'listings', 'key' => 'per_page', 'value' => '20', 'type' => 'integer', 'label' => 'Per Page', 'description' => 'Listings per page', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'listings', 'key' => 'max_vehicle_images', 'value' => '20', 'type' => 'integer', 'label' => 'Max Images', 'description' => 'Max images per vehicle', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'listings', 'key' => 'max_image_size_mb', 'value' => '5', 'type' => 'integer', 'label' => 'Max Image Size', 'description' => 'Max MB per image', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'listings', 'key' => 'listing_duration_days', 'value' => '90', 'type' => 'integer', 'label' => 'Listing Duration', 'description' => 'Days listing is active', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'listings', 'key' => 'auto_approve_listings', 'value' => '1', 'type' => 'boolean', 'label' => 'Auto-Approve', 'description' => 'Skip manual review', 'is_public' => false, 'sort_order' => 5],
            ['group' => 'listings', 'key' => 'show_phone_numbers', 'value' => '1', 'type' => 'boolean', 'label' => 'Show Phone', 'description' => 'Display seller phone', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'listings', 'key' => 'enable_video_uploads', 'value' => '1', 'type' => 'boolean', 'label' => 'Video Uploads', 'description' => 'Allow video URLs', 'is_public' => true, 'sort_order' => 7],
            ['group' => 'listings', 'key' => 'enable_price_negotiation', 'value' => '1', 'type' => 'boolean', 'label' => 'Negotiation', 'description' => 'Allow price offers', 'is_public' => true, 'sort_order' => 8],
            ['group' => 'listings', 'key' => 'enable_test_drives', 'value' => '1', 'type' => 'boolean', 'label' => 'Test Drives', 'description' => 'Allow test drive requests', 'is_public' => true, 'sort_order' => 9],
            ['group' => 'listings', 'key' => 'enable_price_alerts', 'value' => '1', 'type' => 'boolean', 'label' => 'Price Alerts', 'description' => 'Allow price drop alerts', 'is_public' => true, 'sort_order' => 10],
            ['group' => 'listings', 'key' => 'enable_saved_searches', 'value' => '1', 'type' => 'boolean', 'label' => 'Saved Searches', 'description' => 'Allow saved searches', 'is_public' => true, 'sort_order' => 11],
            ['group' => 'listings', 'key' => 'enable_vehicle_comparison', 'value' => '1', 'type' => 'boolean', 'label' => 'Comparison', 'description' => 'Vehicle comparison tool', 'is_public' => true, 'sort_order' => 12],
            ['group' => 'listings', 'key' => 'enable_featured', 'value' => '1', 'type' => 'boolean', 'label' => 'Featured Listings', 'description' => 'Enable promoted listings', 'is_public' => true, 'sort_order' => 13],
            ['group' => 'listings', 'key' => 'featured_price', 'value' => '9.99', 'type' => 'float', 'label' => 'Featured Price', 'description' => 'Cost to feature listing', 'is_public' => true, 'sort_order' => 14],
            ['group' => 'listings', 'key' => 'featured_duration_days', 'value' => '7', 'type' => 'integer', 'label' => 'Featured Duration', 'description' => 'Days listing is featured', 'is_public' => true, 'sort_order' => 15],
            ['group' => 'listings', 'key' => 'max_featured_per_user', 'value' => '5', 'type' => 'integer', 'label' => 'Max Featured', 'description' => 'Per user limit', 'is_public' => false, 'sort_order' => 16],
            ['group' => 'listings', 'key' => 'search_radius_km', 'value' => '100', 'type' => 'integer', 'label' => 'Search Radius', 'description' => 'Default km radius', 'is_public' => true, 'sort_order' => 17],
            ['group' => 'listings', 'key' => 'similar_vehicles_count', 'value' => '6', 'type' => 'integer', 'label' => 'Similar Count', 'description' => 'Similar vehicles shown', 'is_public' => true, 'sort_order' => 18],
            ['group' => 'listings', 'key' => 'enable_location_search', 'value' => '1', 'type' => 'boolean', 'label' => 'Location Search', 'description' => 'Enable geo search', 'is_public' => true, 'sort_order' => 19],

            // ═══════════════════════════════════════════
            // APPEARANCE SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'appearance', 'key' => 'logo_url', 'value' => '/logo.svg', 'type' => 'string', 'label' => 'Logo', 'description' => 'Header logo URL', 'is_public' => true, 'sort_order' => 1],
            ['group' => 'appearance', 'key' => 'logo_dark_url', 'value' => '/logo-dark.svg', 'type' => 'string', 'label' => 'Dark Logo', 'description' => 'Dark mode logo', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'appearance', 'key' => 'favicon_url', 'value' => '/favicon.ico', 'type' => 'string', 'label' => 'Favicon', 'description' => 'Browser tab icon', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'appearance', 'key' => 'primary_color', 'value' => '#FF6B00', 'type' => 'color', 'label' => 'Primary Color', 'description' => 'Main brand color', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'appearance', 'key' => 'secondary_color', 'value' => '#1F2937', 'type' => 'color', 'label' => 'Secondary Color', 'description' => 'Secondary brand color', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'appearance', 'key' => 'accent_color', 'value' => '#3B82F6', 'type' => 'color', 'label' => 'Accent Color', 'description' => 'Highlight color', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'appearance', 'key' => 'hero_title', 'value' => 'Find Your Perfect Car', 'type' => 'string', 'label' => 'Hero Title', 'description' => 'Homepage headline', 'is_public' => true, 'sort_order' => 7],
            ['group' => 'appearance', 'key' => 'hero_subtitle', 'value' => 'Search over 2 million vehicles from trusted dealers and private sellers across Europe', 'type' => 'text', 'label' => 'Hero Subtitle', 'description' => 'Homepage subtitle', 'is_public' => true, 'sort_order' => 8],
            ['group' => 'appearance', 'key' => 'hero_image_url', 'value' => '/hero-bg.jpg', 'type' => 'string', 'label' => 'Hero Image', 'description' => 'Hero background image', 'is_public' => true, 'sort_order' => 9],
            ['group' => 'appearance', 'key' => 'hero_cta_text', 'value' => 'Search Vehicles', 'type' => 'string', 'label' => 'CTA Text', 'description' => 'CTA button text', 'is_public' => true, 'sort_order' => 10],
            ['group' => 'appearance', 'key' => 'hero_cta_url', 'value' => '/search', 'type' => 'string', 'label' => 'CTA Link', 'description' => 'CTA button URL', 'is_public' => true, 'sort_order' => 11],
            ['group' => 'appearance', 'key' => 'footer_text', 'value' => '© 2026 AutoScout24 GmbH. All rights reserved.', 'type' => 'text', 'label' => 'Footer Text', 'description' => 'Footer copyright', 'is_public' => true, 'sort_order' => 12],
            ['group' => 'appearance', 'key' => 'footer_facebook', 'value' => 'https://facebook.com/autoscout24', 'type' => 'string', 'label' => 'Facebook', 'description' => 'Facebook URL', 'is_public' => true, 'sort_order' => 13],
            ['group' => 'appearance', 'key' => 'footer_twitter', 'value' => 'https://twitter.com/autoscout24', 'type' => 'string', 'label' => 'Twitter', 'description' => 'Twitter URL', 'is_public' => true, 'sort_order' => 14],
            ['group' => 'appearance', 'key' => 'footer_instagram', 'value' => 'https://instagram.com/autoscout24', 'type' => 'string', 'label' => 'Instagram', 'description' => 'Instagram URL', 'is_public' => true, 'sort_order' => 15],
            ['group' => 'appearance', 'key' => 'footer_linkedin', 'value' => null, 'type' => 'string', 'label' => 'LinkedIn', 'description' => 'LinkedIn URL', 'is_public' => true, 'sort_order' => 16],
            ['group' => 'appearance', 'key' => 'footer_youtube', 'value' => null, 'type' => 'string', 'label' => 'YouTube', 'description' => 'YouTube URL', 'is_public' => true, 'sort_order' => 17],
            ['group' => 'appearance', 'key' => 'custom_css', 'value' => null, 'type' => 'text', 'label' => 'Custom CSS', 'description' => 'Injected CSS', 'is_public' => true, 'sort_order' => 18],
            ['group' => 'appearance', 'key' => 'custom_head_scripts', 'value' => null, 'type' => 'text', 'label' => 'Head Scripts', 'description' => 'Scripts in <head>', 'is_public' => true, 'sort_order' => 19],
            ['group' => 'appearance', 'key' => 'custom_body_scripts', 'value' => null, 'type' => 'text', 'label' => 'Body Scripts', 'description' => 'Scripts before </body>', 'is_public' => true, 'sort_order' => 20],

            // ═══════════════════════════════════════════
            // SECURITY SETTINGS
            // ═══════════════════════════════════════════
            ['group' => 'security', 'key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean', 'label' => 'Email Verification', 'description' => 'Require verified email', 'is_public' => false, 'sort_order' => 1],
            ['group' => 'security', 'key' => 'enable_two_factor', 'value' => '0', 'type' => 'boolean', 'label' => '2FA', 'description' => 'Enable 2FA option', 'is_public' => true, 'sort_order' => 2],
            ['group' => 'security', 'key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'label' => 'Min Password', 'description' => 'Minimum password length', 'is_public' => true, 'sort_order' => 3],
            ['group' => 'security', 'key' => 'password_require_uppercase', 'value' => '1', 'type' => 'boolean', 'label' => 'Require Uppercase', 'description' => 'Require uppercase letter', 'is_public' => true, 'sort_order' => 4],
            ['group' => 'security', 'key' => 'password_require_number', 'value' => '1', 'type' => 'boolean', 'label' => 'Require Number', 'description' => 'Require a number', 'is_public' => true, 'sort_order' => 5],
            ['group' => 'security', 'key' => 'password_require_special', 'value' => '0', 'type' => 'boolean', 'label' => 'Require Special', 'description' => 'Require special character', 'is_public' => true, 'sort_order' => 6],
            ['group' => 'security', 'key' => 'login_max_attempts', 'value' => '5', 'type' => 'integer', 'label' => 'Max Login Attempts', 'description' => 'Before lockout', 'is_public' => false, 'sort_order' => 7],
            ['group' => 'security', 'key' => 'login_lockout_minutes', 'value' => '15', 'type' => 'integer', 'label' => 'Lockout Minutes', 'description' => 'Lockout duration', 'is_public' => false, 'sort_order' => 8],
            ['group' => 'security', 'key' => 'api_rate_limit', 'value' => '60', 'type' => 'integer', 'label' => 'API Rate Limit', 'description' => 'Requests per minute', 'is_public' => false, 'sort_order' => 9],
            ['group' => 'security', 'key' => 'contact_form_rate', 'value' => '5', 'type' => 'integer', 'label' => 'Contact Rate', 'description' => 'Submissions per hour', 'is_public' => false, 'sort_order' => 10],
            ['group' => 'security', 'key' => 'cookie_consent_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Cookie Consent', 'description' => 'Show GDPR banner', 'is_public' => true, 'sort_order' => 11],
            ['group' => 'security', 'key' => 'data_export_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Data Export', 'description' => 'GDPR data export', 'is_public' => true, 'sort_order' => 12],
            ['group' => 'security', 'key' => 'account_deletion_enabled', 'value' => '1', 'type' => 'boolean', 'label' => 'Account Deletion', 'description' => 'Allow account deletion', 'is_public' => true, 'sort_order' => 13],
            ['group' => 'security', 'key' => 'data_retention_days', 'value' => '0', 'type' => 'integer', 'label' => 'Data Retention', 'description' => 'Days (0=forever)', 'is_public' => false, 'sort_order' => 14],
            ['group' => 'security', 'key' => 'privacy_policy_url', 'value' => '/privacy', 'type' => 'string', 'label' => 'Privacy Policy', 'description' => 'Privacy policy link', 'is_public' => true, 'sort_order' => 15],
            ['group' => 'security', 'key' => 'terms_of_service_url', 'value' => '/terms', 'type' => 'string', 'label' => 'Terms of Service', 'description' => 'ToS link', 'is_public' => true, 'sort_order' => 16],
            ['group' => 'security', 'key' => 'session_lifetime_minutes', 'value' => '120', 'type' => 'integer', 'label' => 'Session Lifetime', 'description' => 'Minutes before expiry', 'is_public' => false, 'sort_order' => 17],
            ['group' => 'security', 'key' => 'token_expiry_days', 'value' => '30', 'type' => 'integer', 'label' => 'Token Expiry', 'description' => 'API token lifespan', 'is_public' => false, 'sort_order' => 18],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Seeded ' . count($settings) . ' application settings across ' . collect($settings)->pluck('group')->unique()->count() . ' groups.');
    }
}

<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Secure Vehicle Trading',
    
    // Vehicle Listing
    'listing' => [
        'title' => 'Vehicle Listing',
        'create' => 'Create Listing',
        'edit' => 'Edit Listing',
        'publish' => 'Publish Listing',
        'deactivate' => 'Deactivate Listing',
        'status' => [
            'draft' => 'Draft',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'sold' => 'Sold',
            'expired' => 'Expired',
        ],
        'fields' => [
            'make' => 'Make',
            'model' => 'Model',
            'year' => 'Year',
            'mileage' => 'Mileage',
            'fuel_type' => 'Fuel Type',
            'transmission' => 'Transmission',
            'vin' => 'VIN',
            'registration' => 'Registration Number',
            'exterior_color' => 'Exterior Color',
            'interior_color' => 'Interior Color',
            'condition' => 'Condition',
            'description' => 'Description',
            'min_price' => 'Minimum Price',
            'max_price' => 'Maximum Price',
            'location' => 'Location',
        ],
        'condition' => [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transaction',
        'reference' => 'Reference',
        'status' => [
            'pending_payment' => 'Awaiting Payment',
            'payment_uploaded' => 'Payment Uploaded',
            'payment_verified' => 'Payment Verified',
            'vehicle_handed_over' => 'Vehicle Handed Over',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'disputed' => 'Disputed',
        ],
        'actions' => [
            'upload_payment' => 'Upload Payment Proof',
            'confirm_handover' => 'Confirm Handover',
            'cancel' => 'Cancel Transaction',
            'open_dispute' => 'Open Dispute',
        ],
        'payment' => [
            'due_date' => 'Payment Due',
            'bank_transfer' => 'Bank Transfer',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Bank Details',
            'bank_name' => 'Bank Name',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Account Holder',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Vehicle Pickup',
        'propose_dates' => 'Propose Pickup Dates',
        'confirm_date' => 'Confirm Pickup Date',
        'reschedule' => 'Reschedule Pickup',
        'status' => [
            'pending' => 'Pending',
            'dates_proposed' => 'Dates Proposed',
            'confirmed' => 'Confirmed',
            'rescheduled' => 'Rescheduled',
            'completed' => 'Completed',
        ],
        'fields' => [
            'address' => 'Pickup Address',
            'phone' => 'Contact Phone',
            'date' => 'Pickup Date',
            'notes' => 'Notes',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Dispute',
        'open' => 'Open Dispute',
        'reference' => 'Dispute Reference',
        'status' => [
            'open' => 'Open',
            'under_review' => 'Under Review',
            'awaiting_info' => 'Awaiting Information',
            'escalated' => 'Escalated',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ],
        'types' => [
            'payment_not_received' => 'Payment Not Received',
            'vehicle_not_as_described' => 'Vehicle Not As Described',
            'delivery_issue' => 'Delivery Issue',
            'documentation_problem' => 'Documentation Problem',
            'fraud' => 'Suspected Fraud',
            'other' => 'Other',
        ],
        'priority' => [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ],
        'resolution' => [
            'in_favor_seller' => 'Resolved in Favor of Seller',
            'in_favor_dealer' => 'Resolved in Favor of Dealer',
            'mutual_agreement' => 'Mutual Agreement',
            'cancelled' => 'Cancelled',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Payment of :amount is due by :date',
        'payment_verified' => 'Payment has been verified',
        'pickup_proposed' => 'New pickup dates have been proposed',
        'pickup_confirmed' => 'Pickup confirmed for :date',
        'transaction_completed' => 'Transaction completed successfully',
        'dispute_opened' => 'A dispute has been opened for transaction :reference',
        'dispute_resolved' => 'Dispute :reference has been resolved',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Payment Required - AutoScout24 Safe Trade',
            'payment_verified' => 'Payment Verified - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Pickup Dates Proposed - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Pickup Confirmed - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transaction Completed - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'You are not authorized to perform this action',
        'invalid_status' => 'This action is not available in the current status',
        'payment_already_uploaded' => 'Payment proof has already been uploaded',
        'duplicate_dispute' => 'A dispute already exists for this transaction',
    ],
];

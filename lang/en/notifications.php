<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines
    |--------------------------------------------------------------------------
    */

    'payment_uploaded' => [
        'subject' => 'Payment proof received - Transaction :reference',
        'message' => 'Payment proof has been uploaded and is pending verification',
        'admin' => [
            'subject' => 'Payment verification required - :reference',
            'message' => 'A new payment proof requires verification',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Payment verified - Your funds are secured!',
            'message' => 'The payment for transaction :reference has been verified',
            'intro' => 'Great news! The payment has been verified by our team.',
            'next_steps' => 'You can now schedule the vehicle handover with the dealer.',
        ],
        'dealer' => [
            'subject' => 'Payment verified - Vehicle ready for pickup',
            'message' => 'Your payment for transaction :reference has been verified',
            'intro' => 'Your payment has been verified successfully.',
            'next_steps' => 'Please schedule the vehicle pickup with the seller.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Pickup scheduled for :date',
        'message' => 'Vehicle pickup has been scheduled',
        'seller' => [
            'intro' => 'The dealer has scheduled the vehicle pickup.',
        ],
        'dealer' => [
            'intro' => 'Pickup has been confirmed.',
        ],
    ],

    'pickup_proposed_subject' => 'Pickup dates proposed for :vehicle',
    'pickup_proposed_message' => 'New pickup dates have been proposed. Please review and confirm.',
    'pickup_confirmed_subject' => 'Pickup confirmed for :vehicle',
    'pickup_confirmed_message' => 'Pickup date confirmed for :date',

    'handover_confirmed' => [
        'subject' => 'Vehicle handover confirmed',
        'message' => 'The vehicle handover has been confirmed',
        'intro' => 'The vehicle has been successfully handed over.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transaction completed - :reference',
            'message' => 'Your vehicle sale has been completed successfully!',
            'greeting' => 'Congratulations!',
            'intro' => 'Your vehicle sale has been completed. The funds will be transferred to your bank account.',
            'thanks' => 'Thank you for using AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transaction completed - :reference',
            'message' => 'Your vehicle purchase has been completed successfully!',
            'greeting' => 'Congratulations!',
            'intro' => 'Your vehicle purchase has been completed.',
            'thanks' => 'Thank you for using AutoScout24 SafeTrade!',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transaction cancelled - :reference',
        'message' => 'Transaction :reference has been cancelled',
        'intro' => 'Unfortunately, this transaction has been cancelled.',
        'reason' => 'Reason: :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Dispute opened - Transaction :reference',
        'message' => 'A dispute has been opened for transaction :reference',
        'intro' => 'A dispute has been filed for this transaction.',
        'admin' => [
            'subject' => 'New dispute requires attention - :reference',
            'message' => 'A new dispute has been opened and requires review',
        ],
    ],

    'common' => [
        'vehicle' => 'Vehicle',
        'amount' => 'Amount',
        'reference' => 'Reference',
        'date' => 'Date',
        'status' => 'Status',
        'view_details' => 'View Details',
        'contact_support' => 'Contact Support',
        'questions' => 'If you have any questions, please contact our support team.',
    ],
];

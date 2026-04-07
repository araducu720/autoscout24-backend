<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines - German
    |--------------------------------------------------------------------------
    */
    'payment_uploaded' => [
        'subject' => 'Zahlungsbeleg eingegangen - Transaktion :reference',
        'message' => 'Ein Zahlungsbeleg wurde hochgeladen und wartet auf Überprüfung',
        'admin' => [
            'subject' => 'Zahlungsverifizierung erforderlich - :reference',
            'message' => 'Ein neuer Zahlungsbeleg muss überprüft werden',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Zahlung bestätigt - Ihre Gelder sind gesichert!',
            'message' => 'Die Zahlung für Transaktion :reference wurde bestätigt',
            'intro' => 'Gute Neuigkeiten! Die Zahlung wurde von unserem Team bestätigt.',
            'next_steps' => 'Sie können nun die Fahrzeugübergabe mit dem Händler planen.',
        ],
        'dealer' => [
            'subject' => 'Zahlung bestätigt - Fahrzeug bereit zur Abholung',
            'message' => 'Ihre Zahlung für Transaktion :reference wurde bestätigt',
            'intro' => 'Ihre Zahlung wurde erfolgreich bestätigt.',
            'next_steps' => 'Bitte vereinbaren Sie die Fahrzeugabholung mit dem Verkäufer.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Abholung geplant für :date',
        'message' => 'Die Fahrzeugabholung wurde geplant',
        'seller' => [
            'intro' => 'Der Händler hat die Fahrzeugabholung geplant.',
        ],
        'dealer' => [
            'intro' => 'Die Abholung wurde bestätigt.',
        ],
    ],

    'pickup_proposed_subject' => 'Abholtermine vorgeschlagen für :vehicle',
    'pickup_proposed_message' => 'Neue Abholtermine wurden vorgeschlagen. Bitte prüfen und bestätigen Sie.',
    'pickup_confirmed_subject' => 'Abholung bestätigt für :vehicle',
    'pickup_confirmed_message' => 'Abholtermin bestätigt für :date',

    'handover_confirmed' => [
        'subject' => 'Fahrzeugübergabe bestätigt',
        'message' => 'Die Fahrzeugübergabe wurde bestätigt',
        'intro' => 'Das Fahrzeug wurde erfolgreich übergeben.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transaktion abgeschlossen - :reference',
            'message' => 'Ihr Fahrzeugverkauf wurde erfolgreich abgeschlossen!',
            'greeting' => 'Herzlichen Glückwunsch!',
            'intro' => 'Ihr Fahrzeugverkauf wurde abgeschlossen. Die Gelder werden auf Ihr Bankkonto überwiesen.',
            'thanks' => 'Vielen Dank, dass Sie AutoScout24 SafeTrade genutzt haben!',
        ],
        'dealer' => [
            'subject' => 'Transaktion abgeschlossen - :reference',
            'message' => 'Ihr Fahrzeugkauf wurde erfolgreich abgeschlossen!',
            'greeting' => 'Herzlichen Glückwunsch!',
            'intro' => 'Ihr Fahrzeugkauf wurde abgeschlossen.',
            'thanks' => 'Vielen Dank, dass Sie AutoScout24 SafeTrade genutzt haben!',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transaktion storniert - :reference',
        'message' => 'Transaktion :reference wurde storniert',
        'intro' => 'Leider wurde diese Transaktion storniert.',
        'reason' => 'Grund: :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Streitfall eröffnet - Transaktion :reference',
        'message' => 'Ein Streitfall wurde für Transaktion :reference eröffnet',
        'intro' => 'Für diese Transaktion wurde ein Streitfall eingereicht.',
        'admin' => [
            'subject' => 'Neuer Streitfall erfordert Aufmerksamkeit - :reference',
            'message' => 'Ein neuer Streitfall wurde eröffnet und muss geprüft werden',
        ],
    ],

    'common' => [
        'vehicle' => 'Fahrzeug',
        'amount' => 'Betrag',
        'reference' => 'Referenz',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Details ansehen',
        'contact_support' => 'Support kontaktieren',
        'questions' => 'Wenn Sie Fragen haben, kontaktieren Sie bitte unser Support-Team.',
    ],
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines - Dutch
    |--------------------------------------------------------------------------
    */
    'payment_uploaded' => [
        'subject' => 'Betalingsbewijs ontvangen - Transactie :reference',
        'message' => 'Er is een betalingsbewijs geüpload en wacht op verificatie',
        'admin' => [
            'subject' => 'Betalingsverificatie vereist - :reference',
            'message' => 'Een nieuw betalingsbewijs vereist verificatie',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Betaling geverifieerd - Je geld is veilig!',
            'message' => 'De betaling voor transactie :reference is geverifieerd',
            'intro' => 'Goed nieuws! De betaling is geverifieerd door ons team.',
            'next_steps' => 'Je kunt nu de voertuigoverdracht plannen met de dealer.',
        ],
        'dealer' => [
            'subject' => 'Betaling geverifieerd - Voertuig klaar voor ophalen',
            'message' => 'Je betaling voor transactie :reference is geverifieerd',
            'intro' => 'Je betaling is succesvol geverifieerd.',
            'next_steps' => 'Plan het ophalen van het voertuig met de verkoper.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Ophalen gepland voor :date',
        'message' => 'Het ophalen van het voertuig is gepland',
        'seller' => [
            'intro' => 'De dealer heeft het ophalen van het voertuig gepland.',
        ],
        'dealer' => [
            'intro' => 'Het ophalen is bevestigd.',
        ],
    ],

    'pickup_proposed_subject' => 'Ophaaldata voorgesteld voor :vehicle',
    'pickup_proposed_message' => 'Nieuwe ophaaldata zijn voorgesteld. Bekijk en bevestig deze alstublieft.',
    'pickup_confirmed_subject' => 'Ophalen bevestigd voor :vehicle',
    'pickup_confirmed_message' => 'Ophaaldatum bevestigd voor :date',

    'handover_confirmed' => [
        'subject' => 'Voertuigoverdracht bevestigd',
        'message' => 'De voertuigoverdracht is bevestigd',
        'intro' => 'Het voertuig is succesvol overgedragen.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transactie voltooid - :reference',
            'message' => 'Je voertuigverkoop is succesvol voltooid!',
            'greeting' => 'Gefeliciteerd!',
            'intro' => 'Je voertuigverkoop is voltooid. Het geld wordt overgemaakt naar je bankrekening.',
            'thanks' => 'Bedankt voor het gebruik van AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transactie voltooid - :reference',
            'message' => 'Je voertuigaankoop is succesvol voltooid!',
            'greeting' => 'Gefeliciteerd!',
            'intro' => 'Je voertuigaankoop is voltooid.',
            'thanks' => 'Bedankt voor het gebruik van AutoScout24 SafeTrade!',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transactie geannuleerd - :reference',
        'message' => 'Transactie :reference is geannuleerd',
        'intro' => 'Helaas is deze transactie geannuleerd.',
        'reason' => 'Reden: :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Geschil geopend - Transactie :reference',
        'message' => 'Er is een geschil geopend voor transactie :reference',
        'intro' => 'Er is een geschil ingediend voor deze transactie.',
        'admin' => [
            'subject' => 'Nieuw geschil vereist aandacht - :reference',
            'message' => 'Er is een nieuw geschil geopend dat beoordeling vereist',
        ],
    ],

    'common' => [
        'vehicle' => 'Voertuig',
        'amount' => 'Bedrag',
        'reference' => 'Referentie',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Details bekijken',
        'contact_support' => 'Contact opnemen met support',
        'questions' => 'Als je vragen hebt, neem dan contact op met ons supportteam.',
    ],
];

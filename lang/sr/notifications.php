<?php

return [
    'payment_uploaded' => [
        'subject' => 'Dokaz o uplati primljen - Transakcija :reference',
        'message' => 'Dokaz o uplati je otpremljen i čeka verifikaciju',
        'admin' => [
            'subject' => 'Potrebna verifikacija uplate - :reference',
            'message' => 'Novi dokaz o uplati zahteva verifikaciju',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Uplata verifikovana - Vaša sredstva su obezbeđena!',
            'message' => 'Uplata za transakciju :reference je verifikovana',
            'intro' => 'Sjajne vesti! Uplata je verifikovana od strane našeg tima.',
            'next_steps' => 'Sada možete zakazati primopredaju vozila sa dilerom.',
        ],
        'dealer' => [
            'subject' => 'Uplata verifikovana - Vozilo spremno za preuzimanje',
            'message' => 'Vaša uplata za transakciju :reference je verifikovana',
            'intro' => 'Vaša uplata je uspešno verifikovana.',
            'next_steps' => 'Molimo vas, zakažite preuzimanje vozila sa prodavcem.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Preuzimanje zakazano za :date',
        'message' => 'Preuzimanje vozila je zakazano',
        'seller' => [
            'intro' => 'Diler je zakazao preuzimanje vozila.',
        ],
        'dealer' => [
            'intro' => 'Preuzimanje je potvrđeno.',
        ],
    ],
    'pickup_proposed_subject' => 'Predloženi datumi preuzimanja za :vehicle',
    'pickup_proposed_message' => 'Predloženi su novi datumi preuzimanja. Molimo vas, pregledajte i potvrdite.',
    'pickup_confirmed_subject' => 'Preuzimanje potvrđeno za :vehicle',
    'pickup_confirmed_message' => 'Datum preuzimanja potvrđen za :date',
    'handover_confirmed' => [
        'subject' => 'Primopredaja vozila potvrđena',
        'message' => 'Primopredaja vozila je potvrđena',
        'intro' => 'Vozilo je uspešno predato.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transakcija završena - :reference',
            'message' => 'Prodaja vašeg vozila je uspešno završena!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Prodaja vašeg vozila je završena. Sredstva će biti prebačena na vaš bankovni račun.',
            'thanks' => 'Hvala vam što koristite AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transakcija završena - :reference',
            'message' => 'Kupovina vašeg vozila je uspešno završena!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Kupovina vašeg vozila je završena.',
            'thanks' => 'Hvala vam što koristite AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transakcija otkazana - :reference',
        'message' => 'Transakcija :reference je otkazana',
        'intro' => 'Nažalost, ova transakcija je otkazana.',
        'reason' => 'Razlog: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Spor otvoren - Transakcija :reference',
        'message' => 'Za transakciju :reference je otvoren spor',
        'intro' => 'Za ovu transakciju je podnet spor.',
        'admin' => [
            'subject' => 'Novi spor zahteva pažnju - :reference',
            'message' => 'Novi spor je otvoren i zahteva pregled',
        ],
    ],
    'common' => [
        'vehicle' => 'Vozilo',
        'amount' => 'Iznos',
        'reference' => 'Referenca',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Pogledajte detalje',
        'contact_support' => 'Kontaktirajte podršku',
        'questions' => 'Ako imate bilo kakva pitanja, molimo vas kontaktirajte naš tim za podršku.',
    ],
];

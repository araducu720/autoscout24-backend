<?php

return [
    'payment_uploaded' => [
        'subject' => 'Dokaz o uplati zaprimljen - Transakcija :reference',
        'message' => 'Dokaz o uplati je učitan i čeka potvrdu',
        'admin' => [
            'subject' => 'Potrebna provjera uplate - :reference',
            'message' => 'Novi dokaz o uplati zahtijeva provjeru',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Uplata potvrđena - Vaša sredstva su osigurana!',
            'message' => 'Uplata za transakciju :reference je potvrđena',
            'intro' => 'Sjajne vijesti! Uplata je potvrđena od strane našeg tima.',
            'next_steps' => 'Sada možete dogovoriti primopredaju vozila s trgovcem.',
        ],
        'dealer' => [
            'subject' => 'Uplata potvrđena - Vozilo spremno za preuzimanje',
            'message' => 'Vaša uplata za transakciju :reference je potvrđena',
            'intro' => 'Vaša uplata je uspješno potvrđena.',
            'next_steps' => 'Molimo dogovorite preuzimanje vozila s prodavateljem.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Preuzimanje zakazano za :date',
        'message' => 'Preuzimanje vozila je zakazano',
        'seller' => [
            'intro' => 'Trgovac je zakazao preuzimanje vozila.',
        ],
        'dealer' => [
            'intro' => 'Preuzimanje je potvrđeno.',
        ],
    ],
    'pickup_proposed_subject' => 'Predloženi datumi preuzimanja za :vehicle',
    'pickup_proposed_message' => 'Predloženi su novi datumi preuzimanja. Molimo pregledajte i potvrdite.',
    'pickup_confirmed_subject' => 'Preuzimanje potvrđeno za :vehicle',
    'pickup_confirmed_message' => 'Datum preuzimanja potvrđen za :date',
    'handover_confirmed' => [
        'subject' => 'Primopredaja vozila potvrđena',
        'message' => 'Primopredaja vozila je potvrđena',
        'intro' => 'Vozilo je uspješno predano.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transakcija završena - :reference',
            'message' => 'Prodaja vašeg vozila je uspješno završena!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Prodaja vašeg vozila je završena. Sredstva će biti prebačena na vaš bankovni račun.',
            'thanks' => 'Hvala vam što koristite AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transakcija završena - :reference',
            'message' => 'Kupnja vašeg vozila je uspješno završena!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Kupnja vašeg vozila je završena.',
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
        'message' => 'Otvoren je spor za transakciju :reference',
        'intro' => 'Za ovu transakciju je podnesen spor.',
        'admin' => [
            'subject' => 'Novi spor zahtijeva pažnju - :reference',
            'message' => 'Otvoren je novi spor koji zahtijeva pregled',
        ],
    ],
    'common' => [
        'vehicle' => 'Vozilo',
        'amount' => 'Iznos',
        'reference' => 'Referenca',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Pogledaj detalje',
        'contact_support' => 'Kontaktirajte podršku',
        'questions' => 'Ako imate pitanja, molimo kontaktirajte naš tim za podršku.',
    ],
];

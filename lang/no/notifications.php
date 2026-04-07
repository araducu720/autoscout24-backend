<?php

return [
    'payment_uploaded' => [
        'subject' => 'Betalingsbevis mottatt - Transaksjon :reference',
        'message' => 'Betalingsbevis har blitt lastet opp og venter på verifisering',
        'admin' => [
            'subject' => 'Betalingsverifisering kreves - :reference',
            'message' => 'Et nytt betalingsbevis krever verifisering',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Betaling verifisert - Midlene dine er sikret!',
            'message' => 'Betalingen for transaksjon :reference har blitt verifisert',
            'intro' => 'Gode nyheter! Betalingen har blitt verifisert av teamet vårt.',
            'next_steps' => 'Du kan nå planlegge overlevering av kjøretøyet med forhandleren.',
        ],
        'dealer' => [
            'subject' => 'Betaling verifisert - Kjøretøyet er klart for henting',
            'message' => 'Betalingen din for transaksjon :reference har blitt verifisert',
            'intro' => 'Betalingen din har blitt verifisert.',
            'next_steps' => 'Vennligst avtal henting av kjøretøyet med selgeren.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Henting planlagt til :date',
        'message' => 'Henting av kjøretøy er planlagt',
        'seller' => [
            'intro' => 'Forhandleren har planlagt henting av kjøretøyet.',
        ],
        'dealer' => [
            'intro' => 'Hentingen er bekreftet.',
        ],
    ],
    'pickup_proposed_subject' => 'Hentingsdatoer foreslått for :vehicle',
    'pickup_proposed_message' => 'Nye hentingsdatoer er foreslått. Vennligst gjennomgå og bekreft.',
    'pickup_confirmed_subject' => 'Henting bekreftet for :vehicle',
    'pickup_confirmed_message' => 'Hentingsdato bekreftet for :date',
    'handover_confirmed' => [
        'subject' => 'Overlevering av kjøretøy bekreftet',
        'message' => 'Overleveringen av kjøretøyet er bekreftet',
        'intro' => 'Kjøretøyet har blitt overlevert.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transaksjon fullført - :reference',
            'message' => 'Salget av kjøretøyet ditt har blitt fullført!',
            'greeting' => 'Gratulerer!',
            'intro' => 'Salget av kjøretøyet ditt er fullført. Midlene vil bli overført til bankkontoen din.',
            'thanks' => 'Takk for at du bruker AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transaksjon fullført - :reference',
            'message' => 'Kjøpet av kjøretøyet ditt har blitt fullført!',
            'greeting' => 'Gratulerer!',
            'intro' => 'Kjøpet av kjøretøyet ditt er fullført.',
            'thanks' => 'Takk for at du bruker AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transaksjon kansellert - :reference',
        'message' => 'Transaksjon :reference har blitt kansellert',
        'intro' => 'Dessverre har denne transaksjonen blitt kansellert.',
        'reason' => 'Årsak: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Tvist åpnet - Transaksjon :reference',
        'message' => 'En tvist har blitt åpnet for transaksjon :reference',
        'intro' => 'En tvist har blitt innlevert for denne transaksjonen.',
        'admin' => [
            'subject' => 'Ny tvist krever oppmerksomhet - :reference',
            'message' => 'En ny tvist har blitt åpnet og krever gjennomgang',
        ],
    ],
    'common' => [
        'vehicle' => 'Kjøretøy',
        'amount' => 'Beløp',
        'reference' => 'Referanse',
        'date' => 'Dato',
        'status' => 'Status',
        'view_details' => 'Se detaljer',
        'contact_support' => 'Kontakt support',
        'questions' => 'Hvis du har spørsmål, vennligst kontakt supportteamet vårt.',
    ],
];

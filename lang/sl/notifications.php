<?php

return [
    'payment_uploaded' => [
        'subject' => 'Dokazilo o plačilu prejeto - Transakcija :reference',
        'message' => 'Dokazilo o plačilu je bilo naloženo in čaka na preverjanje',
        'admin' => [
            'subject' => 'Zahtevano preverjanje plačila - :reference',
            'message' => 'Novo dokazilo o plačilu zahteva preverjanje',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Plačilo preverjeno - Vaša sredstva so zavarovana!',
            'message' => 'Plačilo za transakcijo :reference je bilo preverjeno',
            'intro' => 'Odlične novice! Plačilo je bilo preverjeno s strani naše ekipe.',
            'next_steps' => 'Zdaj lahko načrtujete predajo vozila s trgovcem.',
        ],
        'dealer' => [
            'subject' => 'Plačilo preverjeno - Vozilo pripravljeno za prevzem',
            'message' => 'Vaše plačilo za transakcijo :reference je bilo preverjeno',
            'intro' => 'Vaše plačilo je bilo uspešno preverjeno.',
            'next_steps' => 'Prosimo, načrtujte prevzem vozila s prodajalcem.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Prevzem načrtovan za :date',
        'message' => 'Prevzem vozila je bil načrtovan',
        'seller' => [
            'intro' => 'Trgovec je načrtoval prevzem vozila.',
        ],
        'dealer' => [
            'intro' => 'Prevzem je bil potrjen.',
        ],
    ],
    'pickup_proposed_subject' => 'Predlagani datumi prevzema za :vehicle',
    'pickup_proposed_message' => 'Predlagani so bili novi datumi prevzema. Prosimo, preglejte in potrdite.',
    'pickup_confirmed_subject' => 'Prevzem potrjen za :vehicle',
    'pickup_confirmed_message' => 'Datum prevzema potrjen za :date',
    'handover_confirmed' => [
        'subject' => 'Predaja vozila potrjena',
        'message' => 'Predaja vozila je bila potrjena',
        'intro' => 'Vozilo je bilo uspešno predano.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transakcija zaključena - :reference',
            'message' => 'Prodaja vašega vozila je bila uspešno zaključena!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Prodaja vašega vozila je bila zaključena. Sredstva bodo nakazana na vaš bančni račun.',
            'thanks' => 'Hvala, da uporabljate AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transakcija zaključena - :reference',
            'message' => 'Nakup vašega vozila je bil uspešno zaključen!',
            'greeting' => 'Čestitamo!',
            'intro' => 'Nakup vašega vozila je bil zaključen.',
            'thanks' => 'Hvala, da uporabljate AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transakcija preklicana - :reference',
        'message' => 'Transakcija :reference je bila preklicana',
        'intro' => 'Žal je bila ta transakcija preklicana.',
        'reason' => 'Razlog: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Spor odprt - Transakcija :reference',
        'message' => 'Za transakcijo :reference je bil odprt spor',
        'intro' => 'Za to transakcijo je bil vložen spor.',
        'admin' => [
            'subject' => 'Nov spor zahteva pozornost - :reference',
            'message' => 'Nov spor je bil odprt in zahteva pregled',
        ],
    ],
    'common' => [
        'vehicle' => 'Vozilo',
        'amount' => 'Znesek',
        'reference' => 'Referenca',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Ogled podrobnosti',
        'contact_support' => 'Kontaktirajte podporo',
        'questions' => 'Če imate kakršna koli vprašanja, se obrnite na našo ekipo za podporo.',
    ],
];

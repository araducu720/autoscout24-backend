<?php

return [
    'payment_uploaded' => [
        'subject' => 'Dovada plății primită - Tranzacția :reference',
        'message' => 'Dovada plății a fost încărcată și este în așteptarea verificării',
        'admin' => [
            'subject' => 'Verificare plată necesară - :reference',
            'message' => 'O nouă dovadă de plată necesită verificare',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Plată verificată - Fondurile dumneavoastră sunt în siguranță!',
            'message' => 'Plata pentru tranzacția :reference a fost verificată',
            'intro' => 'Vești bune! Plata a fost verificată de echipa noastră.',
            'next_steps' => 'Puteți programa acum predarea vehiculului cu dealerul.',
        ],
        'dealer' => [
            'subject' => 'Plată verificată - Vehiculul este pregătit pentru ridicare',
            'message' => 'Plata dumneavoastră pentru tranzacția :reference a fost verificată',
            'intro' => 'Plata dumneavoastră a fost verificată cu succes.',
            'next_steps' => 'Vă rugăm să programați ridicarea vehiculului cu vânzătorul.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Ridicare programată pentru :date',
        'message' => 'Ridicarea vehiculului a fost programată',
        'seller' => [
            'intro' => 'Dealerul a programat ridicarea vehiculului.',
        ],
        'dealer' => [
            'intro' => 'Ridicarea a fost confirmată.',
        ],
    ],
    'pickup_proposed_subject' => 'Date de ridicare propuse pentru :vehicle',
    'pickup_proposed_message' => 'Au fost propuse noi date de ridicare. Vă rugăm să verificați și să confirmați.',
    'pickup_confirmed_subject' => 'Ridicare confirmată pentru :vehicle',
    'pickup_confirmed_message' => 'Data ridicării confirmată pentru :date',
    'handover_confirmed' => [
        'subject' => 'Predarea vehiculului confirmată',
        'message' => 'Predarea vehiculului a fost confirmată',
        'intro' => 'Vehiculul a fost predat cu succes.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Tranzacție finalizată - :reference',
            'message' => 'Vânzarea vehiculului dumneavoastră a fost finalizată cu succes!',
            'greeting' => 'Felicitări!',
            'intro' => 'Vânzarea vehiculului dumneavoastră a fost finalizată. Fondurile vor fi transferate în contul dumneavoastră bancar.',
            'thanks' => 'Vă mulțumim că ați folosit AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Tranzacție finalizată - :reference',
            'message' => 'Achiziția vehiculului dumneavoastră a fost finalizată cu succes!',
            'greeting' => 'Felicitări!',
            'intro' => 'Achiziția vehiculului dumneavoastră a fost finalizată.',
            'thanks' => 'Vă mulțumim că ați folosit AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Tranzacție anulată - :reference',
        'message' => 'Tranzacția :reference a fost anulată',
        'intro' => 'Din păcate, această tranzacție a fost anulată.',
        'reason' => 'Motiv: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Dispută deschisă - Tranzacția :reference',
        'message' => 'O dispută a fost deschisă pentru tranzacția :reference',
        'intro' => 'O dispută a fost depusă pentru această tranzacție.',
        'admin' => [
            'subject' => 'O nouă dispută necesită atenție - :reference',
            'message' => 'O nouă dispută a fost deschisă și necesită revizuire',
        ],
    ],
    'common' => [
        'vehicle' => 'Vehicul',
        'amount' => 'Sumă',
        'reference' => 'Referință',
        'date' => 'Data',
        'status' => 'Stare',
        'view_details' => 'Vezi detalii',
        'contact_support' => 'Contactați asistența',
        'questions' => 'Dacă aveți întrebări, vă rugăm să contactați echipa noastră de asistență.',
    ],
];

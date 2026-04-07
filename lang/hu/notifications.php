<?php

return [
    'payment_uploaded' => [
        'subject' => 'Fizetési igazolás beérkezett - Tranzakció :reference',
        'message' => 'A fizetési igazolás feltöltésre került és ellenőrzésre vár',
        'admin' => [
            'subject' => 'Fizetés ellenőrzése szükséges - :reference',
            'message' => 'Új fizetési igazolás ellenőrzést igényel',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Fizetés ellenőrizve - Az Ön pénzeszközei biztosítva!',
            'message' => 'A(z) :reference tranzakció fizetése ellenőrizve lett',
            'intro' => 'Nagyszerű hír! A fizetést csapatunk ellenőrizte.',
            'next_steps' => 'Most már egyeztethet a jármű átadásáról a kereskedővel.',
        ],
        'dealer' => [
            'subject' => 'Fizetés ellenőrizve - Jármű átvételre kész',
            'message' => 'A(z) :reference tranzakcióhoz tartozó fizetése ellenőrizve lett',
            'intro' => 'Fizetése sikeresen ellenőrzésre került.',
            'next_steps' => 'Kérjük, egyeztesse a jármű átvételét az eladóval.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Átvétel ütemezve: :date',
        'message' => 'A jármű átvétele ütemezésre került',
        'seller' => [
            'intro' => 'A kereskedő ütemezte a jármű átvételét.',
        ],
        'dealer' => [
            'intro' => 'Az átvétel megerősítésre került.',
        ],
    ],
    'pickup_proposed_subject' => 'Átvételi időpontok javasolva a következőhöz: :vehicle',
    'pickup_proposed_message' => 'Új átvételi időpontok kerültek javaslatra. Kérjük, tekintse át és erősítse meg.',
    'pickup_confirmed_subject' => 'Átvétel megerősítve a következőhöz: :vehicle',
    'pickup_confirmed_message' => 'Átvételi időpont megerősítve: :date',
    'handover_confirmed' => [
        'subject' => 'Jármű átadás megerősítve',
        'message' => 'A jármű átadása megerősítésre került',
        'intro' => 'A jármű sikeresen átadásra került.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Tranzakció befejezve - :reference',
            'message' => 'Járműve eladása sikeresen befejeződött!',
            'greeting' => 'Gratulálunk!',
            'intro' => 'Járműve eladása befejeződött. A pénzeszközök átutalásra kerülnek a bankszámlájára.',
            'thanks' => 'Köszönjük, hogy az AutoScout24 SafeTrade szolgáltatást használta!',
        ],
        'dealer' => [
            'subject' => 'Tranzakció befejezve - :reference',
            'message' => 'Járművásárlása sikeresen befejeződött!',
            'greeting' => 'Gratulálunk!',
            'intro' => 'Járművásárlása befejeződött.',
            'thanks' => 'Köszönjük, hogy az AutoScout24 SafeTrade szolgáltatást használta!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Tranzakció törölve - :reference',
        'message' => 'A(z) :reference tranzakció törölve lett',
        'intro' => 'Sajnos ez a tranzakció törölve lett.',
        'reason' => 'Ok: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Vita megnyitva - Tranzakció :reference',
        'message' => 'Vita nyílt a(z) :reference tranzakcióval kapcsolatban',
        'intro' => 'Ehhez a tranzakcióhoz vitát nyújtottak be.',
        'admin' => [
            'subject' => 'Új vita figyelmet igényel - :reference',
            'message' => 'Új vita nyílt, amely felülvizsgálatot igényel',
        ],
    ],
    'common' => [
        'vehicle' => 'Jármű',
        'amount' => 'Összeg',
        'reference' => 'Hivatkozás',
        'date' => 'Dátum',
        'status' => 'Állapot',
        'view_details' => 'Részletek megtekintése',
        'contact_support' => 'Ügyfélszolgálat elérése',
        'questions' => 'Ha kérdése van, kérjük, forduljon ügyfélszolgálatunkhoz.',
    ],
];

<?php

return [
    'payment_uploaded' => [
        'subject' => 'Doklad o platbe prijatý - Transakcia :reference',
        'message' => 'Doklad o platbe bol nahraný a čaká na overenie',
        'admin' => [
            'subject' => 'Vyžaduje sa overenie platby - :reference',
            'message' => 'Nový doklad o platbe vyžaduje overenie',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Platba overená - Vaše prostriedky sú zabezpečené!',
            'message' => 'Platba za transakciu :reference bola overená',
            'intro' => 'Skvelá správa! Platba bola overená naším tímom.',
            'next_steps' => 'Teraz môžete naplánovať odovzdanie vozidla s predajcom.',
        ],
        'dealer' => [
            'subject' => 'Platba overená - Vozidlo pripravené na vyzdvihnutie',
            'message' => 'Vaša platba za transakciu :reference bola overená',
            'intro' => 'Vaša platba bola úspešne overená.',
            'next_steps' => 'Prosím, naplánujte vyzdvihnutie vozidla s predávajúcim.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Vyzdvihnutie naplánované na :date',
        'message' => 'Vyzdvihnutie vozidla bolo naplánované',
        'seller' => [
            'intro' => 'Predajca naplánoval vyzdvihnutie vozidla.',
        ],
        'dealer' => [
            'intro' => 'Vyzdvihnutie bolo potvrdené.',
        ],
    ],
    'pickup_proposed_subject' => 'Navrhnuté dátumy vyzdvihnutia pre :vehicle',
    'pickup_proposed_message' => 'Boli navrhnuté nové dátumy vyzdvihnutia. Prosím, skontrolujte a potvrďte.',
    'pickup_confirmed_subject' => 'Vyzdvihnutie potvrdené pre :vehicle',
    'pickup_confirmed_message' => 'Dátum vyzdvihnutia potvrdený na :date',
    'handover_confirmed' => [
        'subject' => 'Odovzdanie vozidla potvrdené',
        'message' => 'Odovzdanie vozidla bolo potvrdené',
        'intro' => 'Vozidlo bolo úspešne odovzdané.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transakcia dokončená - :reference',
            'message' => 'Predaj vášho vozidla bol úspešne dokončený!',
            'greeting' => 'Gratulujeme!',
            'intro' => 'Predaj vášho vozidla bol dokončený. Prostriedky budú prevedené na váš bankový účet.',
            'thanks' => 'Ďakujeme, že používate AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transakcia dokončená - :reference',
            'message' => 'Nákup vášho vozidla bol úspešne dokončený!',
            'greeting' => 'Gratulujeme!',
            'intro' => 'Nákup vášho vozidla bol dokončený.',
            'thanks' => 'Ďakujeme, že používate AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transakcia zrušená - :reference',
        'message' => 'Transakcia :reference bola zrušená',
        'intro' => 'Bohužiaľ, táto transakcia bola zrušená.',
        'reason' => 'Dôvod: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Spor otvorený - Transakcia :reference',
        'message' => 'Pre transakciu :reference bol otvorený spor',
        'intro' => 'Pre túto transakciu bol podaný spor.',
        'admin' => [
            'subject' => 'Nový spor vyžaduje pozornosť - :reference',
            'message' => 'Bol otvorený nový spor, ktorý vyžaduje preskúmanie',
        ],
    ],
    'common' => [
        'vehicle' => 'Vozidlo',
        'amount' => 'Suma',
        'reference' => 'Referencia',
        'date' => 'Dátum',
        'status' => 'Stav',
        'view_details' => 'Zobraziť podrobnosti',
        'contact_support' => 'Kontaktovať podporu',
        'questions' => 'Ak máte akékoľvek otázky, prosím, kontaktujte náš tím podpory.',
    ],
];

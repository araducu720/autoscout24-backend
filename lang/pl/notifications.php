<?php

return [
    'payment_uploaded' => [
        'subject' => 'Otrzymano potwierdzenie płatności - Transakcja :reference',
        'message' => 'Potwierdzenie płatności zostało przesłane i oczekuje na weryfikację',
        'admin' => [
            'subject' => 'Wymagana weryfikacja płatności - :reference',
            'message' => 'Nowe potwierdzenie płatności wymaga weryfikacji',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Płatność zweryfikowana - Twoje środki są zabezpieczone!',
            'message' => 'Płatność za transakcję :reference została zweryfikowana',
            'intro' => 'Świetne wiadomości! Płatność została zweryfikowana przez nasz zespół.',
            'next_steps' => 'Możesz teraz umówić się na przekazanie pojazdu z dealerem.',
        ],
        'dealer' => [
            'subject' => 'Płatność zweryfikowana - Pojazd gotowy do odbioru',
            'message' => 'Twoja płatność za transakcję :reference została zweryfikowana',
            'intro' => 'Twoja płatność została pomyślnie zweryfikowana.',
            'next_steps' => 'Prosimy o umówienie odbioru pojazdu ze sprzedającym.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Odbiór zaplanowany na :date',
        'message' => 'Odbiór pojazdu został zaplanowany',
        'seller' => [
            'intro' => 'Dealer zaplanował odbiór pojazdu.',
        ],
        'dealer' => [
            'intro' => 'Odbiór został potwierdzony.',
        ],
    ],
    'pickup_proposed_subject' => 'Zaproponowano terminy odbioru dla :vehicle',
    'pickup_proposed_message' => 'Zaproponowano nowe terminy odbioru. Prosimy o sprawdzenie i potwierdzenie.',
    'pickup_confirmed_subject' => 'Odbiór potwierdzony dla :vehicle',
    'pickup_confirmed_message' => 'Termin odbioru potwierdzony na :date',
    'handover_confirmed' => [
        'subject' => 'Przekazanie pojazdu potwierdzone',
        'message' => 'Przekazanie pojazdu zostało potwierdzone',
        'intro' => 'Pojazd został pomyślnie przekazany.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transakcja zakończona - :reference',
            'message' => 'Sprzedaż Twojego pojazdu została pomyślnie zakończona!',
            'greeting' => 'Gratulacje!',
            'intro' => 'Sprzedaż Twojego pojazdu została zakończona. Środki zostaną przelane na Twoje konto bankowe.',
            'thanks' => 'Dziękujemy za korzystanie z AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transakcja zakończona - :reference',
            'message' => 'Zakup Twojego pojazdu został pomyślnie zakończony!',
            'greeting' => 'Gratulacje!',
            'intro' => 'Zakup Twojego pojazdu został zakończony.',
            'thanks' => 'Dziękujemy za korzystanie z AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transakcja anulowana - :reference',
        'message' => 'Transakcja :reference została anulowana',
        'intro' => 'Niestety, ta transakcja została anulowana.',
        'reason' => 'Powód: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Spór otwarty - Transakcja :reference',
        'message' => 'Spór został otwarty dla transakcji :reference',
        'intro' => 'Dla tej transakcji został złożony spór.',
        'admin' => [
            'subject' => 'Nowy spór wymaga uwagi - :reference',
            'message' => 'Nowy spór został otwarty i wymaga przeglądu',
        ],
    ],
    'common' => [
        'vehicle' => 'Pojazd',
        'amount' => 'Kwota',
        'reference' => 'Numer referencyjny',
        'date' => 'Data',
        'status' => 'Status',
        'view_details' => 'Zobacz szczegóły',
        'contact_support' => 'Skontaktuj się z pomocą techniczną',
        'questions' => 'Jeśli masz pytania, skontaktuj się z naszym zespołem wsparcia.',
    ],
];

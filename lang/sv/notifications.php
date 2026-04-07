<?php

return [
    'payment_uploaded' => [
        'subject' => 'Betalningsbevis mottaget - Transaktion :reference',
        'message' => 'Betalningsbevis har laddats upp och väntar på verifiering',
        'admin' => [
            'subject' => 'Betalningsverifiering krävs - :reference',
            'message' => 'Ett nytt betalningsbevis kräver verifiering',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Betalning verifierad - Dina medel är säkrade!',
            'message' => 'Betalningen för transaktion :reference har verifierats',
            'intro' => 'Goda nyheter! Betalningen har verifierats av vårt team.',
            'next_steps' => 'Du kan nu schemalägga fordonsöverlämningen med handlaren.',
        ],
        'dealer' => [
            'subject' => 'Betalning verifierad - Fordonet redo för upphämtning',
            'message' => 'Din betalning för transaktion :reference har verifierats',
            'intro' => 'Din betalning har verifierats framgångsrikt.',
            'next_steps' => 'Vänligen schemalägga upphämtningen av fordonet med säljaren.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Upphämtning schemalagd för :date',
        'message' => 'Fordonsupphämtning har schemalagts',
        'seller' => [
            'intro' => 'Handlaren har schemalagt fordonsupphämtningen.',
        ],
        'dealer' => [
            'intro' => 'Upphämtningen har bekräftats.',
        ],
    ],
    'pickup_proposed_subject' => 'Upphämtningsdatum föreslagna för :vehicle',
    'pickup_proposed_message' => 'Nya upphämtningsdatum har föreslagits. Vänligen granska och bekräfta.',
    'pickup_confirmed_subject' => 'Upphämtning bekräftad för :vehicle',
    'pickup_confirmed_message' => 'Upphämtningsdatum bekräftat för :date',
    'handover_confirmed' => [
        'subject' => 'Fordonsöverlämning bekräftad',
        'message' => 'Fordonsöverlämningen har bekräftats',
        'intro' => 'Fordonet har framgångsrikt överlämnats.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transaktion slutförd - :reference',
            'message' => 'Din fordonsförsäljning har slutförts framgångsrikt!',
            'greeting' => 'Grattis!',
            'intro' => 'Din fordonsförsäljning har slutförts. Medlen kommer att överföras till ditt bankkonto.',
            'thanks' => 'Tack för att du använder AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transaktion slutförd - :reference',
            'message' => 'Ditt fordonsköp har slutförts framgångsrikt!',
            'greeting' => 'Grattis!',
            'intro' => 'Ditt fordonsköp har slutförts.',
            'thanks' => 'Tack för att du använder AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Transaktion avbruten - :reference',
        'message' => 'Transaktion :reference har avbrutits',
        'intro' => 'Tyvärr har denna transaktion avbrutits.',
        'reason' => 'Anledning: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Tvist öppnad - Transaktion :reference',
        'message' => 'En tvist har öppnats för transaktion :reference',
        'intro' => 'En tvist har lämnats in för denna transaktion.',
        'admin' => [
            'subject' => 'Ny tvist kräver uppmärksamhet - :reference',
            'message' => 'En ny tvist har öppnats och kräver granskning',
        ],
    ],
    'common' => [
        'vehicle' => 'Fordon',
        'amount' => 'Belopp',
        'reference' => 'Referens',
        'date' => 'Datum',
        'status' => 'Status',
        'view_details' => 'Visa detaljer',
        'contact_support' => 'Kontakta support',
        'questions' => 'Om du har några frågor, vänligen kontakta vårt supportteam.',
    ],
];

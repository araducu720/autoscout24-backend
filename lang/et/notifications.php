<?php

return [
    'payment_uploaded' => [
        'subject' => 'Maksetõend saadud - Tehing :reference',
        'message' => 'Maksetõend on üles laaditud ja ootab kinnitamist',
        'admin' => [
            'subject' => 'Makse kinnitamine vajalik - :reference',
            'message' => 'Uus maksetõend vajab kinnitamist',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Makse kinnitatud - Teie raha on kaitstud!',
            'message' => 'Tehingu :reference makse on kinnitatud',
            'intro' => 'Suurepärased uudised! Makse on meie meeskonna poolt kinnitatud.',
            'next_steps' => 'Nüüd saate edasimüüjaga sõiduki üleandmise kokku leppida.',
        ],
        'dealer' => [
            'subject' => 'Makse kinnitatud - Sõiduk on valmis kättesaamiseks',
            'message' => 'Teie makse tehingu :reference eest on kinnitatud',
            'intro' => 'Teie makse on edukalt kinnitatud.',
            'next_steps' => 'Palun leppige müüjaga sõiduki kättesaamine kokku.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Kättesaamine planeeritud kuupäevale :date',
        'message' => 'Sõiduki kättesaamine on planeeritud',
        'seller' => [
            'intro' => 'Edasimüüja on sõiduki kättesaamise planeerinud.',
        ],
        'dealer' => [
            'intro' => 'Kättesaamine on kinnitatud.',
        ],
    ],
    'pickup_proposed_subject' => 'Kättesaamise kuupäevad pakutud sõidukile :vehicle',
    'pickup_proposed_message' => 'Uued kättesaamise kuupäevad on pakutud. Palun vaadake üle ja kinnitage.',
    'pickup_confirmed_subject' => 'Kättesaamine kinnitatud sõidukile :vehicle',
    'pickup_confirmed_message' => 'Kättesaamise kuupäev kinnitatud: :date',
    'handover_confirmed' => [
        'subject' => 'Sõiduki üleandmine kinnitatud',
        'message' => 'Sõiduki üleandmine on kinnitatud',
        'intro' => 'Sõiduk on edukalt üle antud.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Tehing lõpetatud - :reference',
            'message' => 'Teie sõiduki müük on edukalt lõpetatud!',
            'greeting' => 'Palju õnne!',
            'intro' => 'Teie sõiduki müük on lõpetatud. Raha kantakse teie pangakontole.',
            'thanks' => 'Täname, et kasutasite AutoScout24 SafeTrade\'i!',
        ],
        'dealer' => [
            'subject' => 'Tehing lõpetatud - :reference',
            'message' => 'Teie sõiduki ost on edukalt lõpetatud!',
            'greeting' => 'Palju õnne!',
            'intro' => 'Teie sõiduki ost on lõpetatud.',
            'thanks' => 'Täname, et kasutasite AutoScout24 SafeTrade\'i!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Tehing tühistatud - :reference',
        'message' => 'Tehing :reference on tühistatud',
        'intro' => 'Kahjuks on see tehing tühistatud.',
        'reason' => 'Põhjus: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Vaidlus avatud - Tehing :reference',
        'message' => 'Tehingu :reference kohta on avatud vaidlus',
        'intro' => 'Selle tehingu kohta on esitatud vaidlus.',
        'admin' => [
            'subject' => 'Uus vaidlus vajab tähelepanu - :reference',
            'message' => 'Uus vaidlus on avatud ja vajab ülevaatamist',
        ],
    ],
    'common' => [
        'vehicle' => 'Sõiduk',
        'amount' => 'Summa',
        'reference' => 'Viide',
        'date' => 'Kuupäev',
        'status' => 'Olek',
        'view_details' => 'Vaata üksikasju',
        'contact_support' => 'Võtke ühendust toega',
        'questions' => 'Kui teil on küsimusi, palun võtke ühendust meie tugimeeskonnaga.',
    ],
];

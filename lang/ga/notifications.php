<?php

return [
    'payment_uploaded' => [
        'subject' => 'Cruthúnas íocaíochta faighte - Idirbheart :reference',
        'message' => 'Uaslódáladh cruthúnas íocaíochta agus tá sé ag fanacht le fíorú',
        'admin' => [
            'subject' => 'Fíorú íocaíochta ag teastáil - :reference',
            'message' => 'Tá cruthúnas íocaíochta nua ag teastáil fíorú',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Íocaíocht fíoraithe - Tá do chistí slán!',
            'message' => 'Fíoraíodh an íocaíocht don idirbheart :reference',
            'intro' => 'Scéala iontacha! Fíoraíodh an íocaíocht ag ár bhfoireann.',
            'next_steps' => 'Is féidir leat anois aistriú na feithicle a shocrú leis an déileálaí.',
        ],
        'dealer' => [
            'subject' => 'Íocaíocht fíoraithe - Feithicil réidh le bailiú',
            'message' => 'Fíoraíodh d\'íocaíocht don idirbheart :reference',
            'intro' => 'D\'éirigh leis d\'íocaíocht a fhíorú.',
            'next_steps' => 'Socraigh bailiú na feithicle leis an díoltóir, le do thoil.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Bailiú sceidealta do :date',
        'message' => 'Tá bailiú na feithicle sceidealta',
        'seller' => [
            'intro' => 'Shocraigh an déileálaí bailiú na feithicle.',
        ],
        'dealer' => [
            'intro' => 'Tá an bailiú deimhnithe.',
        ],
    ],
    'pickup_proposed_subject' => 'Dátaí bailithe molta do :vehicle',
    'pickup_proposed_message' => 'Tá dátaí bailithe nua molta. Athbhreithnigh agus deimhnigh, le do thoil.',
    'pickup_confirmed_subject' => 'Bailiú deimhnithe do :vehicle',
    'pickup_confirmed_message' => 'Dáta bailithe deimhnithe do :date',
    'handover_confirmed' => [
        'subject' => 'Aistriú na feithicle deimhnithe',
        'message' => 'Tá aistriú na feithicle deimhnithe',
        'intro' => 'Aistríodh an fheithicil go rathúil.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Idirbheart críochnaithe - :reference',
            'message' => 'Críochnaíodh díolachán d\'fheithicle go rathúil!',
            'greeting' => 'Comhghairdeas!',
            'intro' => 'Tá díolachán d\'fheithicle críochnaithe. Aistreofar na cistí chuig do chuntas bainc.',
            'thanks' => 'Go raibh maith agat as AutoScout24 SafeTrade a úsáid!',
        ],
        'dealer' => [
            'subject' => 'Idirbheart críochnaithe - :reference',
            'message' => 'Críochnaíodh ceannach d\'fheithicle go rathúil!',
            'greeting' => 'Comhghairdeas!',
            'intro' => 'Tá ceannach d\'fheithicle críochnaithe.',
            'thanks' => 'Go raibh maith agat as AutoScout24 SafeTrade a úsáid!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Idirbheart cealaithe - :reference',
        'message' => 'Cealáíodh idirbheart :reference',
        'intro' => 'Ar an drochuair, cealáíodh an idirbheart seo.',
        'reason' => 'Fáth: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Díospóid oscailte - Idirbheart :reference',
        'message' => 'Osclaíodh díospóid don idirbheart :reference',
        'intro' => 'Comhdaíodh díospóid don idirbheart seo.',
        'admin' => [
            'subject' => 'Díospóid nua ag teastáil aird - :reference',
            'message' => 'Osclaíodh díospóid nua agus tá athbhreithniú ag teastáil',
        ],
    ],
    'common' => [
        'vehicle' => 'Feithicil',
        'amount' => 'Méid',
        'reference' => 'Tagairt',
        'date' => 'Dáta',
        'status' => 'Stádas',
        'view_details' => 'Féach ar Shonraí',
        'contact_support' => 'Déan Teagmháil le Tacaíocht',
        'questions' => 'Má tá ceisteanna agat, déan teagmháil lenár bhfoireann tacaíochta, le do thoil.',
    ],
];

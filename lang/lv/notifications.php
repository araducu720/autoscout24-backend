<?php

return [
    'payment_uploaded' => [
        'subject' => 'Maksājuma apliecinājums saņemts - Darījums :reference',
        'message' => 'Maksājuma apliecinājums ir augšupielādēts un gaida apstiprināšanu',
        'admin' => [
            'subject' => 'Nepieciešama maksājuma pārbaude - :reference',
            'message' => 'Jauns maksājuma apliecinājums prasa pārbaudi',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Maksājums apstiprināts - Jūsu līdzekļi ir nodrošināti!',
            'message' => 'Darījuma :reference maksājums ir apstiprināts',
            'intro' => 'Lieliskas ziņas! Maksājumu apstiprināja mūsu komanda.',
            'next_steps' => 'Tagad varat plānot transportlīdzekļa nodošanu ar tirgotāju.',
        ],
        'dealer' => [
            'subject' => 'Maksājums apstiprināts - Transportlīdzeklis gatavs saņemšanai',
            'message' => 'Jūsu maksājums par darījumu :reference ir apstiprināts',
            'intro' => 'Jūsu maksājums ir veiksmīgi apstiprināts.',
            'next_steps' => 'Lūdzu, sazinieties ar pārdevēju, lai plānotu transportlīdzekļa saņemšanu.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Saņemšana ieplānota :date',
        'message' => 'Transportlīdzekļa saņemšana ir ieplānota',
        'seller' => [
            'intro' => 'Tirgotājs ir ieplānojis transportlīdzekļa saņemšanu.',
        ],
        'dealer' => [
            'intro' => 'Saņemšana ir apstiprināta.',
        ],
    ],
    'pickup_proposed_subject' => 'Piedāvātie saņemšanas datumi priekš :vehicle',
    'pickup_proposed_message' => 'Ir piedāvāti jauni saņemšanas datumi. Lūdzu, pārskatiet un apstipriniet.',
    'pickup_confirmed_subject' => 'Saņemšana apstiprināta priekš :vehicle',
    'pickup_confirmed_message' => 'Saņemšanas datums apstiprināts :date',
    'handover_confirmed' => [
        'subject' => 'Transportlīdzekļa nodošana apstiprināta',
        'message' => 'Transportlīdzekļa nodošana ir apstiprināta',
        'intro' => 'Transportlīdzeklis ir veiksmīgi nodots.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Darījums pabeigts - :reference',
            'message' => 'Jūsu transportlīdzekļa pārdošana ir veiksmīgi pabeigta!',
            'greeting' => 'Apsveicam!',
            'intro' => 'Jūsu transportlīdzekļa pārdošana ir pabeigta. Līdzekļi tiks pārskaitīti uz jūsu bankas kontu.',
            'thanks' => 'Paldies, ka izmantojāt AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Darījums pabeigts - :reference',
            'message' => 'Jūsu transportlīdzekļa iegāde ir veiksmīgi pabeigta!',
            'greeting' => 'Apsveicam!',
            'intro' => 'Jūsu transportlīdzekļa iegāde ir pabeigta.',
            'thanks' => 'Paldies, ka izmantojāt AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Darījums atcelts - :reference',
        'message' => 'Darījums :reference ir atcelts',
        'intro' => 'Diemžēl šis darījums ir atcelts.',
        'reason' => 'Iemesls: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Strīds atvērts - Darījums :reference',
        'message' => 'Darījumam :reference ir atvērts strīds',
        'intro' => 'Šim darījumam ir iesniegts strīds.',
        'admin' => [
            'subject' => 'Jauns strīds prasa uzmanību - :reference',
            'message' => 'Ir atvērts jauns strīds, kam nepieciešama pārskatīšana',
        ],
    ],
    'common' => [
        'vehicle' => 'Transportlīdzeklis',
        'amount' => 'Summa',
        'reference' => 'Atsauce',
        'date' => 'Datums',
        'status' => 'Statuss',
        'view_details' => 'Skatīt detaļas',
        'contact_support' => 'Sazināties ar atbalstu',
        'questions' => 'Ja jums ir jautājumi, lūdzu, sazinieties ar mūsu atbalsta komandu.',
    ],
];

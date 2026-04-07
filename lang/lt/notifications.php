<?php

return [
    'payment_uploaded' => [
        'subject' => 'Mokėjimo įrodymas gautas - Sandoris :reference',
        'message' => 'Mokėjimo įrodymas įkeltas ir laukia patvirtinimo',
        'admin' => [
            'subject' => 'Reikalingas mokėjimo patikrinimas - :reference',
            'message' => 'Naujas mokėjimo įrodymas reikalauja patikrinimo',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Mokėjimas patvirtintas - Jūsų lėšos apsaugotos!',
            'message' => 'Sandorio :reference mokėjimas buvo patvirtintas',
            'intro' => 'Puikios naujienos! Mokėjimą patvirtino mūsų komanda.',
            'next_steps' => 'Dabar galite susitarti dėl transporto priemonės perdavimo su prekiautoju.',
        ],
        'dealer' => [
            'subject' => 'Mokėjimas patvirtintas - Transporto priemonė paruošta atsiėmimui',
            'message' => 'Jūsų mokėjimas už sandorį :reference buvo patvirtintas',
            'intro' => 'Jūsų mokėjimas sėkmingai patvirtintas.',
            'next_steps' => 'Prašome susitarti dėl transporto priemonės atsiėmimo su pardavėju.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Atsiėmimas suplanuotas :date',
        'message' => 'Transporto priemonės atsiėmimas suplanuotas',
        'seller' => [
            'intro' => 'Prekiautojas suplanuojo transporto priemonės atsiėmimą.',
        ],
        'dealer' => [
            'intro' => 'Atsiėmimas patvirtintas.',
        ],
    ],
    'pickup_proposed_subject' => 'Pasiūlytos atsiėmimo datos :vehicle',
    'pickup_proposed_message' => 'Pasiūlytos naujos atsiėmimo datos. Prašome peržiūrėti ir patvirtinti.',
    'pickup_confirmed_subject' => 'Atsiėmimas patvirtintas :vehicle',
    'pickup_confirmed_message' => 'Atsiėmimo data patvirtinta :date',
    'handover_confirmed' => [
        'subject' => 'Transporto priemonės perdavimas patvirtintas',
        'message' => 'Transporto priemonės perdavimas buvo patvirtintas',
        'intro' => 'Transporto priemonė sėkmingai perduota.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Sandoris baigtas - :reference',
            'message' => 'Jūsų transporto priemonės pardavimas sėkmingai baigtas!',
            'greeting' => 'Sveikiname!',
            'intro' => 'Jūsų transporto priemonės pardavimas baigtas. Lėšos bus pervestos į jūsų banko sąskaitą.',
            'thanks' => 'Dėkojame, kad naudojatės AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Sandoris baigtas - :reference',
            'message' => 'Jūsų transporto priemonės pirkimas sėkmingai baigtas!',
            'greeting' => 'Sveikiname!',
            'intro' => 'Jūsų transporto priemonės pirkimas baigtas.',
            'thanks' => 'Dėkojame, kad naudojatės AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Sandoris atšauktas - :reference',
        'message' => 'Sandoris :reference buvo atšauktas',
        'intro' => 'Deja, šis sandoris buvo atšauktas.',
        'reason' => 'Priežastis: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Ginčas atidarytas - Sandoris :reference',
        'message' => 'Sandoriui :reference atidarytas ginčas',
        'intro' => 'Šiam sandoriui buvo pateiktas ginčas.',
        'admin' => [
            'subject' => 'Naujas ginčas reikalauja dėmesio - :reference',
            'message' => 'Atidarytas naujas ginčas, kuriam reikia peržiūros',
        ],
    ],
    'common' => [
        'vehicle' => 'Transporto priemonė',
        'amount' => 'Suma',
        'reference' => 'Nuoroda',
        'date' => 'Data',
        'status' => 'Būsena',
        'view_details' => 'Peržiūrėti detales',
        'contact_support' => 'Susisiekti su palaikymu',
        'questions' => 'Jei turite klausimų, prašome susisiekti su mūsų palaikymo komanda.',
    ],
];

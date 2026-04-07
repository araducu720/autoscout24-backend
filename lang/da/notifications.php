<?php
// Danish
return [
    'payment_uploaded' => ['subject' => 'Betalingsbevis modtaget - Transaktion :reference', 'message' => 'Betalingsbevis er blevet uploadet og afventer bekræftelse', 'admin' => ['subject' => 'Betalingsbekræftelse påkrævet - :reference', 'message' => 'Et nyt betalingsbevis kræver bekræftelse']],
    'payment_verified' => [
        'seller' => ['subject' => 'Betaling bekræftet - Dine midler er sikret!', 'message' => 'Betalingen for transaktion :reference er blevet bekræftet', 'intro' => 'Gode nyheder! Betalingen er blevet bekræftet af vores team.', 'next_steps' => 'Du kan nu planlægge overdragelsen af køretøjet med forhandleren.'],
        'dealer' => ['subject' => 'Betaling bekræftet - Køretøj klar til afhentning', 'message' => 'Din betaling for transaktion :reference er blevet bekræftet', 'intro' => 'Din betaling er blevet bekræftet.', 'next_steps' => 'Venligst planlæg afhentningen af køretøjet med sælgeren.'],
    ],
    'pickup_scheduled' => ['subject' => 'Afhentning planlagt til :date', 'message' => 'Afhentning af køretøjet er blevet planlagt', 'seller' => ['intro' => 'Forhandleren har planlagt afhentningen af køretøjet.'], 'dealer' => ['intro' => 'Afhentningen er bekræftet.']],
    'pickup_proposed_subject' => 'Afhentningsdatoer foreslået for :vehicle',
    'pickup_proposed_message' => 'Nye afhentningsdatoer er blevet foreslået. Venligst gennemgå og bekræft.',
    'pickup_confirmed_subject' => 'Afhentning bekræftet for :vehicle',
    'pickup_confirmed_message' => 'Afhentningsdato bekræftet til :date',
    'handover_confirmed' => ['subject' => 'Overdragelse af køretøj bekræftet', 'message' => 'Overdragelsen af køretøjet er blevet bekræftet', 'intro' => 'Køretøjet er blevet overdraget.'],
    'transaction_completed' => [
        'seller' => ['subject' => 'Transaktion afsluttet - :reference', 'message' => 'Dit køretøjssalg er blevet gennemført!', 'greeting' => 'Tillykke!', 'intro' => 'Dit køretøjssalg er afsluttet. Midlerne vil blive overført til din bankkonto.', 'thanks' => 'Tak fordi du bruger AutoScout24 SafeTrade!'],
        'dealer' => ['subject' => 'Transaktion afsluttet - :reference', 'message' => 'Dit køretøjskøb er blevet gennemført!', 'greeting' => 'Tillykke!', 'intro' => 'Dit køretøjskøb er afsluttet.', 'thanks' => 'Tak fordi du bruger AutoScout24 SafeTrade!'],
    ],
    'transaction_cancelled' => ['subject' => 'Transaktion annulleret - :reference', 'message' => 'Transaktion :reference er blevet annulleret', 'intro' => 'Desværre er denne transaktion blevet annulleret.', 'reason' => 'Årsag: :reason'],
    'dispute_opened' => ['subject' => 'Tvist åbnet - Transaktion :reference', 'message' => 'En tvist er blevet åbnet for transaktion :reference', 'intro' => 'Der er indgivet en tvist for denne transaktion.', 'admin' => ['subject' => 'Ny tvist kræver opmærksomhed - :reference', 'message' => 'En ny tvist er blevet åbnet og kræver gennemgang']],
    'common' => ['vehicle' => 'Køretøj', 'amount' => 'Beløb', 'reference' => 'Reference', 'date' => 'Dato', 'status' => 'Status', 'view_details' => 'Se detaljer', 'contact_support' => 'Kontakt support', 'questions' => 'Hvis du har spørgsmål, kontakt venligst vores supportteam.'],
];

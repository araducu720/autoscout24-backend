<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines - Italian
    |--------------------------------------------------------------------------
    */
    'payment_uploaded' => [
        'subject' => 'Prova di pagamento ricevuta - Transazione :reference',
        'message' => 'Una prova di pagamento è stata caricata ed è in attesa di verifica',
        'admin' => [
            'subject' => 'Verifica pagamento richiesta - :reference',
            'message' => 'Una nuova prova di pagamento richiede verifica',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Pagamento verificato - I tuoi fondi sono al sicuro!',
            'message' => 'Il pagamento per la transazione :reference è stato verificato',
            'intro' => 'Ottime notizie! Il pagamento è stato verificato dal nostro team.',
            'next_steps' => 'Ora puoi programmare la consegna del veicolo con il concessionario.',
        ],
        'dealer' => [
            'subject' => 'Pagamento verificato - Veicolo pronto per il ritiro',
            'message' => 'Il tuo pagamento per la transazione :reference è stato verificato',
            'intro' => 'Il tuo pagamento è stato verificato con successo.',
            'next_steps' => 'Si prega di programmare il ritiro del veicolo con il venditore.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Ritiro programmato per il :date',
        'message' => 'Il ritiro del veicolo è stato programmato',
        'seller' => [
            'intro' => 'Il concessionario ha programmato il ritiro del veicolo.',
        ],
        'dealer' => [
            'intro' => 'Il ritiro è stato confermato.',
        ],
    ],

    'pickup_proposed_subject' => 'Date di ritiro proposte per :vehicle',
    'pickup_proposed_message' => 'Sono state proposte nuove date di ritiro. Si prega di esaminare e confermare.',
    'pickup_confirmed_subject' => 'Ritiro confermato per :vehicle',
    'pickup_confirmed_message' => 'Data di ritiro confermata per il :date',

    'handover_confirmed' => [
        'subject' => 'Consegna del veicolo confermata',
        'message' => 'La consegna del veicolo è stata confermata',
        'intro' => 'Il veicolo è stato consegnato con successo.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transazione completata - :reference',
            'message' => 'La vendita del tuo veicolo è stata completata con successo!',
            'greeting' => 'Congratulazioni!',
            'intro' => 'La vendita del tuo veicolo è stata completata. I fondi saranno trasferiti sul tuo conto bancario.',
            'thanks' => 'Grazie per aver utilizzato AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transazione completata - :reference',
            'message' => 'Il tuo acquisto del veicolo è stato completato con successo!',
            'greeting' => 'Congratulazioni!',
            'intro' => 'Il tuo acquisto del veicolo è stato completato.',
            'thanks' => 'Grazie per aver utilizzato AutoScout24 SafeTrade!',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transazione annullata - :reference',
        'message' => 'La transazione :reference è stata annullata',
        'intro' => 'Purtroppo, questa transazione è stata annullata.',
        'reason' => 'Motivo: :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Contestazione aperta - Transazione :reference',
        'message' => 'È stata aperta una contestazione per la transazione :reference',
        'intro' => 'È stata presentata una contestazione per questa transazione.',
        'admin' => [
            'subject' => 'Nuova contestazione richiede attenzione - :reference',
            'message' => 'È stata aperta una nuova contestazione che richiede revisione',
        ],
    ],

    'common' => [
        'vehicle' => 'Veicolo',
        'amount' => 'Importo',
        'reference' => 'Riferimento',
        'date' => 'Data',
        'status' => 'Stato',
        'view_details' => 'Visualizza dettagli',
        'contact_support' => 'Contatta il supporto',
        'questions' => 'Se hai domande, contatta il nostro team di supporto.',
    ],
];

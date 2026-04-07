<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Compravendita Veicoli Sicura',
    
    // Listing
    'listing' => [
        'title' => 'Annuncio Permuta',
        'create' => 'Crea annuncio',
        'edit' => 'Modifica annuncio',
        'publish' => 'Pubblica annuncio',
        'deactivate' => 'Disattiva annuncio',
        'status' => [
            'draft' => 'Bozza',
            'active' => 'Attivo',
            'inactive' => 'Inattivo',
            'sold' => 'Venduto',
            'expired' => 'Scaduto',
        ],
        'fields' => [
            'make' => 'Marca',
            'model' => 'Modello',
            'year' => 'Anno',
            'mileage' => 'Chilometraggio',
            'fuel_type' => 'Carburante',
            'transmission' => 'Cambio',
            'vin' => 'Numero telaio',
            'registration' => 'Targa',
            'exterior_color' => 'Colore esterno',
            'interior_color' => 'Colore interno',
            'condition' => 'Condizioni',
            'description' => 'Descrizione',
            'min_price' => 'Prezzo minimo accettabile',
            'max_price' => 'Prezzo massimo atteso',
            'location' => 'Posizione',
        ],
        'condition' => [
            'excellent' => 'Eccellente',
            'good' => 'Buono',
            'fair' => 'Discreto',
            'poor' => 'Scarso',
        ],
    ],
        'fields' => [
            'amount' => 'Importo offerta',
            'message' => 'Messaggio al venditore',
            'expires_at' => 'Valida fino al',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transazione',
        'reference' => 'Riferimento',
        'status' => [
            'pending_payment' => 'In attesa di pagamento',
            'payment_uploaded' => 'Prova di pagamento caricata',
            'payment_verified' => 'Pagamento verificato',
            'vehicle_handed_over' => 'Veicolo consegnato',
            'completed' => 'Completata',
            'cancelled' => 'Annullata',
            'disputed' => 'In contestazione',
        ],
        'actions' => [
            'upload_payment' => 'Carica prova di pagamento',
            'confirm_handover' => 'Conferma consegna',
            'cancel' => 'Annulla transazione',
            'open_dispute' => 'Apri contestazione',
        ],
        'payment' => [
            'due_date' => 'Scadenza pagamento',
            'bank_transfer' => 'Bonifico bancario',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Coordinate bancarie',
            'bank_name' => 'Nome banca',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Intestatario conto',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Ritiro Veicolo',
        'propose_dates' => 'Proponi date',
        'confirm_date' => 'Conferma data',
        'reschedule' => 'Riprogramma',
        'status' => [
            'pending' => 'In attesa',
            'dates_proposed' => 'Date proposte',
            'confirmed' => 'Confermato',
            'rescheduled' => 'Riprogrammato',
            'completed' => 'Completato',
        ],
        'fields' => [
            'address' => 'Indirizzo ritiro',
            'phone' => 'Telefono',
            'date' => 'Data ritiro',
            'notes' => 'Note',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Contestazione',
        'open' => 'Apri contestazione',
        'reference' => 'Riferimento contestazione',
        'status' => [
            'open' => 'Aperta',
            'under_review' => 'In revisione',
            'awaiting_info' => 'In attesa di informazioni',
            'escalated' => 'Escalata',
            'resolved' => 'Risolta',
            'closed' => 'Chiusa',
        ],
        'types' => [
            'payment_not_received' => 'Pagamento non ricevuto',
            'vehicle_not_as_described' => 'Veicolo non conforme',
            'delivery_issue' => 'Problema di consegna',
            'documentation_problem' => 'Problema documentazione',
            'fraud' => 'Sospetta frode',
            'other' => 'Altro',
        ],
        'priority' => [
            'low' => 'Bassa',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
        ],
        'resolution' => [
            'in_favor_seller' => 'A favore del venditore',
            'in_favor_dealer' => 'A favore del concessionario',
            'mutual_agreement' => 'Accordo reciproco',
            'cancelled' => 'Annullato',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Pagamento di :amount dovuto entro il :date',
        'payment_verified' => 'Il pagamento è stato verificato',
        'pickup_proposed' => 'Sono state proposte nuove date per il ritiro',
        'pickup_confirmed' => 'Ritiro confermato per il :date',
        'transaction_completed' => 'Transazione completata con successo',
        'dispute_opened' => 'È stata aperta una contestazione per la transazione :reference',
        'dispute_resolved' => 'La contestazione :reference è stata risolta',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Pagamento richiesto - AutoScout24 Safe Trade',
            'payment_verified' => 'Pagamento verificato - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Date di ritiro proposte - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Ritiro confermato - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transazione completata - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'Non sei autorizzato a eseguire questa azione',
        'invalid_status' => 'Questa azione non è disponibile nello stato attuale',
        'payment_already_uploaded' => 'La prova di pagamento è già stata caricata',
        'duplicate_dispute' => 'Esiste già una contestazione per questa transazione',
    ],
];

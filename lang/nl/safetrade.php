<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Veilige Voertuighandel',
    
    // Listing
    'listing' => [
        'title' => 'Inruil Advertentie',
        'create' => 'Advertentie maken',
        'edit' => 'Advertentie bewerken',
        'publish' => 'Advertentie publiceren',
        'deactivate' => 'Advertentie deactiveren',
        'status' => [
            'draft' => 'Concept',
            'active' => 'Actief',
            'inactive' => 'Inactief',
            'sold' => 'Verkocht',
            'expired' => 'Verlopen',
        ],
        'fields' => [
            'make' => 'Merk',
            'model' => 'Model',
            'year' => 'Bouwjaar',
            'mileage' => 'Kilometerstand',
            'fuel_type' => 'Brandstof',
            'transmission' => 'Transmissie',
            'vin' => 'Chassisnummer',
            'registration' => 'Kenteken',
            'exterior_color' => 'Exterieur kleur',
            'interior_color' => 'Interieur kleur',
            'condition' => 'Staat',
            'description' => 'Beschrijving',
            'min_price' => 'Minimum aanvaardbare prijs',
            'max_price' => 'Maximum verwachte prijs',
            'location' => 'Locatie',
        ],
        'condition' => [
            'excellent' => 'Uitstekend',
            'good' => 'Goed',
            'fair' => 'Redelijk',
            'poor' => 'Slecht',
        ],
    ],
        'fields' => [
            'amount' => 'Bod bedrag',
            'message' => 'Bericht aan verkoper',
            'expires_at' => 'Geldig tot',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transactie',
        'reference' => 'Referentie',
        'status' => [
            'pending_payment' => 'Wacht op betaling',
            'payment_uploaded' => 'Betalingsbewijs geüpload',
            'payment_verified' => 'Betaling geverifieerd',
            'vehicle_handed_over' => 'Voertuig overgedragen',
            'completed' => 'Voltooid',
            'cancelled' => 'Geannuleerd',
            'disputed' => 'In geschil',
        ],
        'actions' => [
            'upload_payment' => 'Betalingsbewijs uploaden',
            'confirm_handover' => 'Overdracht bevestigen',
            'cancel' => 'Transactie annuleren',
            'open_dispute' => 'Geschil openen',
        ],
        'payment' => [
            'due_date' => 'Betaaltermijn',
            'bank_transfer' => 'Bankoverschrijving',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Bankgegevens',
            'bank_name' => 'Banknaam',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Rekeninghouder',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Voertuig Ophalen',
        'propose_dates' => 'Data voorstellen',
        'confirm_date' => 'Datum bevestigen',
        'reschedule' => 'Opnieuw plannen',
        'status' => [
            'pending' => 'In behandeling',
            'dates_proposed' => 'Data voorgesteld',
            'confirmed' => 'Bevestigd',
            'rescheduled' => 'Opnieuw gepland',
            'completed' => 'Voltooid',
        ],
        'fields' => [
            'address' => 'Ophaaladres',
            'phone' => 'Telefoon',
            'date' => 'Ophaaldatum',
            'notes' => 'Opmerkingen',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Geschil',
        'open' => 'Geschil openen',
        'reference' => 'Geschil referentie',
        'status' => [
            'open' => 'Open',
            'under_review' => 'In behandeling',
            'awaiting_info' => 'Wacht op informatie',
            'escalated' => 'Geëscaleerd',
            'resolved' => 'Opgelost',
            'closed' => 'Gesloten',
        ],
        'types' => [
            'payment_not_received' => 'Betaling niet ontvangen',
            'vehicle_not_as_described' => 'Voertuig niet zoals beschreven',
            'delivery_issue' => 'Leveringsprobleem',
            'documentation_problem' => 'Documentatieprobleem',
            'fraud' => 'Vermoeden van fraude',
            'other' => 'Anders',
        ],
        'priority' => [
            'low' => 'Laag',
            'medium' => 'Gemiddeld',
            'high' => 'Hoog',
            'urgent' => 'Urgent',
        ],
        'resolution' => [
            'in_favor_seller' => 'In het voordeel van de verkoper',
            'in_favor_dealer' => 'In het voordeel van de dealer',
            'mutual_agreement' => 'Onderlinge overeenkomst',
            'cancelled' => 'Geannuleerd',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Betaling van :amount vervalt op :date',
        'payment_verified' => 'De betaling is geverifieerd',
        'pickup_proposed' => 'Er zijn nieuwe ophaaldata voorgesteld',
        'pickup_confirmed' => 'Ophalen bevestigd voor :date',
        'transaction_completed' => 'Transactie succesvol voltooid',
        'dispute_opened' => 'Er is een geschil geopend voor transactie :reference',
        'dispute_resolved' => 'Geschil :reference is opgelost',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Betaling vereist - AutoScout24 Safe Trade',
            'payment_verified' => 'Betaling geverifieerd - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Ophaaldata voorgesteld - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Ophalen bevestigd - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transactie voltooid - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'Je bent niet gemachtigd om deze actie uit te voeren',
        'invalid_status' => 'Deze actie is niet beschikbaar in de huidige status',
        'payment_already_uploaded' => 'Het betalingsbewijs is al geüpload',
        'duplicate_dispute' => 'Er bestaat al een geschil voor deze transactie',
    ],
];

<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Sicherer Fahrzeughandel',
    
    // Listing
    'listing' => [
        'title' => 'Ankauf-Angebot',
        'create' => 'Angebot erstellen',
        'edit' => 'Angebot bearbeiten',
        'publish' => 'Angebot veröffentlichen',
        'deactivate' => 'Angebot deaktivieren',
        'status' => [
            'draft' => 'Entwurf',
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
            'sold' => 'Verkauft',
            'expired' => 'Abgelaufen',
        ],
        'fields' => [
            'make' => 'Marke',
            'model' => 'Modell',
            'year' => 'Erstzulassung',
            'mileage' => 'Kilometerstand',
            'fuel_type' => 'Kraftstoffart',
            'transmission' => 'Getriebe',
            'vin' => 'Fahrzeug-Ident.-Nr.',
            'registration' => 'Kennzeichen',
            'exterior_color' => 'Außenfarbe',
            'interior_color' => 'Innenfarbe',
            'condition' => 'Zustand',
            'description' => 'Beschreibung',
            'min_price' => 'Mindestpreis',
            'max_price' => 'Erwarteter Höchstpreis',
            'location' => 'Standort',
        ],
        'condition' => [
            'excellent' => 'Ausgezeichnet',
            'good' => 'Gut',
            'fair' => 'Befriedigend',
            'poor' => 'Mängel',
        ],
    ],
        'fields' => [
            'amount' => 'Gebotshöhe',
            'message' => 'Nachricht an Verkäufer',
            'expires_at' => 'Gültig bis',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transaktion',
        'reference' => 'Referenznummer',
        'status' => [
            'pending_payment' => 'Zahlung ausstehend',
            'payment_uploaded' => 'Zahlungsbeleg hochgeladen',
            'payment_verified' => 'Zahlung bestätigt',
            'vehicle_handed_over' => 'Fahrzeug übergeben',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Storniert',
            'disputed' => 'Im Streitfall',
        ],
        'actions' => [
            'upload_payment' => 'Zahlungsbeleg hochladen',
            'confirm_handover' => 'Übergabe bestätigen',
            'cancel' => 'Transaktion stornieren',
            'open_dispute' => 'Streitfall melden',
        ],
        'payment' => [
            'due_date' => 'Zahlungsfrist',
            'bank_transfer' => 'Überweisung',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Bankverbindung',
            'bank_name' => 'Bankname',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Kontoinhaber',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Fahrzeugabholung',
        'propose_dates' => 'Abholtermine vorschlagen',
        'confirm_date' => 'Abholtermin bestätigen',
        'reschedule' => 'Termin verschieben',
        'status' => [
            'pending' => 'Ausstehend',
            'dates_proposed' => 'Termine vorgeschlagen',
            'confirmed' => 'Bestätigt',
            'rescheduled' => 'Verschoben',
            'completed' => 'Abgeschlossen',
        ],
        'fields' => [
            'address' => 'Abholadresse',
            'phone' => 'Telefonnummer',
            'date' => 'Abholtermin',
            'notes' => 'Anmerkungen',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Streitfall',
        'open' => 'Streitfall melden',
        'reference' => 'Streitfall-Referenz',
        'status' => [
            'open' => 'Offen',
            'under_review' => 'In Prüfung',
            'awaiting_info' => 'Wartet auf Information',
            'escalated' => 'Eskaliert',
            'resolved' => 'Gelöst',
            'closed' => 'Geschlossen',
        ],
        'types' => [
            'payment_not_received' => 'Zahlung nicht erhalten',
            'vehicle_not_as_described' => 'Fahrzeug nicht wie beschrieben',
            'delivery_issue' => 'Lieferproblem',
            'documentation_problem' => 'Dokumentationsproblem',
            'fraud' => 'Betrugsverdacht',
            'other' => 'Sonstiges',
        ],
        'priority' => [
            'low' => 'Niedrig',
            'medium' => 'Mittel',
            'high' => 'Hoch',
            'urgent' => 'Dringend',
        ],
        'resolution' => [
            'in_favor_seller' => 'Zugunsten des Verkäufers',
            'in_favor_dealer' => 'Zugunsten des Händlers',
            'mutual_agreement' => 'Einvernehmliche Lösung',
            'cancelled' => 'Storniert',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Zahlung von :amount bis :date fällig',
        'payment_verified' => 'Zahlung wurde bestätigt',
        'pickup_proposed' => 'Neue Abholtermine wurden vorgeschlagen',
        'pickup_confirmed' => 'Abholung bestätigt für :date',
        'transaction_completed' => 'Transaktion erfolgreich abgeschlossen',
        'dispute_opened' => 'Ein Streitfall wurde für Transaktion :reference eröffnet',
        'dispute_resolved' => 'Streitfall :reference wurde gelöst',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Zahlung erforderlich - AutoScout24 Safe Trade',
            'payment_verified' => 'Zahlung bestätigt - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Abholtermine vorgeschlagen - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Abholung bestätigt - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transaktion abgeschlossen - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'Sie sind nicht berechtigt, diese Aktion durchzuführen',
        'invalid_status' => 'Diese Aktion ist im aktuellen Status nicht verfügbar',
        'payment_already_uploaded' => 'Zahlungsbeleg wurde bereits hochgeladen',
        'duplicate_dispute' => 'Für diese Transaktion existiert bereits ein Streitfall',
    ],
];

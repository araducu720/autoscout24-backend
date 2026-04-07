<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Commerce de véhicules sécurisé',
    
    // Listing
    'listing' => [
        'title' => 'Annonce de reprise',
        'create' => 'Créer une annonce',
        'edit' => 'Modifier l\'annonce',
        'publish' => 'Publier l\'annonce',
        'deactivate' => 'Désactiver l\'annonce',
        'status' => [
            'draft' => 'Brouillon',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'sold' => 'Vendu',
            'expired' => 'Expirée',
        ],
        'fields' => [
            'make' => 'Marque',
            'model' => 'Modèle',
            'year' => 'Année',
            'mileage' => 'Kilométrage',
            'fuel_type' => 'Carburant',
            'transmission' => 'Boîte de vitesses',
            'vin' => 'Numéro VIN',
            'registration' => 'Immatriculation',
            'exterior_color' => 'Couleur extérieure',
            'interior_color' => 'Couleur intérieure',
            'condition' => 'État',
            'description' => 'Description',
            'min_price' => 'Prix minimum acceptable',
            'max_price' => 'Prix maximum attendu',
            'location' => 'Localisation',
        ],
        'condition' => [
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Correct',
            'poor' => 'Mauvais',
        ],
    ],
        'fields' => [
            'amount' => 'Montant de l\'offre',
            'message' => 'Message au vendeur',
            'expires_at' => 'Valable jusqu\'au',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transaction',
        'reference' => 'Référence',
        'status' => [
            'pending_payment' => 'En attente de paiement',
            'payment_uploaded' => 'Preuve de paiement envoyée',
            'payment_verified' => 'Paiement vérifié',
            'vehicle_handed_over' => 'Véhicule remis',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            'disputed' => 'En litige',
        ],
        'actions' => [
            'upload_payment' => 'Envoyer preuve de paiement',
            'confirm_handover' => 'Confirmer la remise',
            'cancel' => 'Annuler la transaction',
            'open_dispute' => 'Ouvrir un litige',
        ],
        'payment' => [
            'due_date' => 'Date limite de paiement',
            'bank_transfer' => 'Virement bancaire',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Coordonnées bancaires',
            'bank_name' => 'Nom de la banque',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Titulaire du compte',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Récupération du véhicule',
        'propose_dates' => 'Proposer des dates',
        'confirm_date' => 'Confirmer la date',
        'reschedule' => 'Reporter',
        'status' => [
            'pending' => 'En attente',
            'dates_proposed' => 'Dates proposées',
            'confirmed' => 'Confirmé',
            'rescheduled' => 'Reporté',
            'completed' => 'Terminé',
        ],
        'fields' => [
            'address' => 'Adresse de récupération',
            'phone' => 'Téléphone',
            'date' => 'Date de récupération',
            'notes' => 'Remarques',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Litige',
        'open' => 'Ouvrir un litige',
        'reference' => 'Référence du litige',
        'status' => [
            'open' => 'Ouvert',
            'under_review' => 'En cours d\'examen',
            'awaiting_info' => 'En attente d\'informations',
            'escalated' => 'Escaladé',
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
        ],
        'types' => [
            'payment_not_received' => 'Paiement non reçu',
            'vehicle_not_as_described' => 'Véhicule non conforme',
            'delivery_issue' => 'Problème de livraison',
            'documentation_problem' => 'Problème de documentation',
            'fraud' => 'Fraude suspectée',
            'other' => 'Autre',
        ],
        'priority' => [
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Haute',
            'urgent' => 'Urgente',
        ],
        'resolution' => [
            'in_favor_seller' => 'En faveur du vendeur',
            'in_favor_dealer' => 'En faveur du concessionnaire',
            'mutual_agreement' => 'Accord mutuel',
            'cancelled' => 'Annulé',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Paiement de :amount dû avant le :date',
        'payment_verified' => 'Le paiement a été vérifié',
        'pickup_proposed' => 'De nouvelles dates de récupération ont été proposées',
        'pickup_confirmed' => 'Récupération confirmée pour le :date',
        'transaction_completed' => 'Transaction terminée avec succès',
        'dispute_opened' => 'Un litige a été ouvert pour la transaction :reference',
        'dispute_resolved' => 'Le litige :reference a été résolu',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Paiement requis - AutoScout24 Safe Trade',
            'payment_verified' => 'Paiement vérifié - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Dates de récupération proposées - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Récupération confirmée - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transaction terminée - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'Vous n\'êtes pas autorisé à effectuer cette action',
        'invalid_status' => 'Cette action n\'est pas disponible dans le statut actuel',
        'payment_already_uploaded' => 'La preuve de paiement a déjà été envoyée',
        'duplicate_dispute' => 'Un litige existe déjà pour cette transaction',
    ],
];

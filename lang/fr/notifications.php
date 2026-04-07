<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines - French
    |--------------------------------------------------------------------------
    */
    'payment_uploaded' => [
        'subject' => 'Preuve de paiement reçue - Transaction :reference',
        'message' => 'Une preuve de paiement a été téléchargée et est en attente de vérification',
        'admin' => [
            'subject' => 'Vérification de paiement requise - :reference',
            'message' => 'Une nouvelle preuve de paiement nécessite une vérification',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Paiement vérifié - Vos fonds sont sécurisés !',
            'message' => 'Le paiement pour la transaction :reference a été vérifié',
            'intro' => 'Bonne nouvelle ! Le paiement a été vérifié par notre équipe.',
            'next_steps' => 'Vous pouvez maintenant planifier la remise du véhicule avec le concessionnaire.',
        ],
        'dealer' => [
            'subject' => 'Paiement vérifié - Véhicule prêt à être récupéré',
            'message' => 'Votre paiement pour la transaction :reference a été vérifié',
            'intro' => 'Votre paiement a été vérifié avec succès.',
            'next_steps' => 'Veuillez planifier la récupération du véhicule avec le vendeur.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Récupération planifiée pour le :date',
        'message' => 'La récupération du véhicule a été planifiée',
        'seller' => [
            'intro' => 'Le concessionnaire a planifié la récupération du véhicule.',
        ],
        'dealer' => [
            'intro' => 'La récupération a été confirmée.',
        ],
    ],

    'pickup_proposed_subject' => 'Dates de récupération proposées pour :vehicle',
    'pickup_proposed_message' => 'De nouvelles dates de récupération ont été proposées. Veuillez les examiner et confirmer.',
    'pickup_confirmed_subject' => 'Récupération confirmée pour :vehicle',
    'pickup_confirmed_message' => 'Date de récupération confirmée pour le :date',

    'handover_confirmed' => [
        'subject' => 'Remise du véhicule confirmée',
        'message' => 'La remise du véhicule a été confirmée',
        'intro' => 'Le véhicule a été remis avec succès.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transaction terminée - :reference',
            'message' => 'Votre vente de véhicule a été complétée avec succès !',
            'greeting' => 'Félicitations !',
            'intro' => 'Votre vente de véhicule est terminée. Les fonds seront transférés sur votre compte bancaire.',
            'thanks' => 'Merci d\'avoir utilisé AutoScout24 SafeTrade !',
        ],
        'dealer' => [
            'subject' => 'Transaction terminée - :reference',
            'message' => 'Votre achat de véhicule a été complété avec succès !',
            'greeting' => 'Félicitations !',
            'intro' => 'Votre achat de véhicule est terminé.',
            'thanks' => 'Merci d\'avoir utilisé AutoScout24 SafeTrade !',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transaction annulée - :reference',
        'message' => 'La transaction :reference a été annulée',
        'intro' => 'Malheureusement, cette transaction a été annulée.',
        'reason' => 'Raison : :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Litige ouvert - Transaction :reference',
        'message' => 'Un litige a été ouvert pour la transaction :reference',
        'intro' => 'Un litige a été déposé pour cette transaction.',
        'admin' => [
            'subject' => 'Nouveau litige nécessite attention - :reference',
            'message' => 'Un nouveau litige a été ouvert et nécessite un examen',
        ],
    ],

    'common' => [
        'vehicle' => 'Véhicule',
        'amount' => 'Montant',
        'reference' => 'Référence',
        'date' => 'Date',
        'status' => 'Statut',
        'view_details' => 'Voir les détails',
        'contact_support' => 'Contacter le support',
        'questions' => 'Si vous avez des questions, veuillez contacter notre équipe de support.',
    ],
];

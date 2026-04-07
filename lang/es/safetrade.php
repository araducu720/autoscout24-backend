<?php

return [
    // General
    'title' => 'Safe Trade',
    'subtitle' => 'Comercio Seguro de Vehículos',
    
    // Listing
    'listing' => [
        'title' => 'Anuncio de Permuta',
        'create' => 'Crear anuncio',
        'edit' => 'Editar anuncio',
        'publish' => 'Publicar anuncio',
        'deactivate' => 'Desactivar anuncio',
        'status' => [
            'draft' => 'Borrador',
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'sold' => 'Vendido',
            'expired' => 'Caducado',
        ],
        'fields' => [
            'make' => 'Marca',
            'model' => 'Modelo',
            'year' => 'Año',
            'mileage' => 'Kilometraje',
            'fuel_type' => 'Combustible',
            'transmission' => 'Transmisión',
            'vin' => 'Número VIN',
            'registration' => 'Matrícula',
            'exterior_color' => 'Color exterior',
            'interior_color' => 'Color interior',
            'condition' => 'Estado',
            'description' => 'Descripción',
            'min_price' => 'Precio mínimo aceptable',
            'max_price' => 'Precio máximo esperado',
            'location' => 'Ubicación',
        ],
        'condition' => [
            'excellent' => 'Excelente',
            'good' => 'Bueno',
            'fair' => 'Regular',
            'poor' => 'Malo',
        ],
    ],
        'fields' => [
            'amount' => 'Importe de la oferta',
            'message' => 'Mensaje al vendedor',
            'expires_at' => 'Válida hasta',
        ],
    ],
    
    // Transactions
    'transaction' => [
        'title' => 'Transacción',
        'reference' => 'Referencia',
        'status' => [
            'pending_payment' => 'Pendiente de pago',
            'payment_uploaded' => 'Comprobante enviado',
            'payment_verified' => 'Pago verificado',
            'vehicle_handed_over' => 'Vehículo entregado',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'disputed' => 'En disputa',
        ],
        'actions' => [
            'upload_payment' => 'Enviar comprobante de pago',
            'confirm_handover' => 'Confirmar entrega',
            'cancel' => 'Cancelar transacción',
            'open_dispute' => 'Abrir disputa',
        ],
        'payment' => [
            'due_date' => 'Fecha límite de pago',
            'bank_transfer' => 'Transferencia bancaria',
            'leasing' => 'Leasing',
        ],
        'bank_details' => [
            'title' => 'Datos bancarios',
            'bank_name' => 'Nombre del banco',
            'iban' => 'IBAN',
            'bic' => 'BIC/SWIFT',
            'account_holder' => 'Titular de la cuenta',
        ],
    ],
    
    // Pickup
    'pickup' => [
        'title' => 'Recogida del Vehículo',
        'propose_dates' => 'Proponer fechas',
        'confirm_date' => 'Confirmar fecha',
        'reschedule' => 'Reprogramar',
        'status' => [
            'pending' => 'Pendiente',
            'dates_proposed' => 'Fechas propuestas',
            'confirmed' => 'Confirmada',
            'rescheduled' => 'Reprogramada',
            'completed' => 'Completada',
        ],
        'fields' => [
            'address' => 'Dirección de recogida',
            'phone' => 'Teléfono',
            'date' => 'Fecha de recogida',
            'notes' => 'Notas',
        ],
    ],
    
    // Disputes
    'dispute' => [
        'title' => 'Disputa',
        'open' => 'Abrir disputa',
        'reference' => 'Referencia de disputa',
        'status' => [
            'open' => 'Abierta',
            'under_review' => 'En revisión',
            'awaiting_info' => 'Esperando información',
            'escalated' => 'Escalada',
            'resolved' => 'Resuelta',
            'closed' => 'Cerrada',
        ],
        'types' => [
            'payment_not_received' => 'Pago no recibido',
            'vehicle_not_as_described' => 'Vehículo no conforme',
            'delivery_issue' => 'Problema de entrega',
            'documentation_problem' => 'Problema de documentación',
            'fraud' => 'Sospecha de fraude',
            'other' => 'Otro',
        ],
        'priority' => [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente',
        ],
        'resolution' => [
            'in_favor_seller' => 'A favor del vendedor',
            'in_favor_dealer' => 'A favor del concesionario',
            'mutual_agreement' => 'Acuerdo mutuo',
            'cancelled' => 'Cancelada',
        ],
    ],
    
    // Notifications
    'notifications' => [
        'payment_due' => 'Pago de :amount vence el :date',
        'payment_verified' => 'El pago ha sido verificado',
        'pickup_proposed' => 'Se han propuesto nuevas fechas de recogida',
        'pickup_confirmed' => 'Recogida confirmada para el :date',
        'transaction_completed' => 'Transacción completada con éxito',
        'dispute_opened' => 'Se ha abierto una disputa para la transacción :reference',
        'dispute_resolved' => 'La disputa :reference ha sido resuelta',
    ],
    
    // Emails
    'emails' => [
        'subject' => [
            'payment_due' => 'Pago requerido - AutoScout24 Safe Trade',
            'payment_verified' => 'Pago verificado - AutoScout24 Safe Trade',
            'pickup_proposed' => 'Fechas de recogida propuestas - AutoScout24 Safe Trade',
            'pickup_confirmed' => 'Recogida confirmada - AutoScout24 Safe Trade',
            'transaction_completed' => 'Transacción completada - AutoScout24 Safe Trade',
        ],
    ],
    
    // Errors
    'errors' => [
        'not_authorized' => 'No estás autorizado para realizar esta acción',
        'invalid_status' => 'Esta acción no está disponible en el estado actual',
        'payment_already_uploaded' => 'El comprobante de pago ya ha sido enviado',
        'duplicate_dispute' => 'Ya existe una disputa para esta transacción',
    ],
];

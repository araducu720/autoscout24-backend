<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines - Spanish
    |--------------------------------------------------------------------------
    */
    'payment_uploaded' => [
        'subject' => 'Comprobante de pago recibido - Transacción :reference',
        'message' => 'Se ha subido un comprobante de pago y está pendiente de verificación',
        'admin' => [
            'subject' => 'Verificación de pago requerida - :reference',
            'message' => 'Un nuevo comprobante de pago requiere verificación',
        ],
    ],

    'payment_verified' => [
        'seller' => [
            'subject' => 'Pago verificado - ¡Tus fondos están asegurados!',
            'message' => 'El pago de la transacción :reference ha sido verificado',
            'intro' => '¡Buenas noticias! El pago ha sido verificado por nuestro equipo.',
            'next_steps' => 'Ahora puedes programar la entrega del vehículo con el concesionario.',
        ],
        'dealer' => [
            'subject' => 'Pago verificado - Vehículo listo para recoger',
            'message' => 'Tu pago para la transacción :reference ha sido verificado',
            'intro' => 'Tu pago ha sido verificado correctamente.',
            'next_steps' => 'Por favor, programa la recogida del vehículo con el vendedor.',
        ],
    ],

    'pickup_scheduled' => [
        'subject' => 'Recogida programada para :date',
        'message' => 'La recogida del vehículo ha sido programada',
        'seller' => [
            'intro' => 'El concesionario ha programado la recogida del vehículo.',
        ],
        'dealer' => [
            'intro' => 'La recogida ha sido confirmada.',
        ],
    ],

    'pickup_proposed_subject' => 'Fechas de recogida propuestas para :vehicle',
    'pickup_proposed_message' => 'Se han propuesto nuevas fechas de recogida. Por favor, revísalas y confirma.',
    'pickup_confirmed_subject' => 'Recogida confirmada para :vehicle',
    'pickup_confirmed_message' => 'Fecha de recogida confirmada para :date',

    'handover_confirmed' => [
        'subject' => 'Entrega del vehículo confirmada',
        'message' => 'La entrega del vehículo ha sido confirmada',
        'intro' => 'El vehículo ha sido entregado correctamente.',
    ],

    'transaction_completed' => [
        'seller' => [
            'subject' => 'Transacción completada - :reference',
            'message' => '¡Tu venta de vehículo se ha completado con éxito!',
            'greeting' => '¡Felicidades!',
            'intro' => 'Tu venta de vehículo se ha completado. Los fondos serán transferidos a tu cuenta bancaria.',
            'thanks' => '¡Gracias por utilizar AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Transacción completada - :reference',
            'message' => '¡Tu compra de vehículo se ha completado con éxito!',
            'greeting' => '¡Felicidades!',
            'intro' => 'Tu compra de vehículo se ha completado.',
            'thanks' => '¡Gracias por utilizar AutoScout24 SafeTrade!',
        ],
    ],

    'transaction_cancelled' => [
        'subject' => 'Transacción cancelada - :reference',
        'message' => 'La transacción :reference ha sido cancelada',
        'intro' => 'Lamentablemente, esta transacción ha sido cancelada.',
        'reason' => 'Motivo: :reason',
    ],

    'dispute_opened' => [
        'subject' => 'Disputa abierta - Transacción :reference',
        'message' => 'Se ha abierto una disputa para la transacción :reference',
        'intro' => 'Se ha presentado una disputa para esta transacción.',
        'admin' => [
            'subject' => 'Nueva disputa requiere atención - :reference',
            'message' => 'Se ha abierto una nueva disputa que requiere revisión',
        ],
    ],

    'common' => [
        'vehicle' => 'Vehículo',
        'amount' => 'Importe',
        'reference' => 'Referencia',
        'date' => 'Fecha',
        'status' => 'Estado',
        'view_details' => 'Ver detalles',
        'contact_support' => 'Contactar soporte',
        'questions' => 'Si tienes alguna pregunta, por favor contacta con nuestro equipo de soporte.',
    ],
];
